<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Historique des affectations d'un dossier à un ou plusieurs
     * agents au cours de son cycle de vie.
     */
    public function up(): void
    {
        Schema::create('affectations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('demande_id')->constrained('demandes')->cascadeOnDelete();
            $table->foreignId('agent_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('affecte_par_id')->constrained('users')->cascadeOnDelete();
            $table->enum('statut', ['active', 'terminee'])->default('active');
            $table->text('commentaire')->nullable();
            $table->timestamp('date_affectation')->useCurrent();
            $table->timestamp('date_fin')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affectations');
    }
};
