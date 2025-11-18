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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'grupo_id')) {
                $table->unsignedBigInteger('grupo_id')->nullable()->after('roles_id');
                $table->foreign('grupo_id')->references('id')->on('grupos')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'grupo_id')) {
                $table->dropForeign(['grupo_id']);
                $table->dropColumn('grupo_id');
            }
        });
    }
};
