<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Middleware\CheckIfAdminChair;
use App\Slot;
use App\Session;
use App\Conference;

class SlotsController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
        $this->middleware(CheckIfAdminChair::class);
    }

    public function index($acronym,$edition){

        $conf = new Conference;
        $conference = $conf->getConference($acronym,$edition,'AC');

        $slots = Slot::where('conference_id',$conference->id)->orderBy('begin')->get();

        $nbrDate = Slot::where('conference_id',$conference->id)->orderBy('date')->groupBy('date')->get();

        $chairs = \DB::table('conferences')
                    ->join('conference_user' , 'conferences.id' , 'conference_user.conference_id' )
                    ->join('users' , 'users.id' , '=' , 'conference_user.user_id')
                    ->where('conference_user.conference_id',$conference->id)
                    ->where('conference_user.role','C')
                    ->select('users.*')
                    ->get();
                    
        $papers=\App\Paper::where('conference_id',$conference->id)->where('session_id',null)->orderBy('pos_in_session')->get();
        
        return view('conferences.slots.index',compact('slots','nbrDate','conference','chairs','papers'));
    }

    public function commit($acronym,$edition,Request $request){


        $slotArray=[];
        
        foreach($request->all() as $key => $value){
            if($key != '_token'){
                $slotId = str_replace('slot','',$key);
                $slotArray[]= 'slot'.$slotId;

                $name='slot'.$slotId;

                $i = 1;
                foreach($request->$name as $sess){
                    $session = Session::find($sess);
                    $session->slot_id = $slotId;
                    $session->position=$i;
                    $session->save();
                    $i++;
                }
            }

            

        }


        return back();

    }

    public function store($acronym,$edition,Request $request){

        $lang=$request->session()->get('lang');       
        $P = parse_ini_file(base_path('language/'.$lang.'/PROGRAM.ini'));
        
        $this->validate(request(), [
            'name'       =>  ['required','min:3','max:255','regex:/^[^<>]+$/'],
            'color'      =>  ['required','min:3','max:255','regex:/^[^<>]+$/'],
            'type'       =>  ['required','in:1,2','numeric','regex:/^[^<>]+$/'],
            'begin'      =>  ['required','max:5','regex:/^[^<>]+$/'],
            'end'        =>  ['required','max:5','regex:/^[^<>]+$/'],
        ]);

        $conf = new Conference;
        $conference = $conf->getConference($acronym,$edition,'AC');

        if(Slot::where('name',$request->name)->where('conference_id',$conference->id)->where('date',$request->date)->count() != 0)
                return back()->withErrors([
                'message' => $P['ERR_SLOT_EX']
            ]);
        

        if($request->dateSlot != null){

            $this->validate(request(), [
                'dateSlot'      =>  ['required','date','regex:/^[^<>]+$/'],
            ]);
  

        $begin = $request->begin.':00';
        $end = $request->end.':00';

        if(strtotime($begin) >= strtotime($end)){
            return back()->withErrors([
                'message' => $P['ERR_TIME_COMB']
            ]);
        }

        $slotExist = Slot::where('conference_id',$conference->id)
                        ->where('date',$request->dateSlot)
                        ->where(function($query)use($begin,$end){
                            $query->orWhere(function($query1) use ($begin,$end){
                                $query1->where('begin','>',$begin)
                                        ->where('begin','<',$end);
                            })
                            ->orWhere(function($query2) use ($begin,$end){
                                $query2->where('end','>',$begin)
                                        ->where('end','<',$end);
                            })
                            ->orWhere(function($query3) use ($begin,$end){
                                $query3->where('begin','<',$begin)
                                        ->where('end','>',$end);
                            })
                            ->orWhere(function($query4) use ($begin,$end){
                                $query4->where('begin','=',$begin)
                                        ->where('end','=',$end);
                            });

                        })->count();


        if($slotExist != 0){
            return back()->withErrors([
                'message' => $P['ERR_TIME_RES']
            ]);
        }

        Slot::create([
            'name' => $request->name,
            'color' => $request->color,
            'type' => $request->type,
            'begin' => $request->begin,
            'end' => $request->end,
            'date' => $request->dateSlot,
            'conference_id' => $conference->id,
        ]);



        }else{

       
        $this->validate(request(), [
                'date'      =>  ['required','date','regex:/^[^<>]+$/'],
            ]);
        
        $begin = $request->begin.':00';
        $end = $request->end.':00';

        if(strtotime($begin) >= strtotime($end)){
            return back()->withErrors([
                'message' => $P['ERR_TIME_COMB']
            ]);
        }

        $slotExist = Slot::where('conference_id',$conference->id)
                        ->where('date',$request->date)
                        ->where(function($query)use($begin,$end){
                            $query->orWhere(function($query1) use ($begin,$end){
                                $query1->where('begin','>',$begin)
                                        ->where('begin','<',$end);
                            })
                            ->orWhere(function($query2) use ($begin,$end){
                                $query2->where('end','>',$begin)
                                        ->where('end','<',$end);
                            })
                            ->orWhere(function($query3) use ($begin,$end){
                                $query3->where('begin','<',$begin)
                                        ->where('end','>',$end);
                            })
                            ->orWhere(function($query4) use ($begin,$end){
                                $query4->where('begin','=',$begin)
                                        ->where('end','=',$end);
                            });

                        })->count();

        if($slotExist != 0){
            return back()->withErrors([
                'message' => $P['ERR_TIME_RES']
            ]);
        }

        Slot::create([
            'name' => $request->name,
            'color' => $request->color,
            'type' => $request->type,
            'begin' => $request->begin,
            'end' => $request->end,
            'date' => $request->date,
            'conference_id' => $conference->id,
        ]);




        }

        return back();
    }

    public function jsonSlot($acronym,$edition,\App\Slot $slot){
        return $slot;
    }

    public function delete($acronym,$edition,Slot $slot){
        $slot->delete();
        return back();
    }

     public function update($acronym,$edition, Request $request){

        $lang=$request->session()->get('lang');       
        $P = parse_ini_file(base_path('language/'.$lang.'/PROGRAM.ini'));

        $slot = Slot::find($request->slotId);
        if ( count($slot) == 0 )
            return back()->withErrors([
                'message' => $P['ERR_SLOT_404']
            ]);
            
         
        
         $this->validate(request(), [
            'name'       =>  ['required','min:3','max:255','regex:/^[^<>]+$/'],
            'color'       =>  ['required','min:3','max:255','regex:/^[^<>]+$/'],
            'type'       =>  ['required','in:1,2','numeric','regex:/^[^<>]+$/'],
            'slotId'       =>  ['required','numeric','regex:/^[^<>]+$/'],
            'date'       =>  ['required','date','regex:/^[^<>]+$/'],
            'begin'      =>  ['required','max:5','regex:/^[^<>]+$/'],
            'end'        =>  ['required','max:5','regex:/^[^<>]+$/'],
        ]);

        $conf = new Conference;
        $conference = $conf->getConference($acronym,$edition,'AC');
        
        if($slot->name != $request->name){
            if(Slot::where('name',$request->name)->where('conference_id',$conference->id)->where('date',$request->date)->count() != 0)
                return back()->withErrors([
                'message' => $P['ERR_SLOT_EX']
            ]);
        }



        $begin = $request->begin.':00';
        $end = $request->end.':00';

        if(strtotime($begin) >= strtotime($end)){
            return back()->withErrors([
                'message' => $P['ERR_TIME_COMB']
            ]);
        }

        $slotExist = Slot::where('conference_id',$conference->id)
                        ->where('id','!=',$request->slotId)
                        ->where('date',$request->date)
                        ->where(function($query)use($begin,$end){
                            $query->orWhere(function($query1) use ($begin,$end){
                                $query1->where('begin','>',$begin)
                                        ->where('begin','<',$end);
                            })
                            ->orWhere(function($query2) use ($begin,$end){
                                $query2->where('end','>',$begin)
                                        ->where('end','<',$end);
                            })
                            ->orWhere(function($query3) use ($begin,$end){
                                $query3->where('begin','<',$begin)
                                        ->where('end','>',$end);
                            })
                            ->orWhere(function($query4) use ($begin,$end){
                                $query4->where('begin','=',$begin)
                                        ->where('end','=',$end);
                            });
                        })->count();

     

        if($slotExist != 0){
            return back()->withErrors([
                'message' => $P['ERR_TIME_RES']
            ]);
        }

        $slot->name = $request->name;
        $slot->color = $request->color;
        $slot->type = $request->type;
        $slot->date = $request->date;
        $slot->begin = $request->begin;
        $slot->end = $request->end;

        $slot->save();

        return back();


     }
