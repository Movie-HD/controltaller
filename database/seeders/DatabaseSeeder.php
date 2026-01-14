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

        // Insertar 30 clientes
        $clientesIds = [];
        for ($i = 0; $i < 30; $i++) {
            $nombreCliente = $nombresPeruanos[array_rand($nombresPeruanos)] . ' ' .
                $apellidosPeruanos[array_rand($apellidosPeruanos)] . ' ' .
                $apellidosPeruanos[array_rand($apellidosPeruanos)];

            $fechaCreacion = Carbon::now()->subDays(rand(1, 365));

            $clientesIds[] = DB::table('clientes')->insertGetId([
                'nombre' => $nombreCliente,
                'telefono' => '9' . rand(10000000, 99999999),
                'empresa_id' => $empresaId,
                'created_at' => $fechaCreacion,
                'updated_at' => $fechaCreacion,
            ]);
        }

        // Insertar 45 vehículos
        $vehiculosIds = [];
        $letras = range('A', 'Z');
        for ($i = 0; $i < 45; $i++) {
            $marca = array_rand($marcasModelos);
            $modelo = $marcasModelos[$marca][array_rand($marcasModelos[$marca])];
            $kmInicial = rand(10000, 50000);
            $fechaCreacion = Carbon::now()->subDays(rand(1, 365));

            // Generar placa peruana válida
            $placa = $letras[array_rand($letras)] .
                $letras[array_rand($letras)] .
                $letras[array_rand($letras)] .
                rand(100, 999);

            $vehiculosIds[] = DB::table('vehiculos')->insertGetId([
                'placa' => $placa,
                'marca' => $marca,
                'modelo' => $modelo,
                'anio' => rand(2015, 2023),
                'color' => $colores[array_rand($colores)],
                'km_registro' => $kmInicial,
                'kilometraje' => $kmInicial,
                'cliente_id' => $clientesIds[array_rand($clientesIds)],
                'created_at' => $fechaCreacion,
                'updated_at' => $fechaCreacion,
            ]);
        }

        // Generar reparaciones para cada vehículo
        foreach ($vehiculosIds as $vehiculoId) {
            $vehiculo = DB::table('vehiculos')->where('id', $vehiculoId)->first();
            $kmActual = $vehiculo->km_registro;

            // Generar entre 7 y 12 reparaciones por vehículo
            $numReparaciones = rand(7, 12);

            for ($i = 0; $i < $numReparaciones; $i++) {
                $servicio = array_rand($serviciosComunes);
                $precioRango = $serviciosComunes[$servicio];
                $kmIncremento = rand(3000, 8000);
                $kmActual += $kmIncremento;

                // Generar fecha coherente
                $fechaReparacion = Carbon::now()->subDays(rand(1, 365));

                // Generar notas técnicas coherentes
                $notasBase = [
                    "Se realizó el servicio según especificaciones del fabricante.",
                    "Cliente reportó {problema}. Se solucionó con {solucion}.",
                    "Mantenimiento preventivo completado. Se recomienda próxima revisión a los {km} km.",
                    "Se detectaron desgastes normales para el kilometraje.",
                    "Trabajo realizado con repuestos originales.",
                ];

                $problemas = ["ruido en suspensión", "pérdida de potencia", "consumo excesivo de combustible", "vibración"];
                $soluciones = ["ajuste y calibración", "reemplazo de componentes", "limpieza del sistema", "actualización de software"];

                $nota = $notasBase[array_rand($notasBase)];
                $nota = str_replace(
                    ['{problema}', '{solucion}', '{km}'],
                    [$problemas[array_rand($problemas)], $soluciones[array_rand($soluciones)], $kmActual + 5000],
                    $nota
                );

                // Generar oportunidades aleatorias (50% de probabilidad)
                $oportunidadesData = [];
                if (rand(1, 10) <= 5) {
                    $posiblesOportunidades = [
                        'Cambio de llantas',
                        'Alineamiento y balanceo',
                        'Chequeo de frenos',
                        'Cambio de batería',
                        'Limpieza de inyectores',
                        'Recarga de aire acondicionado'
                    ];
                    $numOps = rand(1, 2);
                    for ($j = 0; $j < $numOps; $j++) {
                        $oportunidadesData[] = [
                            'servicio' => $posiblesOportunidades[array_rand($posiblesOportunidades)],
                            'fecha' => Carbon::now()->addDays(rand(15, 90))->toDateString(),
                        ];
                    }
                }

                $reparacionId = DB::table('reparacions')->insertGetId([
                    'descripcion' => $servicio,
                    'servicios' => "Incluye: " . implode(", ", array_map(function () {
                        return ["mano de obra", "repuestos originales", "limpieza", "diagnóstico"][rand(0, 3)];
                    }, range(1, 3))),
                    'kilometraje' => $kmActual,
                    'precio' => rand($precioRango[0], $precioRango[1]),
                    'notas' => $nota,
                    'oportunidades' => !empty($oportunidadesData) ? json_encode($oportunidadesData) : null,
                    'cliente_id' => $vehiculo->cliente_id,
                    'vehiculo_id' => $vehiculoId,
                    'empresa_id' => $empresaId,
                    'created_at' => $fechaReparacion,
                    'updated_at' => $fechaReparacion,
                ]);

                // Insertar en tabla pivot (asignar 1 o 2 mecánicos aleatorios)
                $mecanicosAsignados = (array) array_rand(array_flip($mecanicosIds), rand(1, 2));
                foreach ($mecanicosAsignados as $mecanicoId) {
                    DB::table('mecanico_reparacion')->insert([
                        'reparacion_id' => $reparacionId,
                        'mecanico_id' => $mecanicoId,
                        'created_at' => $fechaReparacion,
                        'updated_at' => $fechaReparacion,
                    ]);
                }

                // Actualizar kilometraje del vehículo
                DB::table('vehiculos')
                    ->where('id', $vehiculoId)
                    ->update(['kilometraje' => $kmActual]);
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
