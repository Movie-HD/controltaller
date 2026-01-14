<?php

namespace App\Events;

use App\Models\Reparacion;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReparacionActualizadaEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Reparacion $reparacion,
        public string $accion // creada, actualizada, eliminada
    ) {
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('kanban-reparaciones'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'reparacion.actualizada';
    }

    public function broadcastWith(): array
    {
        return [
            'reparacion_id' => $this->reparacion->id,
            'placa' => $this->reparacion->vehiculo?->placa ?? 'N/A',
            'accion' => $this->accion,
            'usuario' => auth()->user()?->name ?? 'Sistema',
        ];
    }
}
