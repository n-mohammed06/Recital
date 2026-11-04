<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Notifications\PasswordChanged;
use App\Support\UserSessions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Throwable;

/**
 * « Changer de mot de passe », depuis le profil d'un élève connecté.
 */
class PasswordController extends Controller
{
    private const MESSAGES = [
        'current_password.required' => 'Indique ton mot de passe actuel.',
        'current_password.current_password' => 'Ce n’est pas ton mot de passe actuel.',
        'password.required' => 'Choisis un nouveau mot de passe.',
        'password.different' => 'Ton nouveau mot de passe doit être différent de l’ancien.',
    ];

    /**
     * Formulaire « Changer de mot de passe ».
     */
    public function edit(Request $request): View
    {
        return view('profile.password', ['user' => $request->user()]);
    }

    /**
     * Vérifie le mot de passe actuel, enregistre le nouveau et déconnecte les autres appareils.
     */
    public function update(Request $request): RedirectResponse
    {
        // Mot de passe actuel obligatoire : quelqu'un qui trouve un téléphone resté connecté
        // ne peut pas s'approprier le compte. Mêmes règles qu'à l'inscription pour le nouveau.
        $data = $request->validate([
            'current_password' => ['required', 'string', 'current_password'],
            'password' => ['required', Password::defaults(), 'different:current_password'],
        ], self::MESSAGES + RegisterController::PASSWORD_MESSAGES);

        $user = $request->user();
        // L'élève avait-il coché « Se souvenir de moi » sur cet appareil ?
        $remember = $request->hasCookie(Auth::guard()->getRecallerName());

        $user->password = Hash::make($data['password']);
        // Les appareils restés connectés avec « Se souvenir de moi » sont déconnectés.
        $user->setRememberToken(Str::random(60));
        $user->save();

        // Cet appareil reste connecté : nouvel identifiant de session (l'ancien est détruit)
        // et, si besoin, nouveau cookie « Se souvenir de moi ».
        Auth::login($user, $remember);

        // Les sessions ouvertes sur les autres appareils sont fermées.
        UserSessions::close($user, $request->session()->getId());

        // E-mail de sécurité : si ce n'est pas l'élève qui a changé le mot de passe, il le saura tout de suite.
        try {
            $user->notify(new PasswordChanged);
        } catch (Throwable $e) {
            report($e);
        }

        return redirect()->route('profile')
            ->with('status', 'Ton mot de passe a été changé. Tes autres appareils ont été déconnectés.');
    }
}
