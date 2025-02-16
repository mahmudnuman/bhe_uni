<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable, HasFactory;

    protected $fillable = [
        'id',
        'name',
        'email',
        'email_verified_at',
        'password',
        'role'
    ];
    protected $hidden = ['password'];

    protected $casts = ['password' => 'hashed'];

    public function leads() {
        return $this->hasMany(Assignment::class, 'counselor_id');
    }

    public function applications() {
        return $this->hasMany(Application::class, 'counselor_id');
    }

    public function getJWTIdentifier() {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return ['role' => $this->role];
    }

    public function assignments() {
        return $this->hasMany(Assignment::class, 'counselor_id');
    }
}
