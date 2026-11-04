<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Les sessions ouvertes par un élève sur ses différents appareils.
 */
class UserSessions
{
    /**
     * Ferme les sessions de l'élève (téléphone perdu, quelqu'un d'autre…), sauf éventuellement
     * celle de l'appareil utilisé en ce moment.
     *
     * Possible seulement quand les sessions sont dans la base de données (SESSION_DRIVER=database,
     * le réglage par défaut de Laravel).
     */
    public static function close(User $user, ?string $exceptSessionId = null): void
    {
        if (config('session.driver') !== 'database') {
            return;
        }

        DB::connection(config('session.connection'))
            ->table(config('session.table', 'sessions'))
            ->where('user_id', $user->getKey())
            ->when($exceptSessionId, fn ($query) => $query->where('id', '!=', $exceptSessionId))
            ->delete();
    }
}
