<?php

namespace App\Notifications;

use App\Models\Promotion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PromotionClosedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private $promotion;

    public function __construct(Promotion $promotion)
    {
        $this->promotion = $promotion;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Promotion clôturée')
                    ->line('La promotion "' . $this->promotion->title . '" a été clôturée.')
                    ->line('Vous pouvez consulter votre relevé de notes et votre bulletin personnalisé en pièce jointe.')
                    ->attach(storage_path('app/public/promotions/' . $this->promotion->id . '/' . $notifiable->id . '.pdf'));
    }
}