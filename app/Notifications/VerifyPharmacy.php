<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyPharmacy extends Notification
{
    use Queueable;
    private $pathOfPhoto ;
    private $pharmacyName ;
    private $number ;
    /**
     * Create a new notification instance.
     */
    public function __construct($pathOfPhoto,$pharmacyName,$number)
    {
        $this->pathOfPhoto = $pathOfPhoto ;
        $this->pharmacyName = $pharmacyName ;
        $this->number = $number ;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)->subject('Pharmacy Verification')
                    ->line('Please verify this pharmacy ')
                    ->line('pharmacy name : ' . $this->pharmacyName)
                    ->line('pharmacy number : ' . $this->number)
                    ->attach(public_path('/pharmaciesPhotos/'.$this->pathOfPhoto))
                    ->line('Thank you for working with us!') ;

    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
