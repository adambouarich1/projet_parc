<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Les rôles possibles
    public const ROLE_ADMIN = 'admin';
    public const ROLE_RESPONSABLE_PARC = 'responsable_parc';
    public const ROLE_VALIDEUR = 'valideur';
    public const ROLE_AGENT_SAISIE = 'agent_saisie';
    public const ROLE_CONSULTATION = 'consultation';

    public const ROLES = [
        self::ROLE_ADMIN => 'Administrateur',
        self::ROLE_RESPONSABLE_PARC => 'Responsable Parc',
        self::ROLE_VALIDEUR => 'Valideur',
        self::ROLE_AGENT_SAISIE => 'Agent de Saisie',
        self::ROLE_CONSULTATION => 'Consultation',
    ];

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
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
        ];
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role, $roles);
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function canEdit(): bool
    {
        return $this->role !== self::ROLE_CONSULTATION;
    }

    public function canValidate(): bool
    {
        return in_array($this->role, [
            self::ROLE_ADMIN,
            self::ROLE_RESPONSABLE_PARC,
            self::ROLE_VALIDEUR,
        ]);
    }

    public function getRoleLabelAttribute(): string
    {
        return self::ROLES[$this->role] ?? $this->role;
    }
}