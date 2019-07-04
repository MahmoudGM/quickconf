<?php

namespace App\Http\Controllers;
use App\Http\Middleware\CheckIfAdmin;

use Datatables;
use Illuminate\Http\Request;
use App\Conference;
use App\Paperstatus;

class PaperstatusController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
        $this->middleware(CheckIfAdmin::class);
    }

     public function getData($acronym,$edition, Request $request){
         $lang=$request->session()->get('lang');       
         $PS = parse_ini_file(base_path('language/'.$lang.'/P_STATUS.ini'));

        $conference = \App\Conference::where('confAcronym' , '=' , $acronym)
                                        ->where('confEdition' , '=' , $edition)
                                        ->first();
        $GLOBALS['C'] = $conference;
        
        $GLOBALS['L'] = $lang;
        $GLOBALS['T'] = $PS;
        $pstatuses =  \App\Conference::where('confAcronym' , '=' , $acronym)->where('confEdition' , '=' , $edition)
                                        ->first()
                                        ->paperstatuses;
        return Datatables::of($pstatuses)->addColumn('action', function ($ps) {
                    return '
                    
                        <form id="deleteTopicForm" action="/conferences/'.$GLOBALS['C']->confAcronym.'/'.$GLOBALS['C']->confEdition.'/paperstatus/'.$ps->id.'/delete" method = "POST">
                            <a href="/conferences/'.$GLOBALS['C']->confAcronym.'/'.$GLOBALS['C']->confEdition.'/paperstatus/'.$ps->id.'/edit" class="button ui primary"><i class="edit icon"></i>'.$GLOBALS['T']['BTN_EDIT'].'</a>
                            <button type="submit" class="button ui red"><i class="delete icon"></i>'.$GLOBALS['T']['BTN_DELETE'].'</a>
                        </form>';
                },5)
                ->addColumn('showMsg',function($ps){
                    return '<span id="'.$ps->id.'" href="#" class="button ui teal showMsg">'.$GLOBALS['T']['SHOW'].'</span>';
                })
                ->rawColumns(['showMsg','action'])
                ->editColumn('camReadyRequired',function($ps){
                    if($ps->camReadyRequired == 0)
                        return $GLOBALS['T']['N'];
                    else
                        return $GLOBALS['T']['Y'];                    
                })
                ->editColumn('accepted',function($ps){
                    if($ps->accepted == 0)
                        return $GLOBALS['T']['N'];
                    else
                        return $GLOBALS['T']['Y'];                    
                })
                ->make(true);

}


  public function index($acronym,$edition,Request $request)
    {
        $conf = new Conference;
        $conference = $conf->getConference($acronym,$edition);

        return view('conferences.paperstatus.index',compact('conference'));

    }

    public function create($acronym,$edition){
        $conf = new Conference;
        $conference = $conf->getConference($acronym,$edition);
        
        return view('conferences.paperstatus.create',compact('conference'));
        
    }

   


    public function store($acronym,$edition,Request $request)
    {
        $lang=$request->session()->get('lang');       
        $PS = parse_ini_file(base_path('language/'.$lang.'/P_STATUS.ini'));

        $conf = new Conference;
        $conference = $conf->getConference($acronym,$edition);
      
         //'unique:topics,label'
        $this->validate(request(), [
            'label' =>  ['required','min:2','max:255','regex:/^[^<>]+$/'],
            'msgTemplate' =>  ['required','min:10'],
            'camReady' =>  ['required','in:0,1'],
            'accepted' =>  ['required','in:0,1'],

            ]);
        
  
            $pss = Paperstatus::where('label',$request->label)
                        ->where('conference_id', $conference->id)
                        ->count();
        
            if($pss != 0){
                return back()->withErrors([
                    'message' => "Paper status already exist"
                ]);
            }



        $n = count(request('label'));
  
        Paperstatus::create([
            'label'=>request('label'),
            'msgTemplate'=>request('msgTemplate'),
            'camReadyRequired'=>request('camReady'),
            'accepted'=>request('accepted'),
            'conference_id'=>request('conference_id'),
            
            
        ]);

        return redirect('/conferences/'.$acronym.'/'.$edition.'/paperstatus');
        
    }

    public function edit($acronym,$edition,Paperstatus $pstatus)
    {

        $conf = new Conference;
        $conference = $conf->getConference($acronym,$edition);

  
        return view('conferences.paperstatus.edit',compact('conference','acronym','pstatus','edition'));
        
    }


    public function update($acronym,$edition, Paperstatus $pstatus,Request $request)
    {

        $conf = new Conference;
        $conference = $conf->getConference($acronym,$edition);

        if($request->label != $pstatus->label ){
            $this->validate(request(), [
                'label' =>  ['required','min:2','max:255','regex:/^[^<>]+$/'],
                'msgTemplate' =>  ['required','min:10'],
                'camReady' =>  ['required','in:0,1','regex:/^[^<>]+$/'],
                'accepted' =>  ['required','in:0,1','regex:/^[^<>]+$/'],
                ]);
            
            

            $pss = Paperstatus::where('label',$request->label)
                        ->where('conference_id', $conference->id)
                        ->count();
                        
            if($pss != 0){
                return back()->withErrors([
                    'message' => "Paper status already exist"
                ]);
            
            }

            $pstatus->label=request('label');
            $pstatus->msgTemplate=request('msgTemplate');
            $pstatus->camReadyRequired=request('camReady');
            $pstatus->accepted=request('accepted');
            $pstatus->save();
        }else{
            $this->validate(request(), [
                'camReady' =>  ['required','in:0,1','regex:/^[^<>]+$/'],
                'accepted' =>  ['required','in:0,1','regex:/^[^<>]+$/'],
                'msgTemplate' =>  ['required','min:10'],
                ]);
            
            $pstatus->camReadyRequired=request('camReady');
            $pstatus->accepted=request('accepted');
            $pstatus->msgTemplate=request('msgTemplate');
            $pstatus->save();
        }

        return redirect('/conferences/'.$acronym.'/'.$edition.'/paperstatus');
    }


    public function destroy($acronym,$edition,Paperstatus $pstatus)
    {
        $pstatus->delete();
        return redirect()->back();
    }
}
