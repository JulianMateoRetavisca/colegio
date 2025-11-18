<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if(!Schema::hasTable('reportes_disciplina_historial')){
            Schema::create('reportes_disciplina_historial', function (Blueprint $table) {
                $table->id();
                $table->foreignId('reporte_id')->constrained('reportes_disciplina')->onDelete('cascade');
                $table->foreignId('user_id')->nullable()->constrained('users');
                $table->string('estado_from')->nullable();
                $table->string('estado_to');
                $table->text('descripcion')->nullable();
                $table->timestamps();
                $table->index(['reporte_id','estado_to']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('reportes_disciplina_historial');
    }
};
