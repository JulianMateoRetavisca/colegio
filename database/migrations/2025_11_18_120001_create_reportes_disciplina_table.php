<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if(!Schema::hasTable('reportes_disciplina')){
            Schema::create('reportes_disciplina', function (Blueprint $table) {
                $table->id();
                $table->foreignId('estudiante_id')->constrained('users');
                $table->foreignId('docente_id')->constrained('users');
                $table->foreignId('coordinador_id')->nullable()->constrained('users');
                $table->string('estado'); // reportado, en_revision, sancion_asignada, notificado, apelacion_solicitada, apelacion_en_revision, apelacion_aceptada, apelacion_rechazada, archivado
                $table->text('descripcion_incidente');
                $table->string('gravedad')->nullable(); // leve|moderada|grave
                $table->text('sancion_text')->nullable();
                $table->boolean('sancion_activa')->default(false);
                $table->timestamp('sancion_asignada_at')->nullable();
                $table->timestamp('notificado_at')->nullable();
                $table->text('apelacion_motivo')->nullable();
                $table->string('apelacion_result')->nullable(); // aceptada|rechazada
                $table->timestamp('apelacion_resuelta_at')->nullable();
                $table->timestamp('sancion_modificada_at')->nullable();
                $table->timestamp('sancion_eliminada_at')->nullable();
                $table->timestamp('archivado_at')->nullable();
                $table->timestamps();
                $table->index('estado');
                $table->index('estudiante_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('reportes_disciplina');
    }
};