public function preview($acronym,$edition){ 

        $conf = new Conference;
        $conference = $conf->getConference($acronym,$edition,'AC');
        
        $days = Slot::where('conference_id',$conference->id)->orderBy('date')->groupBy('date')->get();

        return view('conferences.slots.preview',compact('conference','days'));
}
public function docProgram($acronym,$edition){

        $conf = new Conference;
        $conference = $conf->getConference($acronym,$edition,'AC');

        $headers = array(
            "Content-type"=>"text/html",
            "Content-Disposition"=>"attachment;Filename=myfile.docx"
        );

        $Days = Slot::where('conference_id',$conference->id)->orderBy('date')->groupBy('date')->get();
        $style= asset('assets/semantic-ui/semantic.min.css');
        $content = '<html>
                    <head>
                    <meta charset="utf-8">
                    <link href="'.$style.'" rel="stylesheet">
                    <style>
                    body{
                        font-size:10px;
                    }
                    </style>
                    </head>
                    <body>
                        <center><h2 style="color:#db2828">
                            Program of:
                            '.$conference->confAcronym . $conference->confEdition.'
                            Conference
                        </h2></center><br>';
        $i = 1;

        foreach($Days as $day){
            $slots = Slot::where('conference_id',$conference->id)->where('date',$day->date)->orderBy('begin')->get();
            $content .= '<h3 style="color:#18969e">Day'.$i.': '.$day->date.'</h3>';
                $content .= '<ul>';
                    foreach($slots as $slot){
                    $sessions = Session::where('slot_id',$slot->id)->orderBy('position')->get();
                    $content .= '<li style="margin:5px">'.$slot->name.': '.$slot->begin.'-'.$slot->end.'</li>';
                        $content .='<ul>';
                            foreach($sessions as $session){
                                $content .= '<li style="margin:4px">'.$session->name.', room: '.$session->room.' ('.$session->capacity.' papers)</li>';
                                $papers=\App\Paper::where('session_id',$session->id)->orderBy('pos_in_session')->get();
                                    $content .='<ol>';
                                        foreach($papers as $paper){
                                            $content .= '<li style="margin:4px">'.$paper->title.'</li>';
                                        }
                                    $content .='</ol>';
                            }
                         $content .='</ul>';
                    }
                $content .= '</ul>';

        $i++;
        if($i!=count($Days)+1)
            $content .='<hr>';
        }  



        $content .= '</body>
                    </html>';

        return \Response::make($content,200, $headers);


     }
}
