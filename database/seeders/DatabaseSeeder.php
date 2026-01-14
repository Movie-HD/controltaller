<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Arrays de datos base
        $nombresPeruanos = [
            'José',
            'Luis',
            'Carlos',
            'Miguel',
            'Jorge',
            'Marco',
            'César',
            'Juan',
            'Pedro',
            'María',
            'Rosa',
            'Ana',
            'Carmen',
            'Julia',
            'Patricia',
            'Lucía',
            'Sandra',
            'Roberto',
            'Fernando',
            'Diego',
            'Alberto',
            'Alejandro',
            'Daniel',
            'Eduardo',
            'Claudia',
            'Susana',
            'Mónica',
            'Teresa',
            'Victoria',
            'Beatriz'
        ];

        $apellidosPeruanos = [
            'García',
            'Rodríguez',
            'López',
            'Flores',
            'Huamán',
            'Quispe',
            'Ramos',
            'Torres',
            'Díaz',
            'Vásquez',
            'Cruz',
            'Ruiz',
            'Mendoza',
            'Castro',
            'Mamani',
            'Gonzales',
            'Chávez',
            'Rojas',
            'Vargas',
            'Hernández',
            'Espinoza',
            'Castillo',
            'Paredes'
        ];

        $marcasModelos = [
            'Toyota' => ['Yaris', 'Corolla', 'RAV4', 'Hilux'],
            'Hyundai' => ['Accent', 'Elantra', 'Tucson', 'Santa Fe'],
            'Kia' => ['Rio', 'Sportage', 'Seltos', 'Picanto'],
            'Suzuki' => ['Swift', 'Vitara', 'S-Presso', 'Baleno'],
            'Nissan' => ['Sentra', 'Versa', 'Kicks', 'X-Trail'],
            'Mitsubishi' => ['L200', 'ASX', 'Outlander', 'Montero'],
            'Chevrolet' => ['Sail', 'Tracker', 'Onix', 'Captiva']
        ];

        $colores = ['Plata', 'Blanco', 'Negro', 'Gris', 'Rojo', 'Azul', 'Blanco Perlado'];

        $serviciosComunes = [
            'Cambio de aceite y filtros' => [80, 150],
            'Afinamiento de motor' => [200, 400],
            'Cambio de pastillas de freno' => [150, 300],
            'Cambio de amortiguadores' => [400, 800],
            'Cambio de correa de distribución' => [500, 1000],
            'Mantenimiento de transmisión' => [300, 600],
            'Cambio de embrague' => [800, 1500],
            'Reparación sistema eléctrico' => [200, 500],
            'Cambio de batería' => [350, 700],
            'Cambio de llantas' => [600, 1200],
            'Alineamiento y balanceo' => [100, 200],
            'Diagnóstico computarizado' => [80, 150],
            'Limpieza de inyectores' => [150, 300],
            'Cambio de bujías' => [100, 200],
            'Reparación de alternador' => [250, 500]
        ];

        // Insertar empresa
        $empresaId = DB::table('empresas')->insertGetId([
            'nombre' => 'AutoTaller Perú S.A.C.',
            'direccion' => 'Av. Industrial 1234, San Juan de Lurigancho',
            'telefono' => '01-3749288',
            'correo' => 'contacto@autotallerperu.com',
            'ruc' => '20505327894',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insertar mecánicos
        $mecanicosIds = [];
        $nombresMecanicos = [
            'Jorge Ramírez Torres' => 'Especialista en motor y sistemas de inyección',
            'Manuel Quispe Huamán' => 'Especialista en sistemas eléctricos',
            'Roberto Flores Mendoza' => 'Especialista en suspensión y dirección',
            'Carlos Mendoza Vásquez' => 'Especialista en transmisión automática',
            'Luis Torres Castro' => 'Especialista en diagnóstico computarizado'
        ];

        foreach ($nombresMecanicos as $nombre => $especialidad) {
            $mecanicosIds[] = DB::table('mecanicos')->insertGetId([
                'nombre' => $nombre,
                'empresa_id' => $empresaId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Insertar 40 clientes con estados distribuidos
        $clientesIds = [];
        $crmEstados = ['curioso', 'primerizo', 'recurrente', 'vip', 'frio'];
        $posicionesPorEstado = [
            'curioso' => 0,
            'primerizo' => 0,
            'recurrente' => 0,
            'vip' => 0,
            'frio' => 0
        ];

        for ($i = 0; $i < 40; $i++) {
            $nombreCliente = $nombresPeruanos[array_rand($nombresPeruanos)] . ' ' .
                $apellidosPeruanos[array_rand($apellidosPeruanos)] . ' ' .
                $apellidosPeruanos[array_rand($apellidosPeruanos)];

            $fechaCreacion = Carbon::now()->subDays(rand(1, 365));
            $estado = $crmEstados[array_rand($crmEstados)];

            $clientesIds[] = DB::table('clientes')->insertGetId([
                'nombre' => $nombreCliente,
                'telefono' => '9' . rand(10000000, 99999999),
                'email' => strtolower(str_replace(' ', '.', $nombreCliente)) . '@example.com',
                'estado' => $estado,
                'position' => $posicionesPorEstado[$estado]++,
                'origen' => rand(1, 10) > 3 ? 'directo' : 'curioso_convertido',
                'empresa_id' => $empresaId,
                'created_at' => $fechaCreacion,
                'updated_at' => $fechaCreacion,
            ]);
        }

        // Insertar 50 vehículos
        $vehiculosIds = [];
        $letras = range('A', 'Z');
        for ($i = 0; $i < 50; $i++) {
            $marca = array_rand($marcasModelos);
            $modelo = $marcasModelos[$marca][array_rand($marcasModelos[$marca])];
            $kmInicial = rand(10000, 150000);
            $fechaCreacion = Carbon::now()->subDays(rand(1, 365));

            $placa = $letras[array_rand($letras)] .
                $letras[array_rand($letras)] .
                $letras[array_rand($letras)] .
                rand(100, 999);

            $vehiculosIds[] = DB::table('vehiculos')->insertGetId([
                'placa' => $placa,
                'marca' => $marca,
                'modelo' => $modelo,
                'anio' => rand(2010, 2024),
                'color' => $colores[array_rand($colores)],
                'km_registro' => $kmInicial,
                'kilometraje' => $kmInicial,
                'cliente_id' => $clientesIds[array_rand($clientesIds)],
                'created_at' => $fechaCreacion,
                'updated_at' => $fechaCreacion,
            ]);
        }

        // Generar historial de reparaciones (ya entregadas)
        $tallerEstados = ['recepcion', 'diagnostico', 'proceso', 'espera_repuestos', 'finalizado', 'entregado'];
        $posicionesTaller = [
            'recepcion' => 0,
            'diagnostico' => 0,
            'proceso' => 0,
            'espera_repuestos' => 0,
            'finalizado' => 0,
            'entregado' => 0
        ];

        foreach ($vehiculosIds as $vehiculoId) {
            $vehiculo = DB::table('vehiculos')->where('id', $vehiculoId)->first();
            $numHistorial = rand(1, 4);

            for ($i = 0; $i < $numHistorial; $i++) {
                $servicio = array_rand($serviciosComunes);
                $precioRango = $serviciosComunes[$servicio];
                $fechaReparacion = Carbon::now()->subMonths(rand(1, 12));

                $reparacionId = DB::table('reparacions')->insertGetId([
                    'descripcion' => $servicio,
                    'servicios' => "Servicio preventivo realizado.",
                    'kilometraje' => $vehiculo->kilometraje - rand(1000, 5000),
                    'precio' => rand($precioRango[0], $precioRango[1]),
                    'estado' => 'entregado',
                    'position' => $posicionesTaller['entregado']++,
                    'cliente_id' => $vehiculo->cliente_id,
                    'vehiculo_id' => $vehiculoId,
                    'empresa_id' => $empresaId,
                    'created_at' => $fechaReparacion,
                    'updated_at' => $fechaReparacion,
                ]);

                // Asignar mecánicos
                $mecanicosAsignados = (array) array_rand(array_flip($mecanicosIds), rand(1, 2));
                foreach ($mecanicosAsignados as $mecanicoId) {
                    DB::table('mecanico_reparacion')->insert([
                        'reparacion_id' => $reparacionId,
                        'mecanico_id' => $mecanicoId,
                        'created_at' => $fechaReparacion,
                        'updated_at' => $fechaReparacion,
                    ]);
                }
            }
        }

        // Generar 15 reparaciones ACTIVAS distribuidas en el Kanban de Taller
        $activeStates = ['recepcion', 'diagnostico', 'proceso', 'espera_repuestos', 'finalizado'];
        for ($i = 0; $i < 15; $i++) {
            $vehiculoId = $vehiculosIds[array_rand($vehiculosIds)];
            $vehiculo = DB::table('vehiculos')->where('id', $vehiculoId)->first();
            $estado = $activeStates[array_rand($activeStates)];

            $servicio = array_rand($serviciosComunes);
            $precioRango = $serviciosComunes[$servicio];
            $fecha = Carbon::now()->subHours(rand(1, 72));

            $reparacionId = DB::table('reparacions')->insertGetId([
                'descripcion' => $servicio,
                'servicios' => "Trabajo actual solicitado.",
                'kilometraje' => $vehiculo->kilometraje,
                'precio' => rand($precioRango[0], $precioRango[1]),
                'estado' => $estado,
                'position' => $posicionesTaller[$estado]++,
                'cliente_id' => $vehiculo->cliente_id,
                'vehiculo_id' => $vehiculoId,
                'empresa_id' => $empresaId,
                'created_at' => $fecha,
                'updated_at' => $fecha,
            ]);

            $mecanicosAsignados = (array) array_rand(array_flip($mecanicosIds), rand(1, 2));
            foreach ($mecanicosAsignados as $mecanicoId) {
                DB::table('mecanico_reparacion')->insert([
                    'reparacion_id' => $reparacionId,
                    'mecanico_id' => $mecanicoId,
                    'created_at' => $fecha,
                    'updated_at' => $fecha,
                ]);
            }
        }

        // Actualizar métricas de clientes basadas en las reparaciones insertadas
        $clientes = DB::table('clientes')->get();
        foreach ($clientes as $cliente) {
            $compras = DB::table('reparacions')
                ->where('cliente_id', $cliente->id)
                ->where('estado', 'entregado')
                ->get();

            if ($compras->count() > 0) {
                $totalMonto = $compras->sum('precio');
                $fechaUltima = Carbon::parse($compras->max('created_at'));
                $diasSinComprar = now()->diffInDays($fechaUltima);

                DB::table('clientes')->where('id', $cliente->id)->update([
                    'total_compras' => $compras->count(),
                    'ingreso_total_generado' => $totalMonto,
                    'ticket_promedio' => $totalMonto / $compras->count(),
                    'fecha_ultima_compra' => $fechaUltima,
                    'dias_sin_comprar' => $diasSinComprar,
                    'compras_ultimo_mes' => $compras->where('created_at', '>=', now()->subMonth())->count(),
                ]);
            }
        }

        // Crear usuario administrador
        User::create([
            'name' => 'Administrador',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);
    }
}
