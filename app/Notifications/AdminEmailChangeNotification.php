<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminEmailChangeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $token;
    protected string $oldEmail;

    public function __construct(string $token, string $oldEmail)
    {
        $this->token = $token;
        $this->oldEmail = $oldEmail;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $frontendUrls = explode(',', env('FRONTEND_URL', 'http://localhost:5173'));
        $frontendUrl = trim($frontendUrls[0]);
        $verificationUrl = $frontendUrl . '/verificar-email?token=' . $this->token . '&email=' . urlencode($notifiable->email);

        return (new MailMessage)
            ->subject('Cambio de correo electrónico - SGEI')
            ->greeting('¡Hola, ' . $notifiable->nombre . '!')
            ->line('Una administración del Sistema de Gestión Escolar Integral (SGEI) ha modificado la dirección de correo electrónico asociada a tu cuenta.')
            ->line("Tu correo anterior ({$this->oldEmail}) ha sido reemplazado por {$notifiable->email}.")
            ->line('Para continuar utilizando el sistema con tu nueva dirección de correo, es necesario que la verifiques haciendo clic en el botón de abajo.')
            ->action('Verificar Nuevo Correo', $verificationUrl)
            ->line('Si tienes alguna duda o no realizaste este cambio, por favor comunícate con la administración.')
            ->salutation('Atentamente, El equipo de SGEI');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'token' => $this->token,
            'old_email' => $this->oldEmail,
        ];
    }
}
