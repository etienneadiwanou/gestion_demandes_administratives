<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

class AuditLogService
{
    /**
     * Enregistre une action sur une entité dans la piste d'audit.
     */
    public function log(?int $userId, string $action, Model $entite, ?array $donneesAvant = null, ?array $donneesApres = null): AuditLog
    {
        return AuditLog::create([
            'user_id' => $userId,
            'action' => $action,
            'entite' => $entite::class,
            'entite_id' => $entite->getKey(),
            'donnees_avant' => $donneesAvant,
            'donnees_apres' => $donneesApres,
            'ip_address' => request()?->ip(),
        ]);
    }
}
