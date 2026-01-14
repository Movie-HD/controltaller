<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mecanico_reparacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mecanico_id')->constrained('mecanicos')->onDelete('cascade');
            $table->foreignId('reparacion_id')->constrained('reparacions')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mecanico_reparacion');
    }
};
