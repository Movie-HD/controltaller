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

        // Insertar dos sucursales relacionadas con la empresa
        $sucursalLimaId = DB::table('sucursals')->insertGetId([
            'nombre' => 'Sucursal Lima',
            'direccion' => 'Av. Lima 456, Lima, Perú',
            'telefono' => '987654322',
            'empresa_id' => $empresaId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $sucursalArequipaId = DB::table('sucursals')->insertGetId([
            'nombre' => 'Sucursal Arequipa',
            'direccion' => 'Av. Arequipa 789, Arequipa, Perú',
            'telefono' => '987654323',
            'empresa_id' => $empresaId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insertar tres mecánicos relacionados con las sucursales
        $mecanico1Id = DB::table('mecanicos')->insertGetId([
            'nombre' => 'Juan Pérez',
            'empresa_id' => $empresaId,
            'sucursal_id' => $sucursalLimaId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $mecanico2Id = DB::table('mecanicos')->insertGetId([
            'nombre' => 'Carlos García',
            'empresa_id' => $empresaId,
            'sucursal_id' => $sucursalLimaId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $mecanico3Id = DB::table('mecanicos')->insertGetId([
            'nombre' => 'Ana López',
            'empresa_id' => $empresaId,
            'sucursal_id' => $sucursalArequipaId,
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
                'descripcion' => 'Cambio de aceite y revisión de frenos',
                'servicios' => 'Aceite sintético, revisión de frenos',
                'kilometraje' => 16000,
                'notas' => 'El aceite estaba muy sucio.',
                'cliente_id' => $cliente1Id,
                'vehiculo_id' => $vehiculo1Id,
                'empresa_id' => $empresaId,
                'sucursal_id' => $sucursalLimaId,
                'mecanico_id' => $mecanico1Id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'descripcion' => 'Cambio de llantas y alineación',
                'servicios' => 'Llantas nuevas, alineación',
                'kilometraje' => 31000,
                'notas' => 'Llantas desgastadas por uso excesivo.',
                'cliente_id' => $cliente1Id,
                'vehiculo_id' => $vehiculo2Id,
                'empresa_id' => $empresaId,
                'sucursal_id' => $sucursalLimaId,
                'mecanico_id' => $mecanico2Id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'descripcion' => 'Revisión de sistema de frenos',
                'servicios' => 'Revisión y ajuste de frenos',
                'kilometraje' => 21000,
                'notas' => 'Sistema de frenos en buen estado.',
                'cliente_id' => $cliente2Id,
                'vehiculo_id' => $vehiculo3Id,
                'empresa_id' => $empresaId,
                'sucursal_id' => $sucursalArequipaId,
                'mecanico_id' => $mecanico3Id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'descripcion' => 'Reemplazo de batería',
                'servicios' => 'Batería nueva, ajuste de sistema eléctrico',
                'kilometraje' => 15500,
                'notas' => 'Batería descargada.',
                'cliente_id' => $cliente1Id,
                'vehiculo_id' => $vehiculo1Id,
                'empresa_id' => $empresaId,
                'sucursal_id' => $sucursalLimaId,
                'mecanico_id' => $mecanico1Id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'descripcion' => 'Cambio de filtro de aire',
                'servicios' => 'Filtro de aire nuevo, limpieza',
                'kilometraje' => 18000,
                'notas' => 'Filtro en mal estado.',
                'cliente_id' => $cliente2Id,
                'vehiculo_id' => $vehiculo3Id,
                'empresa_id' => $empresaId,
                'sucursal_id' => $sucursalArequipaId,
                'mecanico_id' => $mecanico3Id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'descripcion' => 'Alineación de dirección',
                'servicios' => 'Alineación de dirección, revisión de suspensión',
                'kilometraje' => 25000,
                'notas' => 'Dirección desalineada.',
                'cliente_id' => $cliente1Id,
                'vehiculo_id' => $vehiculo2Id,
                'empresa_id' => $empresaId,
                'sucursal_id' => $sucursalLimaId,
                'mecanico_id' => $mecanico2Id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'descripcion' => 'Revisión completa de motor',
                'servicios' => 'Revisión y limpieza de motor',
                'kilometraje' => 19000,
                'notas' => 'Motor en buen estado, sin fallas.',
                'cliente_id' => $cliente2Id,
                'vehiculo_id' => $vehiculo3Id,
                'empresa_id' => $empresaId,
                'sucursal_id' => $sucursalArequipaId,
                'mecanico_id' => $mecanico3Id,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        $user = User::create([
            'name' => 'Administrador',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'sucursal_id' => $sucursalLimaId,
        ]);
    }
}
