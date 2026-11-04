<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\InactiveAccountWarning;
use DateTimeInterface;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * Ménage quotidien promis par la politique de confidentialité :
 * 1. un compte dont l'adresse n'a jamais été confirmée est supprimé au bout de 7 jours ;
 * 2. un compte inutilisé depuis 23 mois reçoit un e-mail d'avertissement ;
 * 3. un mois après cet e-mail, s'il n'y a toujours eu aucune connexion, le compte est supprimé.
 *
 * Lancée chaque nuit (routes/console.php), ou à la main : php artisan recital:clean-accounts
 */
class CleanAccounts extends Command
{
    protected $signature = 'recital:clean-accounts';

    protected $description = 'Supprime les comptes jamais confirmés et ceux inutilisés depuis 2 ans (après un e-mail d’avertissement).';

    public function handle(): int
    {
        $now = now();

        // 1. Adresse jamais confirmée au bout de 7 jours.
        $unconfirmed = User::whereNull('email_verified_at')
            ->where('created_at', '<=', $now->copy()->subDays(7))
            ->delete();

        // 2. Avertissement, un mois avant la suppression. Par paquets de 100 pour ne pas saturer la mémoire.
        $warned = 0;
        $deletionDate = $now->copy()->addMonth();

        User::whereNotNull('email_verified_at')
            ->whereNull('inactivity_warned_at')
            ->where(fn (Builder $query) => $this->inactiveSince($query, $now->copy()->subMonths(23)))
            ->chunkById(100, function (Collection $users) use (&$warned, $now, $deletionDate) {
                foreach ($users as $user) {
                    $user->notify(new InactiveAccountWarning($deletionDate));
                    $user->forceFill(['inactivity_warned_at' => $now])->saveQuietly();
                    $warned++;
                }
            });

        // 3. Prévenu depuis au moins un mois, et toujours aucune connexion depuis 2 ans.
        //    (Se connecter remet l'avertissement à zéro : voir App\Listeners\RecordLastLogin.)
        $inactive = User::whereNotNull('inactivity_warned_at')
            ->where('inactivity_warned_at', '<=', $now->copy()->subMonth())
            ->where(fn (Builder $query) => $this->inactiveSince($query, $now->copy()->subYears(2)))
            ->delete();

        $this->info("Comptes jamais confirmés supprimés : {$unconfirmed}");
        $this->info("Élèves prévenus d’une suppression prochaine : {$warned}");
        $this->info("Comptes inactifs supprimés : {$inactive}");

        return self::SUCCESS;
    }

    /**
     * Aucune connexion depuis la date donnée (ou jamais connecté et inscrit avant cette date).
     */
    private function inactiveSince(Builder $query, DateTimeInterface $date): void
    {
        $query->where('last_login_at', '<=', $date)
            ->orWhere(fn (Builder $query) => $query->whereNull('last_login_at')->where('created_at', '<=', $date));
    }
}
