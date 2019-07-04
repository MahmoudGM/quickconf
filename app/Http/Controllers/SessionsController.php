<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Middleware\CheckIfAdminChair;
use App\Slot;
use App\Session;
use App\Conference;

class SessionsController extends Controller
{
     public function __construct(){
        $this->middleware('auth');
        $this->middleware(CheckIfAdminChair::class);
    }

    public function store($acronym,$edition, Request $request){

        $lang=$request->session()->get('lang');       
        $P = parse_ini_file(base_path('language/'.$lang.'/PROGRAM.ini'));

        if(Session::where('name',$request->name)->where('slot_id',$request->slotId)->count() != 0)
                return back()->withErrors([
                'message' => $P['ERROR_SESS_EX']
            ]);


        $this->validate(request(), [
            'slotId'     =>  ['required','regex:/^[^<>]+$/'],
            'name'       =>  ['required','min:3','max:255','regex:/^[^<>]+$/'],
            'room'       =>  ['required','min:1','max:255','regex:/^[^<>]+$/'],
            'comment'    =>  ['required','regex:/^[^<>]+$/'],
            'capacity'   =>  ['required','numeric','min:1','regex:/^[^<>]+$/'],
            'chair'      =>  ['required','numeric','min:1','regex:/^[^<>]+$/'],
        ]);

        $position = Session::where('slot_id',$request->slotId)->orderBy('position','DESC')->first();

        if(count($position) == 0){
            $posSession = 1;
        }else{
            $posSession = $position->position+1;
        }

        Session::create([
            'slot_id' => $request->slotId,
            'name' => $request->name,
            'room' => $request->room,
            'comment' => $request->comment,
            'capacity' => $request->capacity,
            'position' => $posSession,
            'user_id' => $request->chair,
        ]);

        return back();
    }

    public function update($acronym,$edition, Request $request){

        $lang=$request->session()->get('lang');       
        $P = parse_ini_file(base_path('language/'.$lang.'/PROGRAM.ini'));


        $session = Session::find($request->sessionId);
        if ( count($session) == 0 )
            return back()->withErrors([
                'message' => $P['ERR_SESS_404']
            ]);

        if($session->name != $request->name){
           if(Session::where('name',$request->name)->where('slot_id',$request->slotId)->count() != 0)
                return back()->withErrors([
                'message' => $P['ERROR_SESS_EX']
            ]);
        }
        
        $this->validate(request(), [
            'sessionId'     =>  ['required','regex:/^[^<>]+$/'],
            'name'       =>  ['required','min:3','max:255','regex:/^[^<>]+$/'],
            'room'       =>  ['required','min:1','max:255','regex:/^[^<>]+$/'],
            'comment'    =>  ['required','regex:/^[^<>]+$/'],
            'capacity'   =>  ['required','numeric','min:1','regex:/^[^<>]+$/'],
            'chair'      =>  ['required','numeric','min:1','regex:/^[^<>]+$/'],
        ]);

        

        $session->name=$request->name;
        $session->room=$request->room;
        $session->comment=$request->comment;
        $session->capacity=$request->capacity;
        $session->user_id=$request->chair;

        $session->save();

        return back();
    }

    public function assignPapers($acronym,$edition,Session $session,Request $request){

        $lang=$request->session()->get('lang');       
        $P = parse_ini_file(base_path('language/'.$lang.'/PROGRAM.ini'));

        $conf = new Conference;
        $conference = $conf->getConference($acronym,$edition,'AC');

        $this->validate(request(), [
            'papers.*'   =>  ['required','numeric','min:1','regex:/^[^<>]+$/'],
        ]);

        $v = (count(request('papers')) != count(array_unique(request('papers'))));

         if($v){
             return back()->withErrors([
                'message' => $P['ERR_DUP_PAPERS']
            ]);
         }

        $currentPos = \App\Paper::where('conference_id',$conference->id)
                                ->where('session_id',$session->id)
                                ->orderBy('pos_in_session','DESC')
                                ->first();
        if(count($currentPos) == 0)
            $currentPos = 0;
        else
            $currentPos=$currentPos->pos_in_session;
        foreach($request->papers as $p){
            $paper = \App\Paper::find($p);
            $paper->pos_in_session = $currentPos;
            $paper->session_id = $session->id;
            $paper->save();
            $currentPos++;
        }

        
   
        return back();
    }

    public function updatePapers($acronym,$edition,Session $session,Request $request){

        $conf = new Conference;
        $conference = $conf->getConference($acronym,$edition,'AC');

 
        if($request->paperPos != null){

        $this->validate(request(), [
            'paperPos.*'   =>  ['required','numeric','min:1','regex:/^[^<>]+$/'],
        ]);
        
        $papers = \App\Paper::where('conference_id',$conference->id)->where('session_id',$session->id)->get();
        //return $papers;
        if(count($papers) == count($request->paperPos)){
            $pos=1;
            foreach($request->paperPos as $p_pos){
                $paper = \App\Paper::find($p_pos);
                $paper->pos_in_session = $pos;
                $paper->save();
                $pos++;
            }
        }else{
            $query = \DB::table('papers');
            foreach($request->paperPos as $pr){
                $deletePapers = $query->where('id','!=',$pr);
            }
            $deleteP = $deletePapers->where('conference_id',$conference->id)->where('session_id',$session->id);
            $deleteP->update(['session_id' => null]);
        }
        

        }else{
             \App\Paper::where('conference_id',$conference->id)->where('session_id',$session->id)->update(['session_id' => null]);
        }

        return back();




        
    } 



    public function jsonSession($acronym,$edition,Session $session){
        $sess =  \DB::table('sessions')->join('users','users.id','sessions.user_id')->where('sessions.id',$session->id)->select('users.*','sessions.*')->first();

        $papers = \DB::select('select topics.*,authors.*, papers.*, paperstatuses.label as psLabel, paperstatuses.camReadyRequired as camReadyRq from papers
                                    INNER JOIN paper_topic ON papers.id = paper_topic.paper_id
                                    INNER JOIN topics ON topics.id = paper_topic.topic_id
                                    INNER JOIN authors ON authors.paper_id = papers.id
                                    LEFT OUTER JOIN paperstatuses ON paperstatuses.id = papers.paperstatus_id
                                    WHERE authors.is_corresponding = 1
                                        and session_id = :sess
                                    GROUP BY papers.id
                                    ORDER BY papers.pos_in_session
                                    ',['sess' => $session->id]);

        return \Response::json(array('session' => $sess, 'papers'=> $papers,));
    }

     public function delete($acronym,$edition,Session $session){
        $session->delete();
        return back();
    }


}
