<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'is_active',
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
            'is_active' => 'boolean',
        ];
    }

    /**
     * Cek apakah user adalah Managing Director (Top Level / Full Modul).
     */
    public function isManagingDirector(): bool
    {
        return $this->role === 'managing_director';
    }

    /**
     * Cek apakah user adalah Superadmin (Akses luas, tetapi dibatasi dari Executive Dashboard).
     */
    public function isSuperAdmin(): bool
    {
        return in_array($this->role, ['managing_director', 'superadmin']);
    }

    /**
     * Cek apakah user memiliki hak akses dashboard / modul CRM (MD, Superadmin, Admin).
     */
    public function isAdmin(): bool
    {
        return in_array($this->role, ['managing_director', 'superadmin', 'admin']);
    }

    public function assignedLeads(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Lead::class, 'assigned_to');
    }

    public function surveys(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Survey::class, 'pic_user_id');
    }
}
