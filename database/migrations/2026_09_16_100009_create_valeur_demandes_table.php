<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Valeur saisie par l'utilisateur pour un champ dynamique d'une
     * demande donnée.
     */
    public function up(): void
    {
        Schema::create('valeur_demandes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('demande_id')->constrained('demandes')->cascadeOnDelete();
            $table->foreignId('champ_demande_id')->constrained('champ_demandes')->cascadeOnDelete();
            $table->text('valeur')->nullable();
            $table->timestamps();

            $table->unique(['demande_id', 'champ_demande_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('valeur_demandes');
    }
};
