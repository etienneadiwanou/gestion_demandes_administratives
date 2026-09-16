<?php

namespace App\Models;

use App\Enums\DecisionValidation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Validation extends Model
{
    use HasFactory;

    // Le nom de la table est explicité car "validations" entre en
    // conflit potentiel avec le vocabulaire du framework (validation
    // des requêtes) : mieux vaut être explicite ici.
    protected $table = 'validations';

    protected $fillable = [
        'demande_id',
        'validateur_id',
        'decision',
        'commentaire',
    ];

    protected function casts(): array
    {
        return [
            'decision' => DecisionValidation::class,
        ];
    }

    public function demande(): BelongsTo
    {
        return $this->belongsTo(Demande::class);
    }

    public function validateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validateur_id');
    }
}
