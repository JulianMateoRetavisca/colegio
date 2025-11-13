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
        Schema::create('grupo_materia', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('grupo_id');
            $table->unsignedBigInteger('materia_id');
            $table->timestamps();

            // Evitar duplicados
            $table->unique(['grupo_id', 'materia_id']);
            
            // Opcional: claves foráneas (descomenta si las tablas grupos y materias ya existen)
            // $table->foreign('grupo_id')->references('id')->on('grupos')->onDelete('cascade');
            // $table->foreign('materia_id')->references('id')->on('materias')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grupo_materia');
    }
};
