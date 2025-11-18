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
        Schema::create('orientacion_citas_historial', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cita_id')->constrained('orientacion_citas')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('estado_from',30)->nullable();
            $table->string('estado_to',30);
            $table->string('descripcion',255)->nullable();
            $table->timestamps();
            $table->index(['cita_id','estado_to']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orientacion_citas_historial');
    }
};
