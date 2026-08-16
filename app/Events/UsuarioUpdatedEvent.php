<?php

declare(strict_types=1);

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UsuarioUpdatedEvent implements ShouldBroadcastNow
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public string $action,
        public ?string $userId
    ) {
    }

    /**
     * Get the channels that the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // 🔒 Canal privado: Requiere autenticación y autorización
        return [new PrivateChannel('usuarios')];
    }

    /**
     * Nombre personalizado del evento (opcional, pero recomendado).
     */
    public function broadcastAs(): string
    {
        return 'UsuarioUpdated';
    }

    /**
     * Datos que se enviarán en el payload del websocket.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'action' => $this->action,
            'userId' => $this->userId,
        ];
    }
}