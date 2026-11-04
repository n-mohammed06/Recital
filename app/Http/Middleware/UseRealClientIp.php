<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Derrière le proxy d'alwaysdata, toutes les visites sembleraient venir de la même adresse IP,
 * celle du proxy. Les limites anti-robot par connexion bloqueraient alors tout le monde à la fois.
 * Le proxy transmet la vraie adresse dans un en-tête (X-Real-IP) : on la remet à sa place.
 *
 * Actif seulement si REAL_IP_HEADER est rempli dans le .env (voir config/trustedproxy.php).
 */
class UseRealClientIp
{
    public function handle(Request $request, Closure $next): Response
    {
        $header = config('trustedproxy.real_ip_header');

        if ($header) {
            $ip = trim((string) $request->headers->get($header));

            if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
                $request->server->set('REMOTE_ADDR', $ip);
            }
        }

        return $next($request);
    }
}
