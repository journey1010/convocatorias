<?php

namespace Modules\User\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laratrust\Traits\HasRolesAndPermissions;
use Illuminate\Notifications\Notifiable;
use Laratrust\Contracts\LaratrustUser;

class User extends Authenticatable implements LaratrustUser
{
    use HasFactory, Notifiable, HasRolesAndPermissions;

    protected static function newFactory()
    {
        return \Database\Factories\UserFactory::new();
    }

    public function officeUser() 
    {
        return $this->hasMany(\Modules\User\Models\OfficeUser::class);
    }

    protected $fillable = [
        'name',
        'last_name',
        'dni',
        'nickname',
        'email',
        'email_verified_at',
        'phone_verified_at',
        'phone',
        'password',
        'status',
        'created_by',
        'level',
        'type_user',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}