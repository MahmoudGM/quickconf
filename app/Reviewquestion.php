<?php

namespace App;

class Reviewquestion extends Model
{
    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }
}
