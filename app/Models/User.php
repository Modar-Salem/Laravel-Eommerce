<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
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

        'password' ,

        'role' ,

        'phone'
    ];


// variable hidden don't return when we return user data
    protected $hidden = [
        'remember_token' ,
        'password'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    //if we write User:find(1)->product()->delete() : will remove all product of this user
    //we can use many method like : where() , update() , get() ...
    public function product() {
        return $this->hasMany(Product::class) ;
    }

    public function favorite()
    {
        return $this->hasMany(Favorite::class) ;
    }

    public function purchase()
    {
        return $this->hasMany(Purchase::class) ;
    }

    public function rate()
    {
        return $this->hasMany(Rate::class) ;
    }
}
