<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChampDemande extends Model
{
    use HasFactory;

    protected $fillable = [
        'type_demande_id',
        'label',
        'nom_technique',
        'type_champ',
        'obligatoire',
        'options',
        'ordre',
    ];

    protected function casts(): array
    {
        return [
            'obligatoire' => 'boolean',
            'options' => 'array',
            'ordre' => 'integer',
        ];
    }

    public function typeDemande(): BelongsTo
    {
        return $this->belongsTo(TypeDemande::class);
    }

    public function valeurs(): HasMany
    {
        return $this->hasMany(ValeurDemande::class);
    }
}
