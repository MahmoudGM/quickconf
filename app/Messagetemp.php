<?php

namespace App;


class Messagetemp extends Model
{
    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }
}
