<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountInvitationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $token;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $frontendUrls = explode(',', env('FRONTEND_URL', 'http://localhost:5173'));
        $frontendUrl = trim($frontendUrls[0]);
        // Redirigimos a una nueva ruta de activación en el frontend
        $setupUrl = $frontendUrl . '/activar-cuenta?token=' . $this->token . '&email=' . urlencode($notifiable->email);

        return (new MailMessage)
            ->subject('Activación de cuenta - SGEI')
            ->greeting('¡Hola, ' . $notifiable->nombre . '!')
            ->line('Se ha creado una cuenta para ti en el Sistema de Gestión Escolar Integral (SGEI).')
            ->line('Para comenzar a utilizar el sistema, es necesario que actives tu cuenta configurando una contraseña.')
            ->action('Activar mi Cuenta', $setupUrl)
            ->line('Este enlace de activación expirará en 24 horas.')
            ->line('Si no esperabas esta invitación, puedes ignorar este correo.')
            ->salutation('Atentamente, El equipo de SGEI');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'token' => $this->token,
        ];
    }
}
