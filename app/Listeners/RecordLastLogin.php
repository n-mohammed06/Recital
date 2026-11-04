<?php

namespace App\Listeners;

use App\Models\User;
use Illuminate\Auth\Events\Login;

/**
 * Note la date de chaque connexion : à l'inscription, avec le formulaire de connexion,
 * ou automatiquement grâce à « Se souvenir de moi ».
 *
 * La politique de confidentialité promet de supprimer un compte inutilisé pendant 2 ans,
 * après un e-mail d'avertissement : cette date permet de savoir lesquels (recital:clean-accounts).
 * Se connecter annule l'avertissement.
 *
 * Laravel trouve ce fichier tout seul (dossier app/Listeners), rien à déclarer ailleurs.
 */
class RecordLastLogin
{
    public function handle(Login $event): void
    {
        if ($event->user instanceof User) {
            $event->user->forceFill([
                'last_login_at' => now(),
                'inactivity_warned_at' => null,
            ])->saveQuietly();
        }
    }
}
