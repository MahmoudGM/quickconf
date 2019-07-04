<?php

namespace App\Http\Controllers;
use Datatables;
use App\Conference;
use App\Paperquestion;
use Illuminate\Http\Request;
use App\Http\Middleware\CheckIfAdmin;

class PaperquestionsController extends Controller
{

    public function __construct(){
        $this->middleware('auth');
        $this->middleware(CheckIfAdmin::class);
    }


    public function getData($acronym,$edition,Request $request){
        $lang=$request->session()->get('lang'); 
        $Q = parse_ini_file(base_path('language/'.$lang.'/PQUESTIONS.ini'));
        
        $conf = new Conference;
        $conference = $conf->getConference($acronym,$edition);

        $GLOBALS['C'] = $conference;
        //return $GLOBALS['C'];
        $pquestions =  \App\Conference::where('confAcronym' , '=' , $acronym)
                                        ->where('confEdition' , '=' , $edition)
                                        ->first()
                                        ->pquestions;
        
        $GLOBALS['L'] = $lang;
        $GLOBALS['Q'] = $Q;
        return Datatables::of($pquestions)->addColumn('action', function ($pq) {
                    return '

                        <form id="deletePqForm" action="/conferences/'.$GLOBALS['C']->confAcronym.'/'.$GLOBALS['C']->confEdition.'/pquestions/'.$pq->id.'/delete" method = "POST">
                            <a href="/conferences/'.$GLOBALS['C']->confAcronym.'/'.$GLOBALS['C']->confEdition.'/pquestions/'.$pq->id.'/edit" class="button ui primary"><i class="edit icon"></i>  '.$GLOBALS['Q']['BTN_EDIT'].'</a>
                            <button type="submit" class="button ui red"><i class="delete icon"></i>  '.$GLOBALS['Q']['BTN_DELETE'].'</a>
                        </form>';
                        //<a href="#show-'.$msg->id.'" class="button ui teal"><i class="unhide icon"></i>  Show</a>';
                },5)->addColumn('choices',function($pq){
                    $choices = \DB::table('pqchoices')->select('choice')->where('paperquestion_id',$pq->id)->get();
                    
                    return $choices;

                },5)
                ->make(true);

}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
  public function index($acronym,$edition,Request $request)
    {
        $conf = new Conference;
        $conference = $conf->getConference($acronym,$edition);

        $pqs = Paperquestion::where('conference_id', '=' ,$conference->id)->first();
        return view('conferences.pquestions.index',compact('conference','pqs'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($acronym,$edition)
    {
        $conf = new Conference;
        $conference = $conf->getConference($acronym,$edition);

        return view('conferences.pquestions.create',compact('conference'));
 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($acronym,$edition,Request $request)
    {
        $lang=$request->session()->get('lang');       
        $PQ = parse_ini_file(base_path('language/'.$lang.'/PQUESTIONS.ini'));

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
                'message' => $PQ['POS_EROOR']
            ]);
        }
        if($v){
            return back()->withErrors([
                'message' => $PQ['CHOICE_ERROR']
            ]);
        }
        if(count($request->choice) < 2 )
        {
            return back()->withErrors([
                'message' => $PQ['CHOICE_COUNT_ERR']
            ]);
        }
             
        $this->validate(request(), [
            'question'      =>  ['required','min:5','max:255','regex:/^[^<>]+$/'],
            'required'      =>  ['required','numeric','min:0','max:1','regex:/^[^<>]+$/'],
            'choice.*'      =>  ['required','min:2','max:255','regex:/^[^<>]+$/'],
            'position.*'    =>  ['required','numeric','min:1','max:5','regex:/^[^<>]+$/'],
            ]);

        $pqs = Paperquestion::where('question',$request->question)
                            ->where('conference_id', $conference->id)
                            ->count();
        
        if($pqs != 0){
            return back()->withErrors([
                'message' => "Question already exist"
            ]);
        }

        Paperquestion::create([
            'question'=>request('question'),
            'required'=>request('required'),
            'conference_id'=>request('conference_id'),
        ]);

        $n = count(request('choice'));

        $pq = new Paperquestion;
        $pqId = $pq->orderBy('id', 'DESC')->first();


        for($i=0;$i<$n;$i++)
        {
            \DB::table('pqchoices')->insert([
                'choice' => request('choice')[$i],
                'position' => request('position')[$i],
                'paperquestion_id' => $pqId->id,
            ]);
        }
        return redirect('/conferences/'.$acronym.'/'.$edition.'/pquestions');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Paperquestion  $paperquestion
     * @return \Illuminate\Http\Response
     */
    public function show(Paperquestion $paperquestion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Paperquestion  $paperquestion
     * @return \Illuminate\Http\Response
     */
    public function edit($acronym,$edition, Paperquestion $paperquestion)
    {
        $conf = new Conference;
        $conference = $conf->getConference($acronym,$edition);

        $pquestion = $paperquestion;
        $pqchoice =  \DB::table('pqchoices')->select('*')->where('paperquestion_id' , '=' , $pquestion->id)->get();
        return view('conferences.pquestions.edit',compact('conference','pquestion','acronym','edition','pqchoice'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Paperquestion  $paperquestion
     * @return \Illuminate\Http\Response
     */
    public function update($acronym,$edition, Paperquestion $paperquestion,Request $request)
    {
        
        $conf = new Conference;
        $conference = $conf->getConference($acronym,$edition);

        $lang=$request->session()->get('lang');       
        $PQ = parse_ini_file(base_path('language/'.$lang.'/PQUESTIONS.ini'));



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
                'message' => $PQ['POS_ERROR']
            ]);
        }
        if($v){
            return back()->withErrors([
                'message' => $PQ['CHOICE_ERROR']
            ]);
        }

        if(count($request->choice) < 2 )
        {
            return back()->withErrors([
                'message' => $PQ['CHOICE_COUNT_ERR']
            ]);
        }

        if($request->question != $paperquestion->question){
            $this->validate(request(), [
            'question'      =>  ['required','min:5','max:255','regex:/^[^<>]+$/'],
            ]);


            $pqs = Paperquestion::where('question',$request->question)
                            ->where('conference_id', $conference->id)
                            ->count();
        
        
            if($pqs != 0){
                return back()->withErrors([
                    'message' => "Question already exist"
                ]);
            }

        }

        $this->validate(request(), [
            'question'      =>  ['required','min:5','max:255','regex:/^[^<>]+$/'],
            'required'      =>  ['required','numeric','min:0','max:1','regex:/^[^<>]+$/'],
            'choice.*'      =>  ['required','min:2','max:255','regex:/^[^<>]+$/'],
            'added_choice.*'      =>  ['required','min:2','max:255','regex:/^[^<>]+$/'],
            'position.*'    =>  ['required','numeric','min:1','max:5','regex:/^[^<>]+$/'],
            'added_position.*'    =>  ['required','numeric','min:1','max:5','regex:/^[^<>]+$/'],
            ]);

        $paperquestion->question = $request->question;

        for ($i=0; $i < count($request->id_pc) ; $i++) {
          \DB::table('pqchoices')
              ->where('id', $request->id_pc[$i])
              ->update([
                'choice' => $request->choice[$i],
              ]);
        }
        $paperquestion->save();
        $pqchoice =  \DB::table('pqchoices')->select('*')->where('paperquestion_id' , '=' , $paperquestion->id)->get();
        //return count($request->choice);
        if(count($request->added_choice != 0) ){
          for ($i=0; $i < count($request->added_choice) ; $i++) {
            \DB::table('pqchoices')->insert([
                'choice' => request('added_choice')[$i],
                'position' => request('added_position')[$i],
                'paperquestion_id' => $paperquestion->id,
            ]);
          }
        }

        return redirect('/conferences/'.$acronym.'/'.$edition.'/pquestions');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Paperquestion  $paperquestion
     * @return \Illuminate\Http\Response
     */
    public function destroy($acronym,$edition,Paperquestion $paperquestion)
    {
             
        $paperquestion->delete();
        \Session::flash('success_remove_pquestion','Paper question removed successfully');
        return redirect()->back();
    }

    public function deleteChoice(Request $request,$acronym,$edition, $paperquestion, $choice)
    {

      $pqchoice =  \DB::table('pqchoices')
                      ->select('*')
                      ->where('id', $choice)
                      ->delete();
      return redirect()->back();
    }
}
