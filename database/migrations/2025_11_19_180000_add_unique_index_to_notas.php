<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

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

        // Detectar si el índice único existe
        $indexes = collect(DB::select("SHOW INDEX FROM notas"))->pluck('Key_name');
        if ($indexes->contains('notas_estudiante_materia_periodo_unique')) {
            // Asegurar índice simple para la FK estudiante_id antes de eliminar el único si la FK lo está usando
            $singleEstIndexMissing = !$indexes->contains('notas_estudiante_id_index');
            if ($singleEstIndexMissing) {
                Schema::table('notas', function (Blueprint $table) {
                    $table->index('estudiante_id', 'notas_estudiante_id_index');
                });
            }
            // Intentar eliminar el índice único de forma segura
            try {
                DB::statement('ALTER TABLE notas DROP INDEX notas_estudiante_materia_periodo_unique');
            } catch (\Throwable $e) {
                // Si no se puede eliminar (FK depende), se deja para evitar fallo del rollback
            }
        }
    }
};
