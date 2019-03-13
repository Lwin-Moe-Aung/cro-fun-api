<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens,Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','role',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    public function fieldofficers()
    {
        return $this->hasMany('App\FieldOfficer');
    }
    public function lenders()
    {
        return $this->hasMany('App\Lender');
    }
    public function borrowers()
    {
        return $this->hasMany('App\Borrower');
    }
}
