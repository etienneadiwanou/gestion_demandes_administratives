<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Complète la table users par défaut de Laravel avec les
     * informations métier AdminFlow : téléphone, rôle, département,
     * statut du compte.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('telephone')->nullable()->after('email');
            $table->foreignId('role_id')->nullable()->after('telephone')
                ->constrained('roles')->nullOnDelete();
            $table->foreignId('department_id')->nullable()->after('role_id')
                ->constrained('departments')->nullOnDelete();
            $table->boolean('actif')->default(true)->after('department_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('role_id');
            $table->dropConstrainedForeignId('department_id');
            $table->dropColumn(['telephone', 'actif']);
        });
    }
};
