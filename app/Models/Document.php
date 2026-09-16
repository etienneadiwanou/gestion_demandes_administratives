<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'demande_id',
        'user_id',
        'nom_original',
        'chemin_fichier',
        'type_mime',
        'taille',
    ];

    public function demande(): BelongsTo
    {
        return $this->belongsTo(Demande::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
