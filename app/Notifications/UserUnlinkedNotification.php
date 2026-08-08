<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserUnlinkedNotification extends Notification implements ShouldQueue
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
            ->subject('Vinculación revocada - SGEI')
            ->greeting('¡Hola, ' . $notifiable->nombre . '!')
            ->line('Tu cuenta de usuario ha sido desvinculada de tu registro en el padrón.')
            ->line("**Persona desvinculada:** {$this->personaApellido}, {$this->personaNombre}")
            ->line('Tu estado ha sido actualizado. Si crees que esto es un error, por favor comunícate con la administración.')
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
