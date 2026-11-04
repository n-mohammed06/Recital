<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Anti-robot invisible : un élève qui se trompe de mot de passe n'attend jamais.
     * Seules les rafales impossibles pour un humain sont freinées :
     * - plus de 20 essais ratés en 5 minutes sur un même compte (robot qui devine un mot de passe) ;
     * - plus de 100 essais ratés en 1 minute depuis une même connexion (robot qui essaie des milliers de comptes).
     *   Une classe entière sur le même Wi-Fi reste très loin de ce chiffre.
     *
     * @var array<string, array{int, int}> type => [essais ratés autorisés, durée en secondes]
     */
    private const LIMITS = [
        'compte' => [20, 300],
        'connexion' => [100, 60],
    ];

    /**
     * Affiche le formulaire de connexion.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Vérifie l'adresse et le mot de passe, puis ouvre la session.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->merge(['email' => Str::lower((string) $request->input('email'))]);

        $credentials = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Indique ton adresse e-mail.',
            'email.email' => 'Cette adresse e-mail ne semble pas valide. Vérifie-la.',
            'email.max' => 'Cette adresse e-mail est trop longue.',
            'password.required' => 'Indique ton mot de passe.',
        ]);

        // L'adresse est transformée en empreinte : le cache ne garde aucune adresse en clair.
        $keys = [
            'compte' => 'login:compte:'.sha1($credentials['email']),
            'connexion' => 'login:connexion:'.$request->ip(),
        ];

        foreach ($keys as $type => $key) {
            if (RateLimiter::tooManyAttempts($key, self::LIMITS[$type][0])) {
                throw ValidationException::withMessages([
                    'login' => 'Trop d’essais en très peu de temps. Réessaie dans '.$this->duration(RateLimiter::availableIn($key)).'.',
                ]);
            }
        }

        // Auth::attempt répond toujours en au moins 200 ms : la durée ne trahit pas si le compte existe.
        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            foreach ($keys as $type => $key) {
                RateLimiter::hit($key, self::LIMITS[$type][1]);
            }

            // Message volontairement vague : on ne dit pas si c'est l'adresse ou le mot de passe qui est faux.
            throw ValidationException::withMessages([
                'login' => 'Adresse e-mail ou mot de passe incorrect.',
            ]);
        }

        RateLimiter::clear($keys['compte']);

        // Nouvel identifiant de session à la connexion (contre le vol de session).
        $request->session()->regenerate();

        return redirect()->intended(route('home'));
    }

    /**
     * Ferme la session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('status', 'Déconnexion réussie. À bientôt.');
    }

    /**
     * « 45 secondes », « 3 minutes ».
     */
    private function duration(int $seconds): string
    {
        if ($seconds >= 60) {
            $minutes = (int) ceil($seconds / 60);

            return $minutes.' '.($minutes > 1 ? 'minutes' : 'minute');
        }

        return $seconds.' '.($seconds > 1 ? 'secondes' : 'seconde');
    }
}
