<?php

namespace App;


class Topic extends Model
{
    protected $hidden=['conference_id'];
    public function papers()
    {
        return $this->belongsToMany('App\Paper');
    }

    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
