<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ValeurDemande extends Model
{
    use HasFactory;

    protected $fillable = [
        'demande_id',
        'champ_demande_id',
        'valeur',
    ];

    public function demande(): BelongsTo
    {
        return $this->belongsTo(Demande::class);
    }

    public function champDemande(): BelongsTo
    {
        return $this->belongsTo(ChampDemande::class);
    }
}
