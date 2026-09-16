<?php

namespace App\Models;

use App\Enums\RoleSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'telephone',
        'role_id',
        'department_id',
        'actif',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'actif' => 'boolean',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function demandes(): HasMany
    {
        return $this->hasMany(Demande::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function affectationsRecues(): HasMany
    {
        return $this->hasMany(Affectation::class, 'agent_id');
    }

    public function affectationsEffectuees(): HasMany
    {
        return $this->hasMany(Affectation::class, 'affecte_par_id');
    }

    public function validations(): HasMany
    {
        return $this->hasMany(Validation::class, 'validateur_id');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    public function hasPermission(string $slug): bool
    {
        return $this->role?->hasPermission($slug) ?? false;
    }

    public function hasRole(RoleSlug $role): bool
    {
        return $this->role?->slug === $role->value;
    }
}
