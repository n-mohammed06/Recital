<?php

namespace App\Support;

use App\Models\User;

/**
 * Les progrès d'un élève, affichés dans son profil.
 *
 * Les leçons n'existent pas encore : tout part de zéro pour l'instant.
 * Quand elles arriveront, ces chiffres viendront des tables prévues dans le plan
 * (lesson_progress, practice_sessions) : seule la méthode for() changera, pas les pages.
 */
final readonly class ProgressSummary
{
    public function __construct(
        public string $level = 'Découverte',
        public int $streakDays = 0,
        public int $lessonsCompleted = 0,
        public int $stars = 0,
        public int $practiceMinutes = 0,
    ) {}

    public static function for(User $user): self
    {
        return new self;
    }
}
