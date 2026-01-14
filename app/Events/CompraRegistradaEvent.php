<?php
// app/Events/CompraRegistradaEvent.php

namespace App\Events;

use App\Models\Cliente;
use App\Models\Compra;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CompraRegistradaEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Compra $compra,
        public Cliente $cliente,
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
        return 'compra.registrada';
    }

    public function broadcastWith(): array
    {
        return [
            'compra_id' => $this->compra->id,
            'cliente_id' => $this->cliente->id,
            'cliente_nombre' => $this->cliente->nombre,
            'monto' => $this->compra->monto,
            'metodo_pago' => $this->compra->metodo_pago,
            'usuario' => $this->userName,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
