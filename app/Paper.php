<?php

namespace App;

class Paper extends Model
{
    public function topics()
    {
        return $this->belongsToMany('App\Topic');
    }

    public function paperquestions()
    {
        return $this->belongsToMany('App\Paperquestion');
    }

    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }

    public function author()
    {
        return $this->belongsTo('App\Author');
    }
}
