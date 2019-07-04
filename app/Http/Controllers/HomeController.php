<?php

namespace App\Http\Controllers;
use App\Http\Middleware\CheckIfReviewer;

use Illuminate\Http\Request;
use Session;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(CheckIfReviewer::class)->only('choose','storeChoose','storeEdit');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->session()->get('lang') == NULL){
            $request->session()->put('lang', 'en');
        }
        
        try {
        \DB::connection()->getPdo();
            } catch (\Exception $e) {              
                return view('errors.no_installation');
            }
    
        
        

        //$confs = \DB::table('conferences')->where('user_id', '=', auth()->id())->distinct('confAcronym')->get();
        $confsAdmin =auth()->user()->conferences->where('pivot.role','A');
        $confsAuthor =auth()->user()->conferences->where('pivot.role','Aut');
        $confsChair =auth()->user()->conferences->where('pivot.role','C');
        $confsRev =auth()->user()->conferences->where('pivot.role','R');
        
        /*$confs = \DB::table('conferences')
                    ->join('conference_user' , 'conferences.id' , 'conference_user.conference_id' )
                    ->join('users' , 'users.id' , '=' , 'conference_user.user_id')
                    ->where('user_id', '=' , auth()->id())
                    ->groupBy('confAcronym')
                    ->get();
        */
        //$MENU = parse_ini_file(base_path('language/fr/MENU.ini'));
        //return $confs;

        //return $confsAuthor;
        $requestChair = \DB::table('conferences')
                    ->join('comites' , 'conferences.id' , 'comites.conference_id' )
                    ->where('comites.email',auth()->user()->email)
                    ->where('comites.role','C')
                    ->where('comites.accept',0)
                    ->get();

        $requestRev = \DB::table('conferences')
                    ->join('comites' , 'conferences.id' , 'comites.conference_id' )
                    ->where('comites.email',auth()->user()->email)
                    ->where('comites.role','R')
                    ->where('comites.accept',0)
                    ->get();

        $topics = \DB::table('users')
                    ->join('topic_user' , 'users.id' ,'=', 'topic_user.user_id' )
                    ->join('topics' , 'topics.id' , '=' , 'topic_user.topic_id')
                    ->where('users.id',auth()->user()->id)
                    ->select('topics.id')
                    ->count();

        
        

        return view('home',compact('confsAdmin','confsAuthor','editions','requestChair','requestRev','confsChair','confsRev','topics'));
    }

    public function choose($acronym,$edition){

        $conference = \App\Conference::where('confAcronym' , '=' , $acronym)
                                        ->where('confEdition' , '=' , $edition)
                                        ->first();

        $topics = \App\Topic::where('conference_id',$conference->id)->get();

        $count_topics = \DB::table('users')
                            ->join('topic_user' , 'users.id' ,'=', 'topic_user.user_id' )
                            ->join('topics' , 'topics.id' , '=' , 'topic_user.topic_id')
                            ->where('users.id' , '=' , auth()->user()->id)
                            ->where('topics.conference_id',$conference->id)
                            ->groupBy('topics.id')
                            ->select('topics.*')
                            ->get();

                            //return $count_topics;

        if(count($count_topics) != 0){
            $query = \DB::table('topics');

            foreach($count_topics as $ctp){         
            $topics2 = $query->orWhere('id' , '!=' , $ctp->id)
                             ->where('conference_id',$conference->id);
            }

            $topics2 = $topics2->get();
        }

    
        return view('conferences.comite.chooseTopics',compact('conference','topics','count_topics','topics2'));
    }

    public function storeChoose(Request $request){


        $this->validate(request(), [
            'topics'     =>  ['required'],
            'topics.*'     =>  ['max:255','regex:/^[^<>]+$/']
        ]);

        foreach($request->topics as $topic){
                    \DB::table('topic_user')->insert([
                        'user_id'=>auth()->user()->id,
                        'topic_id'=>$topic
                    ]);
                }

        return redirect('/');
        
    }

    public function storeEdit($acronym,$edition,Request $request){

        $conference = \App\Conference::where('confAcronym' , '=' , $acronym)
                                        ->where('confEdition' , '=' , $edition)
                                        ->first();
        
        $this->validate(request(), [
            'topics'     =>  ['required'],
            'topics.*'     =>  ['max:255','regex:/^[^<>]+$/']
        ]);

        $confs = \DB::table('topic_user')
                    ->join('users' , 'users.id' , '=' , 'topic_user.user_id')
                    ->join('topics' , 'topics.id' , '=' , 'topic_user.topic_id')
                    ->join('conference_user' , 'users.id' , 'conference_user.user_id' )
                    ->join('conferences' , 'conference_user.conference_id' , 'conferences.id' )
                    ->where('conferences.id', '=' , $conference->id)
                    ->where('conference_user.role', '=' , 'R')
                    ->where('users.id', '=' , auth()->user()->id)
                    ->groupBy('topic_user.topic_id')
                    ->select('topic_user.*')
                    ->delete();

        $confs = \DB::table('topic_user')
                    ->join('users' , 'users.id' , '=' , 'topic_user.user_id')
                    ->join('topics' , 'topics.id' , '=' , 'topic_user.topic_id')
                    ->where('topics.conference_id', '=' , $conference->id)
                    ->where('users.id', '=' , auth()->user()->id)->delete();
        
        foreach($request->topics as $topic){
                    \DB::table('topic_user')->insert([
                        'user_id'=>auth()->user()->id,
                        'topic_id'=>$topic
                    ]);
                }
        return redirect('/');
    
    }


}
