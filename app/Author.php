<?php

namespace App;



class Author extends Model
{
    public function papers()
    {
        return $this->hasMany('App\Paper');
    }


    
}
