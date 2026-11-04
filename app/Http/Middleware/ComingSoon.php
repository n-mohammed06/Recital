<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Mode « Bientôt disponible » : RECITAL_COMING_SOON=true dans le .env du serveur (config/recital.php).
 *
 * Le public ne voit que la page « Bientôt disponible » et les pages légales : toute autre adresse
 * ramène à l'accueil. Le lien secret /apercu/{code} dépose un cookie qui ouvre tout le site
 * sur cet appareil (PreviewAccessController).
 *
 * Middleware global, placé avant les sessions et la connexion : il ne dépend ni de la base de données
 * ni d'une session, et une visite du public ne crée aucun cookie.
 */
class ComingSoon
{
    /**
     * Cookie déposé par le lien secret. Il n'est pas chiffré par Laravel (bootstrap/app.php) :
     * ce middleware le lit avant que les cookies soient déchiffrés. Sa valeur est une empreinte
     * signée avec la clé de l'application : impossible à fabriquer sans la connaître.
     */
    public const COOKIE = 'recital_apercu';

    /**
     * Adresses ouvertes à tous, même pendant le mode « Bientôt disponible » :
     * les pages légales (obligatoires), le lien secret et la vérification de santé du serveur.
     */
    private const ALWAYS_OPEN = ['conditions-utilisation', 'confidentialite', 'apercu/*', 'up'];

    public function handle(Request $request, Closure $next): Response
    {
        if (! config('recital.coming_soon') || $request->is(...self::ALWAYS_OPEN) || self::hasAccess($request)) {
            return $next($request);
        }

        if ($request->is('/')) {
            return response()->view('coming-soon');
        }

        return redirect('/');
    }

    /**
     * Valeur du cookie d'accès : une empreinte du code secret. Changer le code
     * (RECITAL_PREVIEW_CODE) ferme l'accès à tous ceux qui avaient l'ancien lien.
     * null quand aucun code n'est réglé : personne ne peut entrer.
     */
    public static function cookieValue(): ?string
    {
        $code = (string) config('recital.preview_code');

        return $code === '' ? null : hash_hmac('sha256', $code, (string) config('app.key'));
    }

    /**
     * L'appareil a-t-il ouvert le lien secret ?
     */
    public static function hasAccess(Request $request): bool
    {
        $expected = self::cookieValue();
        $given = $request->cookies->get(self::COOKIE);

        return $expected !== null && is_string($given) && hash_equals($expected, $given);
    }
}
