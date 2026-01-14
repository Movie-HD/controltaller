<?php
// app/Events/ClienteMovidoEvent.php

namespace App\Events;

use App\Models\Cliente;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ClienteMovidoEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Cliente $cliente,
        public string $estadoAnterior,
        public string $estadoNuevo,
        public ?string $userName = null
    ) {
        $this->userName = $userName ?? auth()->user()?->name ?? 'Sistema';
    }

    public function broadcastOn(): Channel
    {
        return new Channel('kanban-clientes');
    }

    public function broadcastAs(): string
    {
        return 'cliente.movido';
    }

    public function broadcastWith(): array
    {
        return [
            'cliente_id' => $this->cliente->id,
            'cliente_nombre' => $this->cliente->nombre,
            'estado_anterior' => $this->estadoAnterior,
            'estado_nuevo' => $this->estadoNuevo,
            'usuario' => $this->userName,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
