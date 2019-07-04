<?php

namespace App;

class Paperquestion extends Model
{
    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }
    
    public function papers()
    {
        return $this->belongsToMany('App\Paper');
    }
}
