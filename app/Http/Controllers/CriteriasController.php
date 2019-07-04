<?php

namespace App\Http\Controllers;
use App\Http\Middleware\CheckIfAdmin;

use Illuminate\Http\Request;
use App\Conference;
use App\Criteria;
use Datatables;

class CriteriasController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
        $this->middleware(CheckIfAdmin::class);
    }

    public function getData($acronym,$edition, Request $request){
         $lang=$request->session()->get('lang');       
         $CR = parse_ini_file(base_path('language/'.$lang.'/CRITERIAS.ini'));

        $conference = \App\Conference::where('confAcronym' , '=' , $acronym)
                                        ->where('confEdition' , '=' , $edition)
                                        ->first();
        $GLOBALS['C'] = $conference;
        
        $GLOBALS['L'] = $lang;
        $GLOBALS['T'] = $CR;

        $criterias =  \App\Conference::where('confAcronym' , '=' , $acronym)->where('confEdition' , '=' , $edition)
                                        ->first()
                                        ->criterias;

        return Datatables::of($criterias)->addColumn('action', function ($cr) {
                    return '
                    
                        <form id="deleteTopicForm" action="/conferences/'.$GLOBALS['C']->confAcronym.'/'.$GLOBALS['C']->confEdition.'/criterias/'.$cr->id.'/delete" method = "POST">
                            <a href="/conferences/'.$GLOBALS['C']->confAcronym.'/'.$GLOBALS['C']->confEdition.'/criterias/'.$cr->id.'/edit" class="button ui primary"><i class="edit icon"></i>'.$GLOBALS['T']['BTN_EDIT'].'</a>
                            <button type="submit" class="button ui red"><i class="delete icon"></i>'.$GLOBALS['T']['BTN_DELETE'].'</a>
                        </form>';
                        //<a href="#show-'.$topic->id.'" class="button ui teal"><i class="unhide icon"></i>  Show</a>';
                },5)
                ->make(true);

}

    public function index($acronym,$edition,Request $request)
        {

            $conf = new Conference;
            $conference = $conf->getConference($acronym,$edition);

            return view('conferences.criterias.index',compact('conference'));
        }

    public function create($acronym,$edition){

        $conf = new Conference;
        $conference = $conf->getConference($acronym,$edition);
        
        return view('conferences.criterias.create',compact('conference'));
        
    }

    public function store($acronym,$edition,Request $request)
    {
        $lang=$request->session()->get('lang');       
        $CR = parse_ini_file(base_path('language/'.$lang.'/CRITERIAS.ini'));

        $conf = new Conference;
        $conference = $conf->getConference($acronym,$edition);
      

         //'unique:topics,label'
        $this->validate(request(), [
            'label'       =>  ['required','min:4','max:255','regex:/^[^<>]+$/'],
            'explanation' =>  ['required','min:5','regex:/^[^<>]+$/'],
            'weight'      =>  ['required','numeric','min:1','max:100','regex:/^[^<>]+$/'],

            ]);

            $crs = Criteria::where('label',$request->label)
                        ->where('conference_id', $conference->id)
                        ->count();
        
            if($crs != 0){
                return back()->withErrors([
                    'message' => "Criteria already exist"
                ]);
            }



        Criteria::create([
            'label'=>request('label'),
            'explanation'=>request('explanation'),
            'weight'=>request('weight'),
            'conference_id'=>request('conference_id'),          
        ]);

        return redirect('/conferences/'.$acronym.'/'.$edition.'/criterias');
        
    }

    public function edit($acronym,$edition,Criteria $criteria)
        {
            $conf = new Conference;
            $conference = $conf->getConference($acronym,$edition);

            return view('conferences.criterias.edit',compact('conference','acronym','criteria','edition'));
            
        }

    public function update($acronym,$edition, Criteria $criteria, Request $request)
    {
        $conf = new Conference;
        $conference = $conf->getConference($acronym,$edition);

        if($request->label != $criteria->label ){
            $this->validate(request(), [
                'label' =>  ['required','min:5','max:255','regex:/^[^<>]+$/']
                ]);

            $crs = Criteria::where('label',$request->label)
                        ->where('conference_id', $conference->id)
                        ->count();
        
            if($crs != 0){
                return back()->withErrors([
                    'message' => "Criteria already exist"
                ]);
            }

            $criteria->label=request('label');
            $criteria->save();
        }
        else{

            $this->validate(request(), [
                'explanation' =>  ['required','min:5','max:255','regex:/^[^<>]+$/'],
                'weight'      =>  ['required','numeric','min:1','max:100','regex:/^[^<>]+$/'],     
            ]);

            $criteria->explanation=request('explanation');
            $criteria->weight=request('weight');
            $criteria->save();
        }

        return redirect('/conferences/'.$acronym.'/'.$edition.'/criterias');
    }

    public function destroy($acronym,$edition,Criteria $criteria)
        {
            $criteria->delete();
            return redirect()->back();
        }


}
