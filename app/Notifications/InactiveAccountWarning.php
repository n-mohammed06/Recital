<?php

namespace App\Notifications;

use DateTimeInterface;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Prévient un élève, un mois avant, que son compte inutilisé depuis presque 2 ans va être supprimé.
 */
class InactiveAccountWarning extends Notification
{
    public function __construct(public DateTimeInterface $deletionDate) {}

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
            ->subject('Ton compte Récital va être supprimé')
            ->greeting('Bonjour '.$notifiable->first_name.',')
            ->line('Ton compte Récital n’a pas servi depuis presque deux ans.')
            ->line('Comme prévu par notre politique de confidentialité, il sera supprimé avec tes données le '.$this->deletionDate->format('d/m/Y').'.')
            ->action('Me connecter pour garder mon compte', route('login'))
            ->line('Si tu ne veux plus utiliser Récital, tu n’as rien à faire.');
    }
}
