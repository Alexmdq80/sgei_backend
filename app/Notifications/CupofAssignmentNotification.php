<?php

namespace App\Notifications;

use App\Models\Cupof;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CupofAssignmentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Cupof $cupof;
    protected string $situacionRevista;
    protected string $personaNombre;

    /**
     * Create a new notification instance.
     */
    public function __construct(Cupof $cupof, string $situacionRevista, string $personaNombre)
    {
        $this->cupof = $cupof;
        $this->situacionRevista = $situacionRevista;
        $this->personaNombre = $personaNombre;
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
        $cargo = $this->cupof->nombre_cargo ?? 'Docente';
        $escuela = $this->cupof->escuela->nombre;
        $asignatura = $this->cupof->asignatura?->nombre;
        
        $message = (new MailMessage)
            ->subject('Asignación de puesto - SGEI')
            ->greeting('¡Hola, ' . $this->personaNombre . '!')
            ->line('Te informamos que has sido asignado a un nuevo puesto en el Sistema de Gestión Escolar Integral (SGEI).')
            ->line('**Detalles del puesto:**')
            ->line('- **Cargo:** ' . $cargo)
            ->line('- **Escuela:** ' . $escuela);

        if ($asignatura) {
            $message->line('- **Asignatura:** ' . $asignatura);
        }

        $message->line('- **Situación de Revista:** ' . $this->situacionRevista)
            ->line('Puedes acceder al sistema para ver más detalles.')
            ->salutation('Atentamente, El equipo de SGEI');

        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'cupof_id' => $this->cupof->id,
            'cargo' => $this->cupof->nombre_cargo,
            'escuela' => $this->cupof->escuela->nombre,
        ];
    }
}
