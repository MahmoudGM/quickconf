<?php

namespace App\Http\Controllers;
use App\Http\Middleware\CheckIfAdmin;

use Datatables;
use Illuminate\Http\Request;
use App\Conference;
use App\Ratelabel;

class RatelabelsController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
        $this->middleware(CheckIfAdmin::class);
    }

     public function getData($acronym,$edition, Request $request){
         $lang=$request->session()->get('lang');       
         $R = parse_ini_file(base_path('language/'.$lang.'/RATELABELS.ini'));

        $conference = \App\Conference::where('confAcronym' , '=' , $acronym)
                                        ->where('confEdition' , '=' , $edition)
                                        ->first();
        $GLOBALS['C'] = $conference;
        
        $GLOBALS['L'] = $lang;
        $GLOBALS['T'] = $R;
        $topics =  \App\Conference::where('confAcronym' , '=' , $acronym)->where('confEdition' , '=' , $edition)
                                        ->first()
                                        ->ratelabels;
        return Datatables::of($topics)->addColumn('action', function ($topic) {
                    return '
                    
                        <form id="deleteTopicForm" action="/conferences/'.$GLOBALS['C']->confAcronym.'/'.$GLOBALS['C']->confEdition.'/ratelabels/'.$topic->id.'/delete" method = "POST">
                            <a href="/conferences/'.$GLOBALS['C']->confAcronym.'/'.$GLOBALS['C']->confEdition.'/ratelabels/'.$topic->id.'/edit" class="button ui primary"><i class="edit icon"></i>'.$GLOBALS['T']['BTN_EDIT'].'</a>
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

        return view('conferences.ratelabels.index',compact('conference'));

    }

    public function create($acronym,$edition){
        $conf = new Conference;
        $conference = $conf->getConference($acronym,$edition);
        
        return view('conferences.ratelabels.create',compact('conference'));
        
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
        $R = parse_ini_file(base_path('language/'.$lang.'/RATELABELS.ini'));

        $conf = new Conference;
        $conference = $conf->getConference($acronym,$edition);
      
        $v = (count(request('label')) != count(array_unique(request('label'))));

         if($v){
             return back()->withErrors([
                'message' => $R['DUP_ERROR']
            ]);
         }
         //'unique:topics,label'
        $this->validate(request(), [
            'label.*' =>  ['required','min:2','max:255','regex:/^[^<>]+$/']

            ]);
        
        foreach($request->label as $label)
        {

            $topics = Ratelabel::where('label',$label)
                        ->where('conference_id', $conference->id)
                        ->count();
        
            if($topics != 0){
                return back()->withErrors([
                    'message' => "Ratelabel already exist"
                ]);
            }

        }
        

        


        $n = count(request('label'));
        for($i=0;$i<$n;$i++)
        {
        Ratelabel::create([
            'label'=>request('label')[$i],
            'conference_id'=>request('conference_id'),
            
            
        ]);
        }
        return redirect('/conferences/'.$acronym.'/'.$edition.'/ratelabels');
        
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($acronym,$edition,Ratelabel $ratelabel)
    {
        $conf = new Conference;
        $conference = $conf->getConference($acronym,$edition);

        return view('conferences.ratelabels.edit',compact('conference','acronym','ratelabel','edition'));
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($acronym,$edition, Ratelabel $ratelabel,Request $request)
    {

        $conf = new Conference;
        $conference = $conf->getConference($acronym,$edition);

        
        if($request->label != $ratelabel->label ){
            $this->validate(request(), [
                'label' =>  ['required','min:2','max:255','regex:/^[^<>]+$/']
                ]);

            $ratelabels = Ratelabel::where('label',$request->label)
                        ->where('conference_id', $conference->id)
                        ->count();
        
            if($ratelabels != 0){
                return back()->withErrors([
                    'message' => "Rate label already exist"
                ]);
            }

            $ratelabel->label=request('label');
            $ratelabel->save();
        }

        return redirect('/conferences/'.$acronym.'/'.$edition.'/ratelabels');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($acronym,$edition,Ratelabel $ratelabel)
    {
        $ratelabel->delete();
        return redirect()->back();
    }
}
