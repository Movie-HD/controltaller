<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Insertar una empresa directamente
        $empresaId = DB::table('empresas')->insertGetId([
            'nombre' => 'Mi Empresa S.A.',
            'direccion' => 'Av. Ejemplo 123, Lima, Perú',
            'telefono' => '987654321',
            'correo' => 'contacto@miempresa.com',
            'ruc' => '12345678901',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insertar tres mecánicos
        $mecanico1Id = DB::table('mecanicos')->insertGetId([
            'nombre' => 'Juan Pérez',
            'empresa_id' => $empresaId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $mecanico2Id = DB::table('mecanicos')->insertGetId([
            'nombre' => 'Carlos García',
            'empresa_id' => $empresaId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $mecanico3Id = DB::table('mecanicos')->insertGetId([
            'nombre' => 'Ana López',
            'empresa_id' => $empresaId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insertar dos clientes relacionados con la empresa
        $cliente1Id = DB::table('clientes')->insertGetId([
            'nombre' => 'Pedro González',
            'telefono' => '998877665',
            'empresa_id' => $empresaId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $cliente2Id = DB::table('clientes')->insertGetId([
            'nombre' => 'María Rodríguez',
            'telefono' => '977665544',
            'empresa_id' => $empresaId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insertar tres vehículos relacionados con los clientes
        $vehiculo1Id = DB::table('vehiculos')->insertGetId([
            'placa' => 'ABC123',
            'marca' => 'Toyota',
            'modelo' => 'Corolla',
            'anio' => 2020,
            'color' => 'Rojo',
            'kilometraje' => 15000,
            'cliente_id' => $cliente1Id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $vehiculo2Id = DB::table('vehiculos')->insertGetId([
            'placa' => 'XYZ456',
            'marca' => 'Honda',
            'modelo' => 'Civic',
            'anio' => 2018,
            'color' => 'Negro',
            'kilometraje' => 30000,
            'cliente_id' => $cliente1Id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $vehiculo3Id = DB::table('vehiculos')->insertGetId([
            'placa' => 'LMN789',
            'marca' => 'Ford',
            'modelo' => 'Focus',
            'anio' => 2019,
            'color' => 'Azul',
            'kilometraje' => 20000,
            'cliente_id' => $cliente2Id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insertar 7 reparaciones relacionadas con los vehículos, clientes, mecánicos, etc.
        DB::table('reparacions')->insert([
            [
                'descripcion' => 'Cambio de aceite y filtro',
                'servicios' => 'Aceite sintético y filtro nuevo',
                'kilometraje' => 15000,
                'precio' => 120,
                'notas' => 'Todo se realizó correctamente, no hubo observaciones adicionales.',
                'cliente_id' => $cliente1Id,
                'vehiculo_id' => $vehiculo1Id,
                'empresa_id' => $empresaId,
                'mecanico_id' => $mecanico1Id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'descripcion' => 'Revisión y ajuste de frenos',
                'servicios' => 'Limpieza y ajuste de frenos traseros',
                'kilometraje' => 20000,
                'precio' => 90,
                'notas' => 'Se recomendó reemplazar las pastillas de freno delanteras, pero el cliente decidió hacerlo en una visita futura.',
                'cliente_id' => $cliente1Id,
                'vehiculo_id' => $vehiculo2Id,
                'empresa_id' => $empresaId,
                'mecanico_id' => $mecanico2Id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'descripcion' => 'Cambio de filtro de aire',
                'servicios' => 'Filtro de aire nuevo',
                'kilometraje' => 22000,
                'precio' => 50,
                'notas' => 'Se recomendó limpieza del sistema de admisión, pero el cliente optó por no realizarlo en este momento.',
                'cliente_id' => $cliente1Id,
                'vehiculo_id' => $vehiculo1Id,
                'empresa_id' => $empresaId,
                'mecanico_id' => $mecanico3Id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'descripcion' => 'Reemplazo de batería',
                'servicios' => 'Instalación de batería nueva',
                'kilometraje' => 32000,
                'precio' => 480,
                'notas' => 'Todo en orden, no hubo observaciones adicionales.',
                'cliente_id' => $cliente2Id,
                'vehiculo_id' => $vehiculo3Id,
                'empresa_id' => $empresaId,
                'mecanico_id' => $mecanico1Id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'descripcion' => 'Cambio de llantas',
                'servicios' => 'Llantas nuevas y balanceo',
                'kilometraje' => 40000,
                'precio' => 1500,
                'notas' => 'Se recomendó revisar alineación, pero el cliente decidió postergar la revisión.',
                'cliente_id' => $cliente2Id,
                'vehiculo_id' => $vehiculo2Id,
                'empresa_id' => $empresaId,
                'mecanico_id' => $mecanico2Id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'descripcion' => 'Diagnóstico de ruido en suspensión',
                'servicios' => 'Inspección y ajuste',
                'kilometraje' => 35000,
                'precio' => 180,
                'notas' => 'Se identificó desgaste en amortiguadores traseros. El cliente prefirió no reemplazarlos por ahora.',
                'cliente_id' => $cliente1Id,
                'vehiculo_id' => $vehiculo1Id,
                'empresa_id' => $empresaId,
                'mecanico_id' => $mecanico1Id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'descripcion' => 'Revisión completa de motor',
                'servicios' => 'Inspección y limpieza de motor',
                'kilometraje' => 50000,
                'precio' => 350,
                'notas' => 'Todo se realizó correctamente, sin observaciones adicionales.',
                'cliente_id' => $cliente1Id,
                'vehiculo_id' => $vehiculo3Id,
                'empresa_id' => $empresaId,
                'mecanico_id' => $mecanico2Id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'descripcion' => 'Revisión del sistema eléctrico',
                'servicios' => 'Pruebas de alternador y batería',
                'kilometraje' => 18000,
                'precio' => 200,
                'notas' => 'Se detectó una conexión suelta en el alternador, se corrigió durante el servicio.',
                'cliente_id' => $cliente1Id,
                'vehiculo_id' => $vehiculo3Id,
                'empresa_id' => $empresaId,
                'mecanico_id' => $mecanico3Id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'descripcion' => 'Cambio de líquido de frenos',
                'servicios' => 'Drenado y llenado con líquido nuevo',
                'kilometraje' => 25000,
                'precio' => 120,
                'notas' => 'Todo se realizó correctamente, sin observaciones adicionales.',
                'cliente_id' => $cliente2Id,
                'vehiculo_id' => $vehiculo1Id,
                'empresa_id' => $empresaId,
                'mecanico_id' => $mecanico1Id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'descripcion' => 'Revisión de sistema de escape',
                'servicios' => 'Inspección y ajuste',
                'kilometraje' => 60000,
                'precio' => 300,
                'notas' => 'Todo en orden, sin recomendaciones adicionales.',
                'cliente_id' => $cliente2Id,
                'vehiculo_id' => $vehiculo3Id,
                'empresa_id' => $empresaId,
                'mecanico_id' => $mecanico2Id,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        $user = User::create([
            'name' => 'Administrador',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);
    }
}
