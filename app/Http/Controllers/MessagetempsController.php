<?php

namespace App\Http\Controllers;
use App\Http\Middleware\CheckIfAdmin;

use Datatables;
use Session;

use App\Messagetemp;
use App\Conference;
use Illuminate\Http\Request;

class MessagetempsController extends Controller
{

    public function __construct(){
        $this->middleware('auth');
        $this->middleware(CheckIfAdmin::class);

    }


    public function getData($acronym,$edition, Request $request){

        $lang=$request->session()->get('lang');       
        $MSG = parse_ini_file(base_path('language/'.$lang.'/MESSAGE_TEMP.ini'));
        $conference = \App\Conference::where('confAcronym' , '=' , $acronym)
                                        ->where('confEdition' , '=' , $edition)
                                        ->first();
        $GLOBALS['C'] = $conference;
        $GLOBALS['M'] = $MSG;
        //return $GLOBALS['C'];
        $messages =  \App\Conference::where('confAcronym' , '=' , $acronym)
                                        ->where('confEdition' , '=' , $edition)
                                        ->first()
                                        ->messages;
        
        $GLOBALS['L'] = $lang;
        return Datatables::of($messages)->addColumn('action', function ($msg) {
                    return '
                        <form id="formDeleteMsg" action="/conferences/'.$GLOBALS['C']->confAcronym.'/'.$GLOBALS['C']->confEdition.'/messages/'.$msg->id.'/delete" method = "POST">
                            <a id="'.$msg->id.'"  class="button ui teal show" data-tooltip="'.$GLOBALS['M']['BTN_SHOW'].'"> <i class="eye icon" ></i></a>
                            <a href="/conferences/'.$GLOBALS['C']->confAcronym.'/'.$GLOBALS['C']->confEdition.'/messages/'.$msg->id.'/edit" class="button ui primary" data-tooltip="'.$GLOBALS['M']['BTN_EDIT'].'"> <i class="edit icon" ></i></a>
                            <button type="submit" class="button ui red" data-tooltip="'.$GLOBALS['M']['BTN_DELETE'].'"><i class="delete icon"></i></a>
                        </form>';
                        //<a href="#show-'.$msg->id.'" class="button ui teal"><i class="unhide icon"></i>  Show</a>';
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


            $msgs = Messagetemp::where('conference_id', '=' ,$conference->id)->first();
            return view('conferences.messages.index',compact('conference','msgs'));

        
    }

    public function create($acronym,$edition){
         $conf = new Conference;
        $conference = $conf->getConference($acronym,$edition);


            return view('conferences.messages.create',compact('conference'));
    }

    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($acronym,$edition,Request $request)
    {
        $conf = new Conference;
        $conference = $conf->getConference($acronym,$edition);

        $this->validate(request(), [
            'name'        =>  ['required','min:4','max:255','regex:/^[^<>]+$/'],
            'title'       =>  ['required','min:4','max:255','regex:/^[^<>]+$/'],
            'body'        =>  ['required','min:5'],
            ]);
        
        Messagetemp::create([
            'name'=>request('name'),
            'title'=>request('title'),
            'body'=>request('body'),
            'conference_id'=>$conference->id,
            
            
        ]);
        Session::flash('success_add_message','Message added successfully');
        return redirect()->back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Messagetemp  $messagetemp
     * @return \Illuminate\Http\Response
     */
    public function edit($acronym,$edition, Messagetemp $messagetemp)
    {
        $conf = new Conference;
        $conference = $conf->getConference($acronym,$edition);

        return view('conferences.messages.edit',compact('conference','messagetemp','acronym','edition'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Messagetemp  $messagetemp
     * @return \Illuminate\Http\Response
     */
    public function update($acronym,$edition, Messagetemp $messagetemp,Request $request)
    {
        $messagetemp->name=request('name');
        $messagetemp->title=request('title');
        $messagetemp->body=request('body');

        $messagetemp->save();
        $lang=$request->session()->get('lang');
        return redirect('/conferences/'.$acronym.'/'.$edition.'/messages');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Messagetemp  $messagetemp
     * @return \Illuminate\Http\Response
     */
    public function destroy($acronym,$edition, Messagetemp $messagetemp)
    {
        $messagetemp->delete();
        Session::flash('success_remove_message','Message removed successfully');
        return redirect()->back();
    }
}
