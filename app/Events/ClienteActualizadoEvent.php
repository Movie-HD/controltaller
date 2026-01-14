<?php
// app/Events/ClienteActualizadoEvent.php

namespace App\Events;

use App\Models\Cliente;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ClienteActualizadoEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Cliente $cliente,
        public string $accion, // 'creado', 'actualizado', 'eliminado'
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
        return 'cliente.actualizado';
    }

    public function broadcastWith(): array
    {
        return [
            'cliente_id' => $this->cliente->id,
            'cliente' => [
                'id' => $this->cliente->id,
                'nombre' => $this->cliente->nombre,
                'telefono' => $this->cliente->telefono,
                'estado' => $this->cliente->estado,
                'origen' => $this->cliente->origen,
                'total_compras' => $this->cliente->total_compras,
                'ingreso_total_generado' => $this->cliente->ingreso_total_generado,
                'etiqueta_riesgo' => $this->cliente->etiqueta_riesgo,
            ],
            'accion' => $this->accion,
            'usuario' => $this->userName,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
