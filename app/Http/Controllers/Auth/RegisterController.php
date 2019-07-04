<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Session;
use Illuminate\Http\Request;
use DB;
use Mail;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
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
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'affilation' => 'required|max:255',
            'grade' => 'required|max:255',
            'country' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'affilation' => $data['affilation'],
            'grade' => $data['grade'],
            'country' => $data['country'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }

    public function showRegForm($lang,Request $request){

        if ($request->session()->get('lang') == NULL){
            $request->session()->put('lang', 'en');
        }

        $language = new \App\Language;
        $request->session()->put('lang', $lang);


        //return $req->session()->all();
        //return true;
        return view('auth.register');
    }


    public function registerUser(Request $request){

        
        $input = $request->all();
        $validator = $this->validator($input);

        if($validator->passes()){
            $user = $this->create($input)->toArray();
            $user['link'] = str_random(30);

            $users = new User;
            $userId = $users->orderBy('id', 'DESC')->first();

            $authors = \DB::table('papers')
                    ->join('authors','authors.paper_id','papers.id')
                    ->where('authors.is_corresponding',1)
                    ->select('papers.*','authors.email')
                    ->groupBy('papers.conference_id')
                    ->where('authors.email',$request->email)
                    ->get();

            foreach($authors as $aut){
                \DB::table('conference_user')->insert([
                    'user_id'=>$userId->id,
                    'role'=>'Aut',
                    'conference_id'=> $aut->conference_id
                ]);
            }

            $comiteC = \App\Comite::where('email',$request->email)
                                ->where('role','C')
                                ->where('accept',1)
                                ->get();

            if(count($comiteC) != 0){
                foreach($comiteC as $cc){
                \DB::table('conference_user')->insert([
                    'user_id'=>$userId->id,
                    'role'=>'C',
                    'conference_id'=> $cc->conference_id
                ]);
                }

                $comiteC = \App\Comite::where('email',$request->email)
                                ->where('role','C')
                                ->where('accept',1)
                                ->delete();


            }

            $comiteR = \App\Comite::where('email',$request->email)
                                ->where('role','R')
                                ->where('accept',1)
                                ->get();

            if(count($comiteR) != 0){
                foreach($comiteR as $cr){
                \DB::table('conference_user')->insert([
                    'user_id'=>$userId->id,
                    'role'=>'R',
                    'conference_id'=> $cr->conference_id
                ]);
                }

                if($cr->topics != null){

                $topics = explode('|',$cr->topics);

                foreach($topics as $topic){
                    \DB::table('topic_user')->insert([
                        'user_id'=>$userId->id,
                        'topic_id'=>$topic
                    ]);
                }

            }

            $comiteR = \App\Comite::where('email',$request->email)
                                ->where('role','R')
                                ->where('accept',1)
                                ->delete();

            }

            

            DB::table('user_activations')->insert([
                'user_id'   =>  $user['id'],
                'token'     =>  $user['link']
            ]);


            Mail::send('emails.activation',$user, function($message) use ($user){
                $message->to($user['email'])
                        ->subject('Account Activation');
            });
            Session::flash('success','We sent activation code. Please check your mail.');
            return redirect('login');
        }
        return redirect()->back()->with('errors',$validator->errors());
    }

    public function userActivation($token){
        $check = DB::table('user_activations')->where('token',$token)->first();
        if(!is_null($check)){
            $user = User::find($check->user_id);
            if ($user->is_activated == 1){
                redirect('login')->with('success',"user already activated");
            }
            DB::table('users')
                    ->where('id', $user['id'])
                    ->update(['is_activated' => 1]);
            DB::table('user_activations')->where('token',$token)->delete();
            return redirect('login')->with('success',"user active successfully ");
        }
        return redirect('login')->with('warning',"your token is invalid ");
    }
}
