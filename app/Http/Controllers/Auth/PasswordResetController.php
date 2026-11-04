<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\UserSessions;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

/**
 * « Mot de passe oublié » : un lien valable 60 minutes est envoyé par e-mail.
 * Le jeton du lien est enregistré haché, comme un mot de passe.
 */
class PasswordResetController extends Controller
{
    /**
     * Même message que le compte existe ou non : la page ne révèle pas qui est inscrit.
     */
    private const LINK_SENT = 'Si un compte existe avec cette adresse, un lien pour choisir un nouveau mot de passe vient de partir. Il est valable 60 minutes.';

    private const EMAIL_MESSAGES = [
        'email.required' => 'Indique ton adresse e-mail.',
        'email.email' => 'Cette adresse e-mail ne semble pas valide. Vérifie-la.',
        'email.max' => 'Cette adresse e-mail est trop longue.',
    ];

    /**
     * Formulaire « Mot de passe oublié ».
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Envoie le lien par e-mail.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->merge(['email' => Str::lower((string) $request->input('email'))]);

        $request->validate(['email' => ['required', 'string', 'email', 'max:255']], self::EMAIL_MESSAGES);

        // Un lien au plus par minute et par compte : Laravel s'en charge (config/auth.php, « throttle »).
        try {
            Password::sendResetLink($request->only('email'));
        } catch (Throwable $e) {
            report($e);

            throw ValidationException::withMessages([
                'email' => 'L’e-mail n’a pas pu partir. Réessaie dans quelques minutes.',
            ]);
        }

        return back()->with('status', self::LINK_SENT);
    }

    /**
     * Formulaire « Nouveau mot de passe », ouvert depuis le lien reçu.
     */
    public function edit(Request $request, string $token): View
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => Str::lower((string) $request->query('email')),
        ]);
    }

    /**
     * Enregistre le nouveau mot de passe.
     */
    public function update(Request $request): RedirectResponse
    {
        $request->merge(['email' => Str::lower((string) $request->input('email'))]);

        $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', PasswordRule::defaults()],
        ], self::EMAIL_MESSAGES + RegisterController::PASSWORD_MESSAGES);

        $status = Password::reset(
            $request->only('email', 'password', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    // Les téléphones restés connectés avec « Se souvenir de moi » sont déconnectés.
                    'remember_token' => Str::random(60),
                ]);

                // Le lien est arrivé dans cette boîte mail : l'adresse est donc bien celle de l'élève.
                if (! $user->hasVerifiedEmail()) {
                    $user->email_verified_at = now();
                }

                $user->save();

                // Ferme aussi les sessions ouvertes ailleurs (téléphone perdu, quelqu'un d'autre…).
                UserSessions::close($user);

                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'email' => 'Ce lien n’est plus valable, ou il ne correspond pas à cette adresse. Demande un nouveau lien.',
            ]);
        }

        return redirect()->route('login')
            ->with('status', 'Ton mot de passe a été changé. Tu peux te connecter.');
    }
}
