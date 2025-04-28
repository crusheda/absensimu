<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Whatsapp\WhatsappMessage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class test extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ["whatsapp"];
    }

    public function toWhatsapp($notifiable)
    {
        // Ambil konfigurasi dari services.php
        $baseUrl = config('whatsapp-notification-channel.whatsapp-bot-api.base_uri');
        $session = config('whatsapp-notification-channel.whatsapp-bot-api.whatsappSession');
        $sessionFieldName = config('whatsapp-notification-channel.whatsapp-bot-api.whatsappSessionFieldName');
        $sendMessageEndpoint = config('whatsapp-notification-channel.whatsapp-bot-api.mapMethods.sendMessage');

        // Menyusun payload untuk request API
        $response = Http::post($baseUrl . '/' . $sendMessageEndpoint, [
            'session' => $session,
            'sessionFieldName' => $sessionFieldName,
            'to' => '+6281232545545',  // Ganti dengan nomor WhatsApp yang benar
            'message' => 'Hello World! This is a test notification via WhatsApp.'
        ]);

        // Mengecek apakah pesan berhasil terkirim
        if ($response->successful()) {
            Log::info('WhatsApp Message Sent Successfully');
            return 'Message sent successfully';
        } else {
            Log::error('WhatsApp API Error: ' . $response->body());
            return 'Failed to send message';
        }
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
