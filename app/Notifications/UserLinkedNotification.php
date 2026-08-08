<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserLinkedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $personaNombre;
    protected string $personaApellido;

    public function __construct(string $personaNombre, string $personaApellido)
    {
        $this->personaNombre = $personaNombre;
        $this->personaApellido = $personaApellido;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Vinculación confirmada - SGEI')
            ->greeting('¡Hola, ' . $notifiable->nombre . '!')
            ->line('Tu cuenta de usuario ha sido vinculada exitosamente con tu registro en el padrón.')
            ->line("**Persona vinculada:** {$this->personaApellido}, {$this->personaNombre}")
            ->line('Ya puedes acceder al sistema con tu cuenta. Tu estado ha sido actualizado a "activo".')
            ->salutation('Atentamente, El equipo de SGEI');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'persona_nombre' => $this->personaNombre,
            'persona_apellido' => $this->personaApellido,
        ];
    }
}
