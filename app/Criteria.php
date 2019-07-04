<?php

namespace App;


class Criteria extends Model
{
    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }
}
