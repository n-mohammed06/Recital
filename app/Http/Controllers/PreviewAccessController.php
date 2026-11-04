<?php

namespace App\Http\Controllers;

use App\Http\Middleware\ComingSoon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Lien secret de l'aperçu privé : https://ton-adresse/apercu/{code}
 *
 * Pendant le mode « Bientôt disponible », il ouvre tout le site sur l'appareil qui l'ouvre,
 * pendant 90 jours. À ouvrir une fois sur chaque appareil (téléphone, ordinateur…).
 */
class PreviewAccessController extends Controller
{
    private const DAYS = 90;

    public function __invoke(string $code): RedirectResponse
    {
        $expected = (string) config('recital.preview_code');

        // hash_equals compare en un temps constant : la durée de la réponse ne révèle rien du bon code.
        if ($expected === '' || ! hash_equals($expected, $code)) {
            abort(404);
        }

        return redirect()->route(Auth::check() ? 'home' : 'login')
            ->with('status', 'Aperçu privé ouvert sur cet appareil pour '.self::DAYS.' jours.')
            ->withCookie(cookie(ComingSoon::COOKIE, ComingSoon::cookieValue(), self::DAYS * 24 * 60));
    }
}
