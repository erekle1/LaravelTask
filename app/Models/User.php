<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
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
        'password'          => 'hashed',
    ];


    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    public function userProductGroups(): HasMany
    {
        return $this->hasMany(UserProductGroup::class);
    }

    public function productGroupItems(): HasManyThrough
    {
        return $this->hasManyThrough(
            ProductGroupItem::class,
            UserProductGroup::class,
            'user_id', // Foreign key on UserProductGroup table
            'group_id', // Foreign key on ProductGroupItem table
            'id', // Local key on User table
            'id'  // Local key on UserProductGroup table
        );
    }

}
