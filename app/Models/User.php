<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function images():HasMany
    {
        return $this->hasMany(Image::class);
    }


    /**
     * Get a user's private channel from the address id
     *
     * @param int $user_id
     * @return string
     */
    public static function getPrivateBroadcastChannelForUserId(int $user_id)
    {
        return 'App.Models.User.' . $user_id;
    }

    /**
     * The user's private channel
     *
     * @return Attribute
     */
    public function privateChannel(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => User::getPrivateBroadcastChannelForUserId($attributes['id']),
        );
    }
}
