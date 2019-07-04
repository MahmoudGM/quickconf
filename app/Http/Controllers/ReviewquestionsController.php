<?php

namespace App\Http\Controllers;
use App\Http\Middleware\CheckIfAdmin;

use Illuminate\Http\Request;
use App\Conference;
use App\Reviewquestion;
use Datatables;

class ReviewquestionsController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
        $this->middleware(CheckIfAdmin::class);
    }

    public function getData($acronym,$edition,Request $request){
        $lang=$request->session()->get('lang'); 
        $Q = parse_ini_file(base_path('language/'.$lang.'/RQUESTIONS.ini'));
        
        $conf = new Conference;
        $conference = $conf->getConference($acronym,$edition);

        $GLOBALS['C'] = $conference;
        //return $GLOBALS['C'];
        $pquestions =  \App\Conference::where('confAcronym' , '=' , $acronym)
                                        ->where('confEdition' , '=' , $edition)
                                        ->first()
                                        ->rquestions;
        
        $GLOBALS['L'] = $lang;
        $GLOBALS['Q'] = $Q;
        return Datatables::of($pquestions)->addColumn('action', function ($pq) {
                    return '

                        <form id="deletePqForm" action="/conferences/'.$GLOBALS['C']->confAcronym.'/'.$GLOBALS['C']->confEdition.'/rquestions/'.$pq->id.'/delete" method = "POST">
                            <a href="/conferences/'.$GLOBALS['C']->confAcronym.'/'.$GLOBALS['C']->confEdition.'/rquestions/'.$pq->id.'/edit" class="button ui primary"><i class="edit icon"></i>  '.$GLOBALS['Q']['BTN_EDIT'].'</a>
                            <button type="submit" class="button ui red"><i class="delete icon"></i>  '.$GLOBALS['Q']['BTN_DELETE'].'</a>
                        </form>';
                        //<a href="#show-'.$msg->id.'" class="button ui teal"><i class="unhide icon"></i>  Show</a>';
                },5)->addColumn('choices',function($pq){
                    $choices = \DB::table('pqchoices')->select('choice')->where('paperquestion_id',$pq->id)->get();
                    
                    return $choices;

                },5)
                ->make(true);

            }

    public function index($acronym,$edition,Request $request)
    {
        $conf = new Conference;
        $conference = $conf->getConference($acronym,$edition);

        $rqs = Reviewquestion::where('conference_id', '=' ,$conference->id)->first();
        return view('conferences.rquestions.index',compact('conference','rqs'));

    }

    public function create($acronym,$edition)
    {
        $conf = new Conference;
        $conference = $conf->getConference($acronym,$edition);

        return view('conferences.rquestions.create',compact('conference'));
 
    }

    public function store($acronym,$edition,Request $request)
    {
        $lang=$request->session()->get('lang');       
        $Q = parse_ini_file(base_path('language/'.$lang.'/RQUESTIONS.ini'));

        $conf = new Conference;
        $conference = $conf->getConference($acronym,$edition);

         $v = (count(request('choice')) != count(array_unique(request('choice'))));
         //return request('position');
         $pos = request('position');
         sort($pos);
         $res = "true";
         for($i=0;$i<count($pos);$i++){
             if($i!=count($pos)-1){
                if($pos[$i]-$pos[$i+1] != -1)
                {
                    $res = false;
                    break;
                }
             }
         }
        $res;
        if($res == false){
            return back()->withErrors([
                'message' => $Q['POS_EROOR']
            ]);
        }
        if($v){
            return back()->withErrors([
                'message' => $Q['CHOICE_ERROR']
            ]);
        }
        if(count($request->choice) < 2 )
        {
            return back()->withErrors([
                'message' => $Q['CHOICE_COUNT_ERR']
            ]);
        }
             
        $this->validate(request(), [
            'question'      =>  ['required','min:5','max:255','regex:/^[^<>]+$/'],
            'public'      =>  ['required','numeric','min:0','max:1','regex:/^[^<>]+$/'],
            'choice.*'      =>  ['required','min:2','max:255','regex:/^[^<>]+$/'],
            'position.*'    =>  ['required','numeric','min:1','max:5','regex:/^[^<>]+$/'],
            ]);

        $rqs = Reviewquestion::where('question',$request->question)
                            ->where('conference_id', $conference->id)
                            ->count();
        
        if($rqs != 0){
            return back()->withErrors([
                'message' => "Question already exist"
            ]);
        }

        Reviewquestion::create([
            'question'=>request('question'),
            'public'=>request('public'),
            'conference_id'=>request('conference_id'),
        ]);

        $n = count(request('choice'));

        $pq = new Reviewquestion;
        $pqId = $pq->orderBy('id', 'DESC')->first();


        for($i=0;$i<$n;$i++)
        {
            \DB::table('rqchoices')->insert([
                'choice' => request('choice')[$i],
                'position' => request('position')[$i],
                'reviewquestion_id' => $pqId->id,
            ]);
        }
        return redirect('/conferences/'.$acronym.'/'.$edition.'/rquestions');
    }

    public function edit($acronym,$edition, Reviewquestion $reviewquestion)
    {
        $conf = new Conference;
        $conference = $conf->getConference($acronym,$edition);

        $rquestion = $reviewquestion;
        $rqchoice =  \DB::table('rqchoices')->where('reviewquestion_id' , '=' , $rquestion->id)->get();

        return view('conferences.rquestions.edit',compact('conference','rquestion','acronym','edition','rqchoice'));

    }

    public function update($acronym,$edition, Reviewquestion $reviewquestion,Request $request)
    {


        $conf = new Conference;
        $conference = $conf->getConference($acronym,$edition);

        $lang=$request->session()->get('lang');       
        $Q = parse_ini_file(base_path('language/'.$lang.'/RQUESTIONS.ini'));

        if( ($request->added_choice!=null) and($request->added_position!=null) ){
            $choicesA = array_merge($request->choice ,$request->added_choice);
            $pos = array_merge($request->position ,$request->added_position);
        }else{
            $choicesA = $request->choice ;
            $pos = $request->position ;
        }


         $v = (count($choicesA) != count(array_unique($choicesA)));
         //return request('position');

         sort($pos);
         $res = "true";
         for($i=0;$i<count($pos);$i++){
             if($i!=count($pos)-1){
                if($pos[$i]-$pos[$i+1] != -1)
                {
                    $res = false;
                    break;
                }
             }
         }
        $res;
        if($res == false){
            return back()->withErrors([
                'message' => $Q['POS_ERROR']
            ]);
        }
        if($v){
            return back()->withErrors([
                'message' => $Q['CHOICE_ERROR']
            ]);
        }

        if(count($request->choice) < 2 )
        {
            return back()->withErrors([
                'message' => $Q['CHOICE_COUNT_ERR']
            ]);
        }

        if($request->question != $reviewquestion->question){
            $this->validate(request(), [
            'question'      =>  ['required','min:5','max:255','regex:/^[^<>]+$/'],
            ]);

            $rqs = Reviewquestion::where('question',$request->question)
                            ->where('conference_id', $conference->id)
                            ->count();

            if($rqs != 0){
                return back()->withErrors([
                    'message' => "Question already exist"
                ]);
            }
        }

        $this->validate(request(), [
            'question'      =>  ['required','min:5','max:255','regex:/^[^<>]+$/'],
            'public'      =>  ['required','numeric','min:0','max:1','regex:/^[^<>]+$/'],
            'choice.*'      =>  ['required','min:2','max:255','regex:/^[^<>]+$/'],
            'added_choice.*'      =>  ['required','min:2','max:255','regex:/^[^<>]+$/'],
            'position.*'    =>  ['required','numeric','min:1','max:5','regex:/^[^<>]+$/'],
            'added_position.*'    =>  ['required','numeric','min:1','max:5','regex:/^[^<>]+$/'],
            ]);

        $reviewquestion->question = $request->question;

        for ($i=0; $i < count($request->id_pc) ; $i++) {
          \DB::table('rqchoices')
              ->where('id', $request->id_pc[$i])
              ->update([
                'choice' => $request->choice[$i],
              ]);
        }
        $reviewquestion->save();
        $rqchoice =  \DB::table('rqchoices')->select('*')->where('reviewquestion_id' , '=' , $reviewquestion->id)->get();
        //return count($request->choice);
        if(count($request->added_choice != 0) ){
          for ($i=0; $i < count($request->added_choice) ; $i++) {
            \DB::table('rqchoices')->insert([
                'choice' => request('added_choice')[$i],
                'position' => request('added_position')[$i],
                'reviewquestion_id' => $reviewquestion->id,
            ]);
          }
        }

        return redirect('/conferences/'.$acronym.'/'.$edition.'/rquestions');
    }

    public function destroy($acronym,$edition,Reviewquestion $reviewquestion)
    {
             
        $reviewquestion->delete();
        \Session::flash('success_remove_rquestion','Review question removed successfully');
        return redirect()->back();
    }

    public function deleteChoice(Request $request,$acronym,$edition, $reviewquestion, $choice)
    {

      $rqchoice =  \DB::table('rqchoices')
                      ->select('*')
                      ->where('id', $choice)
                      ->delete();
      return redirect()->back();
    }

}
