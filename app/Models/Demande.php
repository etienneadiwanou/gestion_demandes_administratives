<?php

namespace App\Models;

use App\Enums\StatutDemande;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Demande extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'user_id',
        'type_demande_id',
        'department_id',
        'statut',
        'priorite',
        'commentaire',
        'date_soumission',
        'date_cloture',
    ];

    protected function casts(): array
    {
        return [
            'statut' => StatutDemande::class,
            'date_soumission' => 'datetime',
            'date_cloture' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function typeDemande(): BelongsTo
    {
        return $this->belongsTo(TypeDemande::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function valeurs(): HasMany
    {
        return $this->hasMany(ValeurDemande::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function affectations(): HasMany
    {
        return $this->hasMany(Affectation::class);
    }

    public function affectationActive(): HasMany
    {
        return $this->hasMany(Affectation::class)->where('statut', 'active');
    }

    public function validations(): HasMany
    {
        return $this->hasMany(Validation::class);
    }
}
