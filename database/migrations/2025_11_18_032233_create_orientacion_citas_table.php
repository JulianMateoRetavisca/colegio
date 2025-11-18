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
        if (!Schema::hasTable('orientacion_citas')) {
            Schema::create('orientacion_citas', function (Blueprint $table) {
                $table->id();
                $table->foreignId('estudiante_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('orientador_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('estado',30)->index();
                $table->date('fecha_solicitada')->nullable();
                $table->date('fecha_asignada')->nullable();
                $table->time('hora_asignada')->nullable();
                $table->string('motivo',180)->nullable();
                $table->text('observaciones')->nullable();
                $table->boolean('seguimiento_requerido')->default(false);
                $table->date('fecha_proxima')->nullable();
                $table->timestamp('cerrada_at')->nullable();
                $table->timestamps();
                $table->index(['estudiante_id','estado']);
            });
        } else {
            // Migración correctiva: añadir columnas que falten si ya existía la tabla vacía
            Schema::table('orientacion_citas', function (Blueprint $table) {
                if(!Schema::hasColumn('orientacion_citas','estudiante_id')){
                    $table->foreignId('estudiante_id')->after('id')->constrained('users')->cascadeOnDelete();
                }
                if(!Schema::hasColumn('orientacion_citas','orientador_id')){
                    $table->foreignId('orientador_id')->nullable()->after('estudiante_id')->constrained('users')->nullOnDelete();
                }
                if(!Schema::hasColumn('orientacion_citas','estado')){
                    $table->string('estado',30)->after('orientador_id')->index();
                }
                if(!Schema::hasColumn('orientacion_citas','fecha_solicitada')){
                    $table->date('fecha_solicitada')->nullable()->after('estado');
                }
                if(!Schema::hasColumn('orientacion_citas','fecha_asignada')){
                    $table->date('fecha_asignada')->nullable()->after('fecha_solicitada');
                }
                if(!Schema::hasColumn('orientacion_citas','hora_asignada')){
                    $table->time('hora_asignada')->nullable()->after('fecha_asignada');
                }
                if(!Schema::hasColumn('orientacion_citas','motivo')){
                    $table->string('motivo',180)->nullable()->after('hora_asignada');
                }
                if(!Schema::hasColumn('orientacion_citas','observaciones')){
                    $table->text('observaciones')->nullable()->after('motivo');
                }
                if(!Schema::hasColumn('orientacion_citas','seguimiento_requerido')){
                    $table->boolean('seguimiento_requerido')->default(false)->after('observaciones');
                }
                if(!Schema::hasColumn('orientacion_citas','fecha_proxima')){
                    $table->date('fecha_proxima')->nullable()->after('seguimiento_requerido');
                }
                if(!Schema::hasColumn('orientacion_citas','cerrada_at')){
                    $table->timestamp('cerrada_at')->nullable()->after('fecha_proxima');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orientacion_citas');
    }
};
