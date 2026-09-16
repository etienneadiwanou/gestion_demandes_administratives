<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TypeDemande extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'code',
        'categorie',
        'description',
        'actif',
    ];

    protected function casts(): array
    {
        return [
            'actif' => 'boolean',
        ];
    }

    public function champs(): HasMany
    {
        return $this->hasMany(ChampDemande::class)->orderBy('ordre');
    }

    public function demandes(): HasMany
    {
        return $this->hasMany(Demande::class);
    }
}
