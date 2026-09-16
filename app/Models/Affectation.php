<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Affectation extends Model
{
    use HasFactory;

    protected $fillable = [
        'demande_id',
        'agent_id',
        'affecte_par_id',
        'statut',
        'commentaire',
        'date_affectation',
        'date_fin',
    ];

    protected function casts(): array
    {
        return [
            'date_affectation' => 'datetime',
            'date_fin' => 'datetime',
        ];
    }

    public function demande(): BelongsTo
    {
        return $this->belongsTo(Demande::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function affectePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'affecte_par_id');
    }
}
