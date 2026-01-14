<?php
// database/migrations/xxxx_create_clientes_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();

            // INFORMACIÓN BÁSICA
            $table->string('nombre');
            $table->string('telefono')->unique();
            $table->string('email')->nullable();
            $table->foreignId('empresa_id')->constrained('empresas');

            // CLASIFICACIÓN - Para Kanban
            $table->enum('estado', ['curioso', 'primerizo', 'recurrente', 'vip', 'frio'])
                ->default('curioso');
            $table->enum('origen', ['directo', 'curioso_convertido'])->default('directo');

            // POSICIÓN PARA KANBAN (Flowforge)
            $table->flowforgePositionColumn('position');

            // MÉTRICAS
            $table->integer('total_compras')->default(0);
            $table->integer('compras_ultimo_mes')->default(0);
            $table->decimal('ticket_promedio', 10, 2)->default(0);
            $table->decimal('ingreso_total_generado', 10, 2)->default(0);

            // RIESGO
            $table->enum('etiqueta_riesgo', ['ninguno', 'churn_risk_x1', 'churn_risk_x2'])
                ->default('ninguno');
            $table->integer('dias_sin_comprar')->default(0);

            // FECHAS
            $table->date('fecha_primera_visita')->nullable();
            $table->date('fecha_primera_compra')->nullable();
            $table->date('fecha_ultima_compra')->nullable();
            $table->timestamp('fecha_ultimo_contacto')->nullable();

            // NOTAS
            $table->text('notas')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
