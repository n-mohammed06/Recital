<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * En-têtes de sécurité envoyés avec chaque page.
 */
class SecurityHeaders
{
    /**
     * Le navigateur ne charge que des fichiers venant de Récital lui-même :
     * aucun script injecté ni ressource d'un autre site ne peut s'exécuter.
     */
    private const CONTENT_SECURITY_POLICY = "default-src 'self'; script-src 'self'; style-src 'self'; "
        ."img-src 'self' data:; font-src 'self'; connect-src 'self'; form-action 'self'; "
        ."frame-ancestors 'none'; base-uri 'none'; object-src 'none'";

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $headers = [
            // Le navigateur ne devine pas le type d'un fichier.
            'X-Content-Type-Options' => 'nosniff',
            // L'adresse des pages (et les liens de réinitialisation) n'est jamais transmise à un autre site.
            'Referrer-Policy' => 'same-origin',
            // Récital ne peut pas être affiché dans le cadre d'un autre site (vol de clics).
            'X-Frame-Options' => 'DENY',
            'Cross-Origin-Opener-Policy' => 'same-origin',
            // Fonctions du téléphone dont le site n'a pas besoin. Le micro et le MIDI restent possibles pour plus tard.
            'Permissions-Policy' => 'camera=(), geolocation=(), payment=(), usb=()',
        ];

        // En développement, la page d'erreur détaillée de Laravel a besoin de scripts intégrés :
        // la règle est seulement signalée dans la console du navigateur, sans rien bloquer.
        $headers[config('app.debug') ? 'Content-Security-Policy-Report-Only' : 'Content-Security-Policy'] = self::CONTENT_SECURITY_POLICY;

        // Une fois venu en HTTPS, le navigateur n'utilise plus que le HTTPS pendant un an.
        if ($request->isSecure()) {
            $headers['Strict-Transport-Security'] = 'max-age=31536000';
        }

        foreach ($headers as $name => $value) {
            if (! $response->headers->has($name)) {
                $response->headers->set($name, $value);
            }
        }

        return $response;
    }
}
