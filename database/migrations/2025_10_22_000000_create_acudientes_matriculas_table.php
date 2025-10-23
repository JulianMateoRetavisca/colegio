<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('matriculas_acudientes')) {
        Schema::create('matriculas_acudientes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // acudiente
            $table->unsignedBigInteger('estudiante_id')->nullable();
            $table->unsignedBigInteger('curso_id');
            $table->string('nombres');
            $table->string('apellidos');
            $table->string('documento_identidad');
            $table->string('email')->nullable();
            $table->string('telefono')->nullable();
            $table->json('documentos')->nullable(); // array con rutas en FTP
            $table->enum('estado', ['pendiente','completada','rechazada'])->default('pendiente');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('curso_id')->references('id')->on('cursos')->onDelete('cascade');
        });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('matriculas_acudientes');
    }
};
