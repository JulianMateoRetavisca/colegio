<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('notas')) return;
        Schema::table('notas', function (Blueprint $table) {
            // Evitar error si ya existe
            $indexes = collect(DB::select("SHOW INDEX FROM notas"))->pluck('Key_name');
            if (!$indexes->contains('notas_estudiante_materia_periodo_unique')) {
                $table->unique(['estudiante_id','materia_id','periodo'], 'notas_estudiante_materia_periodo_unique');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('notas')) return;
        Schema::table('notas', function (Blueprint $table) {
            try { $table->dropUnique('notas_estudiante_materia_periodo_unique'); } catch (\Throwable $e) {}
        });
    }
};
