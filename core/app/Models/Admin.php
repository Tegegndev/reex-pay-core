<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable
{
    use HasFactory;
    use HasRoles;
    use Notifiable;

    protected string $guard_name = 'admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'avatar',
        'name',
        'email',
        'google2fa_secret',
        'two_factor_enabled',
        'password',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function getAvatarAltAttribute(): string
    {
        return $this->avatar ?? '/general/static/default/admin.png';
    }

    public function getRecentNotifications(): \Illuminate\Support\Collection
    {
        return $this->unreadNotifications()->get();
    }

    /**
     * Get the attributes that should be cast.
     *
     * This method defines the attributes that should be cast to a specific type.
     *
     * @return array Returns an array of attribute names and their corresponding types.
     */
    protected function casts(): array
    {
        // Define the attributes that should be cast
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'status'            => 'boolean',
        ];
    }
}
