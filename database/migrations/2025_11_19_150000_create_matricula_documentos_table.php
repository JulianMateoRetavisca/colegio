<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('matricula_documentos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('matricula_id');
            $table->unsignedBigInteger('uploaded_by');
            $table->string('tipo', 50)->nullable(); // ej: certificado, cedula, foto
            $table->string('original_name');
            $table->string('stored_name');
            $table->string('mime_type', 100)->nullable();
            $table->unsignedBigInteger('size_bytes');
            $table->timestamps();

            $table->foreign('matricula_id')->references('id')->on('matriculas')->onDelete('cascade');
            $table->foreign('uploaded_by')->references('id')->on('users')->onDelete('cascade');
            $table->index(['matricula_id','tipo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matricula_documentos');
    }
};
