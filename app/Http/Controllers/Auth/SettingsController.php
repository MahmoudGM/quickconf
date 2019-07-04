<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SettingsController extends Controller
{
  public function __construct(){
      $this->middleware('auth');
  }

  public function settings()
  {
    $user = auth()->user();
    return view('auth.settings', compact('user'));
  }

  public function store(Request $request)
  {
    $user = \App\User::find(auth()->user()->id);

    if($request->email != auth()->user()->email)
    {

      $this->validate(request(), [
          'first_name'                  =>  ['required','min:2','max:80','regex:/^[^<>]+$/'],
          'last_name'                   =>  ['required','max:80','regex:/^[^<>]+$/','string'],
          'country'                     =>  ['required','max:80','regex:/^[^<>]+$/','string'],
          'affilation'                  =>  ['required','max:80','regex:/^[^<>]+$/','string'],
          'email'                       =>  ['required','email','unique:users,email','min:4','max:255','regex:/^[^<>]+$/'],
      ]);

    }else {

      $this->validate(request(), [
          'first_name'                  =>  ['required','min:2','max:80','regex:/^[^<>]+$/'],
          'last_name'                   =>  ['required','max:80','regex:/^[^<>]+$/','string'],
          'country'                     =>  ['required','max:80','regex:/^[^<>]+$/','string'],
          'affilation'                  =>  ['required','max:80','regex:/^[^<>]+$/','string'],
          'email'                       =>  ['required','email','min:4','max:255','regex:/^[^<>]+$/'],

          ]);
    }


    if($request->password != ''){
      $this->validate(request(), [
            'password'                    =>  ['confirmed','min:6','max:80','regex:/^[^<>]+$/','string'],
          ]);
      $user->password=bcrypt($request->password);

    }

    $user->first_name=$request->first_name;
    $user->last_name=$request->last_name;
    $user->affilation=$request->affilation;
    $user->email=$request->email;
    $user->country=$request->country;

    $user->save();


    return redirect('/');






  }
}
