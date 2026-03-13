<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // ← أضف هذا

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens; // ← أضف HasApiTokens هنا

  protected $fillable = [
    'name', 'email', 'password',
    'avatar', 'avatar_url', 'current_country_code', 'current_city',
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

    public function visits()
    {
        return $this->hasMany(Visit::class);
    }
}