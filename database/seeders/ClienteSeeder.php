<?php

namespace Database\Seeders;

use App\Models\Cliente;
use Illuminate\Database\Seeder;

class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        $clientes = [
            ['nombre' => 'María González', 'telefono' => '987123456', 'estado' => 'recurrente', 'origen' => 'curioso', 'total_compras' => 3, 'ingreso_total' => 120, 'dias_sin_comprar' => 22],
            ['nombre' => 'Juan Pérez', 'telefono' => '987234567', 'estado' => 'primerizo', 'origen' => 'directo', 'total_compras' => 1, 'ingreso_total' => 35, 'dias_sin_comprar' => 0],
            ['nombre' => 'Carlos Ramos', 'telefono' => '987345678', 'estado' => 'vip', 'origen' => 'directo', 'total_compras' => 12, 'ingreso_total' => 450, 'dias_sin_comprar' => 5],
            ['nombre' => 'Luis Díaz', 'telefono' => '987456789', 'estado' => 'curioso', 'origen' => 'curioso', 'total_compras' => 0, 'ingreso_total' => 0, 'dias_sin_comprar' => 2],
        ];

        foreach ($clientes as $data) {
            Cliente::create([
                ...$data,
                'fecha_primera_visita' => now()->subDays(rand(30, 90)),
                'fecha_ultima_compra' => $data['total_compras'] > 0 ? now()->subDays($data['dias_sin_comprar']) : null,
            ]);
        }
    }
}
