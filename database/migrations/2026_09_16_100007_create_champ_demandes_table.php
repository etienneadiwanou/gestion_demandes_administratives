<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Champs dynamiques associés à un type de demande (formulaires
     * configurables : texte, date, heure, nombre, liste, textarea...).
     */
    public function up(): void
    {
        Schema::create('champ_demandes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('type_demande_id')->constrained('type_demandes')->cascadeOnDelete();
            $table->string('label');
            $table->string('nom_technique');
            $table->enum('type_champ', [
                'texte',
                'nombre',
                'date',
                'heure',
                'liste',
                'textarea',
                'fichier',
                'booleen',
            ]);
            $table->boolean('obligatoire')->default(false);
            $table->json('options')->nullable();
            $table->unsignedInteger('ordre')->default(0);
            $table->timestamps();

            $table->unique(['type_demande_id', 'nom_technique']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('champ_demandes');
    }
};
