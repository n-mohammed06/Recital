<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

/**
 * Un élève de Récital.
 *
 * MustVerifyEmail : l'adresse doit être confirmée avec le lien reçu par e-mail
 * avant d'accéder aux pages réservées (middleware « verified »).
 */
#[Fillable(['first_name', 'last_name', 'email', 'password', 'note_names', 'daily_goal'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Réglage « Noms des notes » : valeur enregistrée => texte affiché.
     */
    public const NOTE_NAMES = [
        'do-re-mi' => 'Do Ré Mi',
        'c-d-e' => 'C D E',
    ];

    /**
     * Réglage « Objectif du jour », en minutes de pratique.
     */
    public const DAILY_GOALS = [5, 10, 15];

    /**
     * Réglages d'un nouvel élève (les mêmes valeurs par défaut que dans la base de données).
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'note_names' => 'do-re-mi',
        'daily_goal' => 10,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'terms_accepted_at' => 'datetime',
            'last_login_at' => 'datetime',
            'inactivity_warned_at' => 'datetime',
            'daily_goal' => 'integer',
            // Le mot de passe est toujours haché avant d'être enregistré (bcrypt).
            'password' => 'hashed',
        ];
    }

    /**
     * Prénom et nom ensemble : « Zoé Martin ».
     */
    public function fullName(): string
    {
        return $this->first_name.' '.$this->last_name;
    }

    /**
     * Initiales affichées dans le rond du profil : « ZM ».
     */
    public function initials(): string
    {
        return Str::upper(Str::substr($this->first_name, 0, 1).Str::substr($this->last_name, 0, 1));
    }

    /**
     * Mois d'inscription, en français : « novembre 2026 ».
     */
    public function memberSince(): string
    {
        return $this->created_at->locale('fr')->translatedFormat('F Y');
    }
}
