<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use HasFactory;

    // Un log d'audit n'est jamais modifié après création.
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'action',
        'entite',
        'entite_id',
        'donnees_avant',
        'donnees_apres',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'donnees_avant' => 'array',
            'donnees_apres' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
