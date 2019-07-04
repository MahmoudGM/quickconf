<?php

namespace App;
use DB;
use Nicolaslopezj\Searchable\SearchableTrait;

class Conference extends Model
{

    use SearchableTrait;

    protected $searchable = [
        'columns' => [
            'conferences.confAcronym' => 10,
            'conferences.confName' => 10,    
        ]
    ];
    
    public function users()
    {
        return $this->belongsToMany('App\User');
    }

    public function messages()
    {
        return $this->hasMany(Messagetemp::class);
    }


    public function pquestions()
    {
        return $this->hasMany(Paperquestion::class);
    }

    public function rquestions()
    {
        return $this->hasMany(Reviewquestion::class);
    }

    public function topics()
    {
        return $this->hasMany(Topic::class);
    }

    public function ratelabels()
    {
        return $this->hasMany(Ratelabel::class);
    }

    public function criterias()
    {
        return $this->hasMany(Criteria::class);
    }

    public function paperstatuses()
    {
        return $this->hasMany(Paperstatus::class);
    }

    public function papers()
    {
        return $this->hasMany(Paper::class);
    }

    public function getConference($acronym,$edition,$role='admin'){
        /*
        $conference = DB::table('conference_user')
                        ->join('users', 'users.id', '=', 'conference_user.user_id')
                        ->join('conferences', 'conferences.id', '=', 'conference_user.conference_id')
                        ->where('conference_user.user_id' , '=' , auth()->id())
                        ->where('conference_user.role' , '=' , 'A')                        
                        ->where('conferences.confAcronym' , '=' , $acronym)
                        ->where('conferences.confEdition' , '=' , $edition)
                        ->where('conferences.is_activated' , '=' , 1)
                        ->first();
            */
        if($role == 'admin')
        {
            $conference =auth()->user()->conferences
                                      ->where('pivot.role','A')
                                      ->where('confAcronym',$acronym)
                                      ->where('confEdition',$edition)
                                      ->where('is_activated',1)
                                      ->first();
        }
        elseif($role == 'author')
        {
            $conference =auth()->user()->conferences
                                      ->where('pivot.role','Aut')
                                      ->where('confAcronym',$acronym)
                                      ->where('confEdition',$edition)
                                      ->where('is_activated',1)
                                      ->first();
                                      
            /*$conference = \App\Conference::where('confAcronym' , '=' , $acronym)
                                        ->where('confEdition' , '=' , $edition)
                                        ->first();*/
        }
        elseif($role == 'R')
        {
            $conference =auth()->user()->conferences
                                      ->where('pivot.role','R')
                                      ->where('confAcronym',$acronym)
                                      ->where('confEdition',$edition)
                                      ->where('is_activated',1)
                                      ->first();
                                      
        }elseif($role == 'AC')
        {
            $conference =auth()->user()->conferences
                                      ->where('pivot.role','A')
                                      ->where('confAcronym',$acronym)
                                      ->where('confEdition',$edition)
                                      ->where('is_activated',1)
                                      ->first();
                            
            if(count($conference) == 0){
                $conference =auth()->user()->conferences
                                      ->where('pivot.role','C')
                                      ->where('confAcronym',$acronym)
                                      ->where('confEdition',$edition)
                                      ->where('is_activated',1)
                                      ->first();
            }
                                      
        }
         
            
        return $conference;

    }


}
