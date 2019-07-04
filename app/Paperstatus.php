<?php

namespace App;

class Paperstatus extends Model
{
    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }
}
