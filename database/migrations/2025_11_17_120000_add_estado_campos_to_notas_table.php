<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('notas')) return; // evitar error si tabla no existe aún
        Schema::table('notas', function (Blueprint $table) {
            if (!Schema::hasColumn('notas','estado')) {
                $table->string('estado',20)->default('borrador')->after('periodo');
            }
            if (!Schema::hasColumn('notas','publicado_at')) {
                $table->timestamp('publicado_at')->nullable()->after('estado');
            }
            if (!Schema::hasColumn('notas','revisado_at')) {
                $table->timestamp('revisado_at')->nullable()->after('publicado_at');
            }
            if (!Schema::hasColumn('notas','bloqueado')) {
                $table->boolean('bloqueado')->default(false)->after('revisado_at');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('notas')) return;
        Schema::table('notas', function (Blueprint $table) {
            if (Schema::hasColumn('notas','bloqueado')) $table->dropColumn('bloqueado');
            if (Schema::hasColumn('notas','revisado_at')) $table->dropColumn('revisado_at');
            if (Schema::hasColumn('notas','publicado_at')) $table->dropColumn('publicado_at');
            if (Schema::hasColumn('notas','estado')) $table->dropColumn('estado');
        });
    }
};
