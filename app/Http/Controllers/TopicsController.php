<?php

namespace App\Http\Controllers;
use App\Http\Middleware\CheckIfAdmin;

use Datatables;
use Illuminate\Http\Request;
use App\Conference;
use App\Topic;

class TopicsController extends Controller
{

    public function __construct(){
        $this->middleware('auth');
        $this->middleware(CheckIfAdmin::class);
    }

     public function getData($acronym,$edition, Request $request){
         $lang=$request->session()->get('lang');       
         $TOPIC = parse_ini_file(base_path('language/'.$lang.'/TOPICS.ini'));

        $conference = \App\Conference::where('confAcronym' , '=' , $acronym)
                                        ->where('confEdition' , '=' , $edition)
                                        ->first();
        $GLOBALS['C'] = $conference;
        
        $GLOBALS['L'] = $lang;
        $GLOBALS['T'] = $TOPIC;
        $topics =  \App\Conference::where('confAcronym' , '=' , $acronym)->where('confEdition' , '=' , $edition)
                                        ->first()
                                        ->topics;
        return Datatables::of($topics)->addColumn('action', function ($topic) {
                    return '
                    
                        <form id="deleteTopicForm" action="/conferences/'.$GLOBALS['C']->confAcronym.'/'.$GLOBALS['C']->confEdition.'/topics/'.$topic->id.'/delete" method = "POST">
                            <a href="/conferences/'.$GLOBALS['C']->confAcronym.'/'.$GLOBALS['C']->confEdition.'/topics/'.$topic->id.'/edit" class="button ui primary"><i class="edit icon"></i>'.$GLOBALS['T']['BTN_EDIT'].'</a>
                            <button type="submit" class="button ui red"><i class="delete icon"></i>'.$GLOBALS['T']['BTN_DELETE'].'</a>
                        </form>';
                        //<a href="#show-'.$topic->id.'" class="button ui teal"><i class="unhide icon"></i>  Show</a>';
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

        return view('conferences.topics.index',compact('conference'));

    }

    public function create($acronym,$edition){
        $conf = new Conference;
        $conference = $conf->getConference($acronym,$edition);
        
        return view('conferences.topics.create',compact('conference'));
        
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
        $TOPIC = parse_ini_file(base_path('language/'.$lang.'/TOPICS.ini'));

        $conf = new Conference;
        $conference = $conf->getConference($acronym,$edition);
      
        $v = (count(request('label')) != count(array_unique(request('label'))));
        $v1 = (count(request('acronym')) != count(array_unique(request('acronym'))));

         if($v or $v1){
             return back()->withErrors([
                'message' => $TOPIC['DUP_ERROR']
            ]);
         }
         //'unique:topics,label'
        $this->validate(request(), [
            'label.*' =>  ['required','min:5','max:255','regex:/^[^<>]+$/'],
            'acronym.*' =>  ['required','min:2','max:255','regex:/^[^<>]+$/'],
            /*'label.*' => Rule::unique('topics,label')->where(function ($query) {
                $query->where('conference_id', $conference->id);
            })*/

            ]);
        
        foreach($request->label as $label)
        {

            $topics = Topic::where('label',$label)
                        ->where('conference_id', $conference->id)
                        ->count();
        
            if($topics != 0){
                return back()->withErrors([
                    'message' => "Topic already exist"
                ]);
            }

        }

        foreach($request->acronym as $acronyme)
        {

            $acronyms = Topic::where('acronym',$acronyme)
                        ->where('conference_id', $conference->id)
                        ->count();
        
            if($acronyms != 0){
                return back()->withErrors([
                    'message' => "Topic already exist"
                ]);
            }

        }
        

        


        $n = count(request('label'));
        for($i=0;$i<$n;$i++)
        {
        Topic::create([
            'label'=>request('label')[$i],
            'acronym'=>request('acronym')[$i],
            'conference_id'=>request('conference_id'),
            
            
        ]);
        }
        return redirect('/conferences/'.$acronym.'/'.$edition.'/topics');
        
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($acronym,$edition,Topic $topic)
    {
        $conf = new Conference;
        $conference = $conf->getConference($acronym,$edition);

  
            return view('conferences.topics.edit',compact('conference','acronym','topic','edition'));
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($acronym,$edition, Topic $topic,Request $request)
    {

        $conf = new Conference;
        $conference = $conf->getConference($acronym,$edition);

        
        if($request->label != $topic->label ){
            $this->validate(request(), [
                'label' =>  ['required','min:5','max:255','regex:/^[^<>]+$/']
                ]);

            $topics = Topic::where('label',$request->label)
                        ->where('conference_id', $conference->id)
                        ->count();
        
            if($topics != 0){
                return back()->withErrors([
                    'message' => "Topic already exist"
                ]);
            }

            $topic->label=request('label');
            $topic->save();
        }

        if($request->acronym != $topic->acronym ){
            $this->validate(request(), [
                'acronym' =>  ['required','min:2','max:255','regex:/^[^<>]+$/']
                ]);

            $acronyms = Topic::where('acronym',$request->acronym)
                        ->where('conference_id', $conference->id)
                        ->count();
        
            if($acronyms != 0){
                return back()->withErrors([
                    'message' => "Topic already exist"
                ]);
            }

            $topic->acronym=request('acronym');
            $topic->save();
        }
        

        return redirect('/conferences/'.$acronym.'/'.$edition.'/topics');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($acronym,$edition,Topic $topic)
    {
        $topic->delete();
        return redirect()->back();
    }
}
