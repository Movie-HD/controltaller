<?php
// database/migrations/xxxx_create_compras_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();

            // DATOS DE COMPRA
            $table->decimal('monto', 10, 2);
            $table->json('productos')->nullable();
            $table->enum('metodo_pago', ['efectivo', 'tarjeta', 'transferencia', 'yape', 'plin'])
                  ->nullable();

            // CONTEXTO
            $table->foreignId('vendedor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notas')->nullable();

            // CLASIFICACIÓN
            $table->enum('estado_cliente_en_compra', ['primerizo', 'recurrente', 'vip'])
                  ->nullable();

            $table->timestamp('fecha_compra')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compras');
    }
};
