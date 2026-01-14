<?php

namespace App\Events;

use App\Models\Reparacion;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReparacionMovidaEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Reparacion $reparacion,
        public string $estado_anterior,
        public string $estado_nuevo
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
        return 'reparacion.movida';
    }

    public function broadcastWith(): array
    {
        return [
            'reparacion_id' => $this->reparacion->id,
            'placa' => $this->reparacion->vehiculo->placa,
            'estado_anterior' => $this->estado_anterior,
            'estado_nuevo' => $this->estado_nuevo,
            'usuario' => auth()->user()?->name ?? 'Sistema',
        ];
    }
}
