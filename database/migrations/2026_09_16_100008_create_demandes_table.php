<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Dossier administratif principal. Le statut porte le cycle de vie
     * défini dans le cahier des charges :
     * brouillon -> soumise -> en_verification -> complement_demande
     * -> en_validation -> approuvee / rejetee -> archivee.
     */
    public function up(): void
    {
        Schema::create('demandes', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('type_demande_id')->constrained('type_demandes')->restrictOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->enum('statut', [
                'brouillon',
                'soumise',
                'en_verification',
                'complement_demande',
                'en_validation',
                'approuvee',
                'rejetee',
                'archivee',
            ])->default('brouillon');
            $table->enum('priorite', ['basse', 'normale', 'haute', 'urgente'])->default('normale');
            $table->text('commentaire')->nullable();
            $table->timestamp('date_soumission')->nullable();
            $table->timestamp('date_cloture')->nullable();
            $table->timestamps();

            $table->index('statut');
            $table->index('priorite');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demandes');
    }
};
