<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Illuminate\Http\Request;
use App\User;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$lang = \App\Language::getCurrentLang();
        //$this->rediretTo = '/'.$lang;
        $this->middleware('guest', ['except' => 'logout']);
    }

    public function showLogForm(Request $request)
    {
        if ($request->session()->get('lang') == NULL){
            $request->session()->put('lang', 'en');
        }

        try {
        \DB::connection()->getPdo();
            } catch (\Exception $e) {              
                return view('errors.no_installation');
                die("Could not connect to the database.  Please check your configuration.");
            }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $this->validate(request(), [
            'email' =>  'required',
            'password'  =>  'required'
            ]);

        if (! auth()->attempt(request(['email' , 'password']))){
            return back()->withErrors([
                'message' => 'Please check your credentials and try again'
            ]);
        }

        if(auth()->user()->is_activated == 0){
            auth()->logout();
            return back()->withErrors([
                'message' => 'Please activate your account'
            ]);
        }
       

        return redirect('/');

    }


    public function logout(){
        auth()->logout();
        return redirect('/');
    }
}
