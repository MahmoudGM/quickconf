<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    //public $with=['topics','conferences'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name','last_name','affilation', 'email', 'password','roles','country','grade'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function conferences()
    {
        return $this->belongsToMany('App\Conference')->withPivot("role");
    }

    public function topics()
    {
        return $this->belongsToMany('App\Topic');
    }
}
