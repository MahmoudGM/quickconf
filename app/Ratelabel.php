<?php

namespace App;

class Ratelabel extends Model
{
    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }
}
