<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * E-mail de sécurité envoyé quand un élève change son mot de passe depuis son profil.
 */
class PasswordChanged extends Notification
{
    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Ton mot de passe Récital a été changé')
            ->greeting('Bonjour '.$notifiable->first_name.',')
            ->line('Le mot de passe de ton compte Récital vient d’être changé. Tes autres appareils ont été déconnectés.')
            ->line('Si c’est toi, tu n’as rien à faire.')
            ->line('Si ce n’est pas toi, choisis tout de suite un nouveau mot de passe : quelqu’un connaît peut-être l’ancien.')
            ->action('Choisir un nouveau mot de passe', route('password.request'));
    }
}
