<?php

use App\Http\Middleware\ComingSoon;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\UseRealClientIp;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Récital : la vraie adresse IP du visiteur, transmise par le proxy de l'hébergeur.
        // Placé en premier pour que toute la suite (limites anti-robot, sessions) la voie.
        $middleware->prepend(UseRealClientIp::class);

        // Récital : derrière le proxy de l'hébergeur, le site sait qu'il est en HTTPS.
        // Seul l'en-tête X-Forwarded-Proto est cru, et seulement si TRUSTED_PROXIES est rempli (config/trustedproxy.php).
        $middleware->trustProxies(headers: Request::HEADER_X_FORWARDED_PROTO);

        // Récital : en-têtes de sécurité sur toutes les réponses, pages d'erreur comprises.
        $middleware->append(SecurityHeaders::class);

        // Récital : mode « Bientôt disponible » (config/recital.php). Placé après les en-têtes de sécurité,
        // pour que la page « Bientôt disponible » les reçoive aussi.
        $middleware->append(ComingSoon::class);

        // Le cookie du lien secret est lu avant le déchiffrement des cookies : il n'est donc pas chiffré.
        // Sa valeur est une empreinte signée avec la clé de l'application (voir ComingSoon).
        $middleware->encryptCookies(except: [ComingSoon::COOKIE]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
