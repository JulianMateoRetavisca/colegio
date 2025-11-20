<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orientacion_cita_historials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cita_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('estado_from')->nullable();
            $table->string('estado_to');
            $table->text('descripcion')->nullable();
            $table->timestamps();

            $table->foreign('cita_id')->references('id')->on('orientacion_citas')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->index(['cita_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orientacion_cita_historials');
    }
};
