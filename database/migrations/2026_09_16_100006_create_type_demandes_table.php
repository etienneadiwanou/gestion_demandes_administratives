<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Types de demandes configurables (Congé annuel, Attestation de
     * travail, ...). L'administrateur peut en créer sans modifier la
     * structure de la base.
     */
    public function up(): void
    {
        Schema::create('type_demandes', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('code')->unique();
            $table->string('categorie')->nullable();
            $table->text('description')->nullable();
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('type_demandes');
    }
};
