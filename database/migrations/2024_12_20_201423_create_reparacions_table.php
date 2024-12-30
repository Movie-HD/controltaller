<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reparacions', function (Blueprint $table) {
            $table->id();
            $table->text('descripcion');
            $table->text('servicios');
            $table->integer('kilometraje');
            $table->text('notas')->nullable();
            $table->integer('precio')->nullable();
            $table->foreignId('cliente_id')->constrained('clientes');
            $table->foreignId('vehiculo_id')->constrained('vehiculos');
            $table->foreignId('empresa_id')->constrained('empresas');
            $table->foreignId('mecanico_id')->constrained('mecanicos');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reparacions');
    }
};
