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
        Schema::table('reparacions', function (Blueprint $table) {
            $table->string('estado')->default('recepcion')->after('precio');
            $table->integer('position')->default(0)->after('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reparacions', function (Blueprint $table) {
            $table->dropColumn(['estado', 'position']);
        });
    }
};
