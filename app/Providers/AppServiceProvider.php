<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Contracts\Validation\UncompromisedVerifier;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\NotPwnedVerifier;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Vérification des mots de passe piratés (Have I Been Pwned) : si le service ne répond pas
        // en 5 secondes, l'inscription continue au lieu d'attendre 30 secondes.
        $this->app->extend(
            UncompromisedVerifier::class,
            fn ($verifier, $app) => new NotPwnedVerifier($app->make(HttpFactory::class), 5),
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Règles des mots de passe, communes à l'inscription et à « Mot de passe oublié » :
        // 8 caractères minimum, une lettre, un chiffre, et pas un mot de passe déjà paru dans une fuite.
        // Seuls les 5 premiers caractères d'une empreinte du mot de passe sont envoyés au service :
        // ni le mot de passe ni l'adresse e-mail ne quittent le site.
        Password::defaults(fn () => Password::min(8)->max(64)->letters()->numbers()->uncompromised());

        // Anti-robot invisible : des limites larges qu'un élève n'atteint jamais,
        // même toute une classe inscrite en même temps sur le même Wi-Fi.
        RateLimiter::for('inscription', fn (Request $request) => Limit::perMinute(60)->by($request->ip()));
        RateLimiter::for('mot-de-passe', fn (Request $request) => Limit::perMinute(20)->by($request->ip()));

        // Changer son mot de passe depuis le profil : 10 essais par minute et par élève,
        // pour qu'un téléphone resté ouvert ne permette pas de deviner le mot de passe actuel.
        RateLimiter::for('changer-mot-de-passe', fn (Request $request) => Limit::perMinute(10)
            ->by($request->user()?->getAuthIdentifier() ?? $request->ip()));

        // Lien secret de l'aperçu privé : impossible de tester des milliers de codes.
        RateLimiter::for('apercu', fn (Request $request) => Limit::perMinute(10)->by($request->ip()));
    }
}
