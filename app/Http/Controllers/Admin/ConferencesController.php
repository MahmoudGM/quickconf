<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Conference;
use Mail;
use App\Mail\Confactivated;
class ConferencesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $conferences = Conference::all();        
        return view('admin.conferences',compact('conferences'));
    }

    public function approve(Request $request){
        if ($request->confs == NULL){
            return back()->withErrors([
                'message' => 'Please select one/many conference'
            ]);
        }
        elseif(count($request->confs) == 1){
            $conference = \App\Conference::find($request->confs[0]);
            if($conference->is_activated == 1){
                return back()->withErrors([
                'message' => 'conference already approved'
            ]);
            }else{
                
                    $conf = \App\Conference::find($request->confs[0]);
                    $user = \App\User::where('email',$conf->chairMail)->first()->toArray();
                    $confArr = $conf->toArray();
                    $data = $confArr + $user;
                    Mail::to($user['email'])->send(new Confactivated($data));

                    \DB::table('conferences')
                    ->where('id',$request->confs[0] )
                    ->update(['is_activated' => 1]);
                    
                return back()->with('success' , 'conference approved and mail sended successffuly');
            }
        }else{
            for($i=0;$i<count($request->confs);$i++){
                 \DB::table('conferences')
                    ->where('id',$request->confs[$i] )
                    ->update(['is_activated' => 1]);
            }
            return back()->with('success' , 'conferences approved successffuly');
        }
    }

    public function delete(Request $request){
         if ($request->confs == NULL){
            return back()->withErrors([
                'message' => 'Please select one/many conference'
            ]);
        }elseif(count($request->confs) == 1){
            $conference = \App\Conference::find($request->confs[0]);
            $conference->delete();
            return back()->with('success' , 'conference deleted successffuly');
        }else{
            for($i=0;$i<count($request->confs);$i++){
                $conference = \App\Conference::find($request->confs[$i]);
                $conference->delete();
                return back()->with('success' , 'conferences deleted successffuly');
            }
            
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
