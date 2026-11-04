<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Confirmation de l'adresse e-mail : prouve que l'adresse donnée à l'inscription est bien celle de l'élève.
 * Tant qu'elle n'est pas confirmée, les pages du site renvoient ici (middleware « verified »).
 */
class EmailVerificationController extends Controller
{
    /**
     * Page « Vérifie ta boîte mail ».
     */
    public function notice(Request $request): RedirectResponse|View
    {
        return $request->user()->hasVerifiedEmail()
            ? redirect()->intended(route('home'))
            : view('auth.verify-email');
    }

    /**
     * Lien reçu par e-mail. Laravel vérifie la signature du lien, sa date de validité (60 minutes)
     * et qu'il correspond bien au compte connecté (EmailVerificationRequest).
     */
    public function verify(EmailVerificationRequest $request): RedirectResponse
    {
        $request->fulfill();

        return redirect()->intended(route('home'))
            ->with('status', 'Ton adresse est confirmée. Bienvenue sur Récital, '.$request->user()->first_name.'.');
    }

    /**
     * Bouton « Renvoyer l'e-mail ».
     */
    public function send(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('home'));
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'Un nouveau lien vient de partir. Pense à regarder dans les courriers indésirables.');
    }
}
