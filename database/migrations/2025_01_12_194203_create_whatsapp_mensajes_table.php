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
        Schema::create('whatsapp_mensajes', function (Blueprint $table) {
            $table->id();
            $table->text('mensaje')->nullable(); // Mensaje enviado
            $table->dateTime('fecha_programada')->nullable(); // Fecha de envío programado
            $table->string('estado')->default('pendiente'); // pendiente, enviado, cancelado
            $table->foreignId('cliente_id')->constrained('clientes'); // Relación con cliente
            $table->foreignId('vehiculo_id')->nullable()->constrained('vehiculos'); // Opcional: Vehículo relacionado
            $table->foreignId('reparacion_id')->nullable()->constrained('reparacions'); // Opcional: Reparación relacionada
            $table->foreignId('plantilla_id')->constrained('whatsapp_plantillas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_mensajes');
    }
};
