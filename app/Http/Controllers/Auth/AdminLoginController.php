<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Session;

class AdminLoginController extends Controller
{
    public function __construct(){
        $this->middleware('guest:admin');
    }
    public function showLoginForm(Request $request)
    {

        if ($request->session()->get('lang') == NULL){
            $request->session()->put('lang', 'en');
        }

        try {
        \DB::connection()->getPdo();
            } catch (\Exception $e) {              
                return redirect()->route('install.create');
                die("Could not connect to the database.  Please check your configuration.");
            }

        return view('auth.admin-login');
        
    }

    public function login(Request $request)
    {

      $this->validate($request, [
        'email'   =>  'required|email',
        'password'=>  'required|min:6'
      ]);

      if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password], $request->remember)) {
				//return redirect()->intended(route('admin.dashboard'));
				return redirect(route('admin.dashboard.conferences'));
      }

			return redirect()->back()->withInput($request->only('email','remember'));


    }
}
