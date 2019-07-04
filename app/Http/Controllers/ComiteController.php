<?php

namespace App\Http\Controllers;
use App\Http\Middleware\CheckIfAdmin;
use App\Http\Middleware\CheckIfAdminChair;
use App\Http\Middleware\CheckIfReviewer;
use Illuminate\Support\Facades\Crypt;


use App\Mail\ReviewSuccessRev;
use App\Mail\ReviewSuccessAdmin;



use Illuminate\Http\Request;
use App\Conference;
use Datatables;
use App\user;
use Mail;
use App\Mail\RequestForComite;

class ComiteController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
        $this->middleware(CheckIfAdmin::class)->except('accept','decline','getPapersRevs','myPapers','review','storeReview','updateReview','assignStatus','getAssignStatus','showReview','showReviewJson','storeAssignStatus','deleteReviewer','rate','storeRate','showRate');
        $this->middleware(CheckIfReviewer::class)->only('getPapersRevs','myPapers','review','storeReview','updateReview','rate','storeRate');
        $this->middleware(CheckIfAdminChair::class)->only('assignStatus','storeAssignStatus','getAssignStatus','showReview','showReviewJson','deleteReviewer','showRate');

    }

    public function getData($acronym,$edition){
        $conference = \App\Conference::where('confAcronym' , '=' , $acronym)
                                        ->where('confEdition' , '=' , $edition)
                                        ->first();
        $GLOBALS['C'] = $conference;
        //return $GLOBALS['C'];
        /*$papers =  \App\Conference::where('confAcronym' , '=' , $acronym)
                                        ->where('confEdition' , '=' , $edition)
                                        ->first()
                                        ->papers;
        */
        $comites = \DB::table('conferences')
                    ->join('conference_user' , 'conferences.id' ,'=', 'conference_user.conference_id' )
                    ->join('users' , 'users.id' , '=' , 'conference_user.user_id')
                    ->where('conference_user.conference_id' , '=' , $conference->id)
                    
                    ->where(function($query){
                        $query->orWhere('conference_user.role' , '=' , 'R')
                              ->orWhere('conference_user.role' , '=' , 'C');
                    })

                    ->select('users.*','conference_user.role')
                    ->get();
                    //->unique('papers.id');

        //$topics = $papers->topics;
        //return $papers;            
        return Datatables::of($comites)->addColumn('action', function ($c) {
                    if($c->role == 'R')
                    return '
                        <form id="formDeletePaper" action="/conferences/'.$GLOBALS['C']->confAcronym.'/'.$GLOBALS['C']->confEdition.'/papers/'.$c->id.'/delete" method = "POST">
                           
                            <a href="/conferences/'.$GLOBALS['C']->confAcronym.'/'.$GLOBALS['C']->confEdition.'/committee/assign/'.$c->id.'" class="button ui teal" data-tooltip="assign to paper"><i class="add user icon"></i></a>
                            <a href="/conferences/'.$GLOBALS['C']->confAcronym.'/'.$GLOBALS['C']->confEdition.'/committee/assign/'.$c->id.'" class="button ui teal" data-tooltip="send instructions"><i class="talk icon"></i></a>
                            <button id="btnDeletePaper" type="submit" class="button ui red" data-tooltip="delete"><i class="delete icon" ></i></button>
                        </form>
                        <input type="hidden" value="'.$c->email.'" class="mailInput" id="'.$c->id.'" name="idForMail[]"> 
                        ';

                    else{
                        return '
                        <form id="formDeletePaper" action="/conferences/'.$GLOBALS['C']->confAcronym.'/'.$GLOBALS['C']->confEdition.'/papers/'.$c->id.'/delete" method = "POST">
                           
                            <a href="/conferences/'.$GLOBALS['C']->confAcronym.'/'.$GLOBALS['C']->confEdition.'/committee/assign/'.$c->id.'" class="button ui teal" data-tooltip="send instructions"><i class="talk icon"></i></a>
                            
                            <button id="btnDeletePaper" type="submit" class="button ui red" data-tooltip="delete"><i class="delete icon"></i></button>
                        </form>
                        <input type="hidden" value="'.$c->email.'" class="mailInput" id="'.$c->id.'" name="idForMail[]"> 
                        ';


                    }

                },5)
                ->addColumn('nbr_papers',function($c){
                    if($c->role=='R'){

                    $nbr =  \DB::table('paper_user')
                                    ->join('papers' , 'papers.id' ,'=', 'paper_user.paper_id' )
                                    ->join('users' , 'users.id' , '=' , 'paper_user.user_id')
                                    ->where('papers.conference_id',$GLOBALS['C']->id)
                                    ->where('users.id',$c->id)
                                    ->count();

                    return '<a href="/conferences/'.$GLOBALS['C']->confAcronym.'/'.$GLOBALS['C']->confEdition.'/reviewer/'.$c->id.'/papers">'.$nbr.'</a>';

                    }


                                    
                })
                ->addColumn('topics',function($c){

                    $topics  = \DB::table('users')
                            ->join('topic_user' , 'users.id' ,'=', 'topic_user.user_id' )
                            ->join('topics' , 'topics.id' , '=' , 'topic_user.topic_id')
                            ->where('users.id',$c->id)
                            ->select('topics.acronym')
                            ->get()->toArray();
                     
                     $topicArray=[] ;
                     foreach ($topics as $topic){
                         $topicArray[] = $topic->acronym;
                     }
                     return implode(',',$topicArray);
                    
                })
                ->rawColumns(['nbr_papers','action'])
                ->make(true);

}

public function getPapersRevs($acronym,$edition, Request $request){

        $lang=$request->session()->get('lang');
        $CM = parse_ini_file(base_path('language/'.$lang.'/COMITE.ini'));

        $conference = \App\Conference::where('confAcronym' , '=' , $acronym)
                                        ->where('confEdition' , '=' , $edition)
                                        ->first();

        $GLOBALS['C'] = $conference;
        $GLOBALS['M'] = $CM;

        



        $papers = \DB::table('papers')
                    ->join('paper_topic' , 'papers.id' ,'=', 'paper_topic.paper_id' )
                    ->join('topics' , 'topics.id' , '=' , 'paper_topic.topic_id')
                    ->join('paper_user' , 'papers.id' ,'=', 'paper_user.paper_id' )
                    ->join('users' , 'users.id' , '=' , 'paper_user.user_id')
                    ->where('users.id' , '=' , auth()->user()->id)
                    ->where('papers.conference_id' , '=' , $conference->id)
                    ->select('topics.*','users.*','papers.*','users.id as revId','paper_user.is_reviewed')
                    ->groupBy('papers.id')
                    ->orderBy('papers.created_at','DESC')
                    ->get();
        

        $GLOBALS['L'] = $lang;
        return Datatables::of($papers)->addColumn('action', function ($paper) {

                        if($paper->is_reviewed == 0){
                            return '    <a href="/conferences/'.$GLOBALS['C']->confAcronym.'/'.$GLOBALS['C']->confEdition.'/papers/'.$paper->id.'" class="button ui green" data-tooltip="show paper" data-inverted><i class="eye icon"></i></a>
                                    <a href="/conferences/'.$GLOBALS['C']->confAcronym.'/'.$GLOBALS['C']->confEdition.'/papers/'.$paper->id.'/review" class="button ui teal" data-inverted data-tooltip="review"><i class="empty star icon"></i></a>
                                    <input type="hidden" value="'.$paper->id.'" class="zipInput" id="'.$paper->id.'" name="idForZip[]"> 

                                ';
                        }else{
                            return '    <a href="/conferences/'.$GLOBALS['C']->confAcronym.'/'.$GLOBALS['C']->confEdition.'/papers/'.$paper->id.'" data-tooltip="show paper" data-inverted  class="button ui green"><i class="eye icon"></i></a>
                                    <a href="/conferences/'.$GLOBALS['C']->confAcronym.'/'.$GLOBALS['C']->confEdition.'/papers/'.$paper->id.'/review" class="button ui teal" data-inverted data-tooltip="edit review"><i class="edit icon"></i><i class="empty star icon"></i></a>
                                    <input type="hidden" value="'.$paper->id.'" class="zipInput" id="'.$paper->id.'" name="idForZip[]"> 

                                ';
                        }

                        

                },5)
                ->make(true);

}


    public function index($acronym, $edition)
    {
        $conf = new Conference;
        $conference = $conf->getConference($acronym,$edition);


        return view('conferences.comite.index',compact('conference'));
    }

    public function send($acronym,$edition,Request $request){

        $this->validate(request(), [
            'role'                    =>  ['in:C,R','regex:/^[^<>]+$/'],
            'email'                   =>  ['required','email','regex:/^[^<>]+$/']
            ]);

        $conf = new Conference;
        $conference = $conf->getConference($acronym,$edition);
        
        $user = \App\User::where('email',$request->email)->first();

        if(count($user) != 0){

            $admin = \DB::table('conferences')
                    ->join('conference_user' , 'conferences.id' , 'conference_user.conference_id' )
                    ->join('users' , 'users.id' , '=' , 'conference_user.user_id')
                    ->where('users.email',$request->email)
                    ->where('conference_user.conference_id',$conference->id)
                    ->where('conference_user.role','A')
                    ->get();

            $chair = \DB::table('conferences')
                    ->join('conference_user' , 'conferences.id' , 'conference_user.conference_id' )
                    ->join('users' , 'users.id' , '=' , 'conference_user.user_id')
                    ->where('users.email',$request->email)
                    ->where('conference_user.conference_id',$conference->id)
                    ->where('conference_user.role','C')
                    ->get();

            $rev = \DB::table('conferences')
                    ->join('conference_user' , 'conferences.id' , 'conference_user.conference_id' )
                    ->join('users' , 'users.id' , '=' , 'conference_user.user_id')
                    ->where('users.email',$request->email)
                    ->where('conference_user.conference_id',$conference->id)
                    ->where('conference_user.role','R')
                    ->get();
                
                if(count($admin) != 0)
                    return back()->withErrors([
                                'message' => "user already admin"
                            ]);

                if(count($chair)!=0 )
                    return back()->withErrors([
                                'message' => "chair already exist"
                            ]);

                if(count($rev)!=0 )
                    return back()->withErrors([
                                'message' => "reviewer already exist"
                            ]);
        }

            $comite = \App\Comite::where('email',$request->email)
                                ->where('role',$request->role)
                                ->where('conference_id',$conference->id)
                                ->count();
            
            if($comite != 0){
                return back()->withErrors([
                            'message' => "member already added"
                        ]);
            }


        \App\Comite::create([
                    'email' => $request->email,
                    'role'  => $request->role,
                    'conference_id'=>$conference->id,
                ]);
        
        $data = $conference->toArray()+$user->toArray();
        $data['role'] = $request->role;
        Mail::to($user['email'])->send(new RequestForComite($data));

        return back();

    }

    public function add($acronym,$edition,Request $request){


        $this->validate(request(), [
            'role'                  =>  ['in:C,R','regex:/^[^<>]+$/'],
            'topics.*'              =>  ['regex:/^[^<>]+$/'],
            'first'                 =>  ['required','min:2','max:80','regex:/^[^<>]+$/'],
            'last'                  =>  ['required','min:2','max:80','regex:/^[^<>]+$/'],
            'aff'                   =>  ['required','min:2','max:80','regex:/^[^<>]+$/'],
            'grade'                 =>  ['required','min:1','max:80',
                                        'in:Teaching Assistant,PhD Candidate,Dr.,Master student,Assoc. Prof. Dr.,Prof,Professional',
                                        'regex:/^[^<>]+$/'],
            'country'               =>  ['required','min:2','max:80','regex:/^[^<>]+$/'],
            'email'                 =>  ['required','email','min:4','max:80','regex:/^[^<>]+$/'],
            
            ]);

        $conf = new Conference;
        $conference = $conf->getConference($acronym,$edition);

        $user = \App\User::where('email',$request->email)->first();

        if(count($user) == 1 ){

            if($request->role == 'C')
            {
                $admin = \DB::table('conferences')
                    ->join('conference_user' , 'conferences.id' , 'conference_user.conference_id' )
                    ->join('users' , 'users.id' , '=' , 'conference_user.user_id')
                    ->where('users.email',$request->email)
                    ->where('conference_user.conference_id',$conference->id)
                    ->where('conference_user.role','A')
                    ->get();

                $chair = \DB::table('conferences')
                    ->join('conference_user' , 'conferences.id' , 'conference_user.conference_id' )
                    ->join('users' , 'users.id' , '=' , 'conference_user.user_id')
                    ->where('users.email',$request->email)
                    ->where('conference_user.conference_id',$conference->id)
                    ->where('conference_user.role','C')
                    ->get();
                
                if(count($admin) != 0)
                    return back()->withErrors([
                                'message' => "user already admin"
                            ]);

                if(count($chair)!=0 )
                    return back()->withErrors([
                                'message' => "chair already exist"
                            ]);
                
                \DB::table('conference_user')->insert([
                    'user_id'=>$user->id,
                    'role'=>'C',
                    'conference_id'=> $conference->id

                ]);

                

            }else{


                $rev = \DB::table('conferences')
                    ->join('conference_user' , 'conferences.id' , 'conference_user.conference_id' )
                    ->join('users' , 'users.id' , '=' , 'conference_user.user_id')
                    ->where('users.email',$request->email)
                    ->where('conference_user.conference_id',$conference->id)
                    ->where('conference_user.role','R')
                    ->get();

                if(count($rev) != 0)
                return back()->withErrors([
                            'message' => "reviewer already exist"
                        ]);

                \DB::table('conference_user')->insert([
                    'user_id'=>$user->id,
                    'role'=>'R',
                    'conference_id'=> $conference->id

                ]);

                foreach($request->topics as $topic){
                    \DB::table('topic_user')->insert([
                        'user_id'=>$user->id,
                        'topic_id'=>$topic
                    ]);
                }

            }

            
        }else{

            $comite = \App\Comite::where('email',$request->email)
                                ->where('role',$request->role)
                                ->where('conference_id',$conference->id)
                                ->count();
            
            if($comite != 0){
                return back()->withErrors([
                            'message' => "member already added"
                        ]);
            }

            $password=str_random(8);

                \App\User::create([
                    'first_name' => $request->first,
                    'last_name' => $request->last,
                    'affilation' => $request->aff,
                    'grade' => $request->grade,
                    'email' => $request->email,
                    'country' => $request->country,
                    'password' => bcrypt($password),
                    'is_activated' => $request->is_activated,
                ]);
            
            $users = new User;
            $userId = $users->orderBy('id', 'DESC')->first();

                \DB::table('conference_user')->insert([
                    'user_id'=>$userId->id,
                    'role'=>$request->role,
                    'conference_id'=> $conference->id

                ]);


            if($request->role == 'R'){
                if(count($request->topics) != 0){
                    foreach($request->topics as $topic){
                        \DB::table('topic_user')->insert([
                            'user_id'=>$userId->id,
                            'topic_id'=>$topic
                        ]);
                    }
            }
                
            }
                

        }

        return back();

    }

    public function accept($acronym,$edition,$role,Request $request){

        $conference = \App\Conference::where('confAcronym' , '=' , $acronym)
                                        ->where('confEdition' , '=' , $edition)
                                        ->first();

        $comite = \App\Comite::where('email',auth()->user()->email)
                                ->where('role',$request->role)
                                ->where('conference_id',$conference->id);
        if ($comite->count() == 0)
            return back();

        
            \DB::table('conference_user')->insert([
                    'user_id'=>auth()->user()->id,
                    'role'=>$request->role,
                    'conference_id'=> $conference->id

                ]);
    

        $comite->delete();

        
        
        return back();
        
    }

    public function decline($acronym,$edition,$role,Request $request){

        $conference = \App\Conference::where('confAcronym' , '=' , $acronym)
                                        ->where('confEdition' , '=' , $edition)
                                        ->first();

                                        


        $comite = \App\Comite::where('email',auth()->user()->email)
                                ->where('role',$request->role)
                                ->where('conference_id',$conference->id);
        
        if ($comite->count() == 0)
            return back();
            
        $comite->delete();

        
        
        return back();

    }

    public function assign($acronym,$edition,User $user){


        $conf = new Conference;
        $conference = $conf->getConference($acronym,$edition);

        $users = \DB::table('users')
                    ->join('topic_user' , 'users.id' ,'=', 'topic_user.user_id' )
                    ->join('topics' , 'topics.id' , '=' , 'topic_user.topic_id')
                    ->where('users.id',$user->id)
                    ->select('topics.id')
                    ->get();
        
         $idUsers= $users->pluck('id')->all();



        $assigned = \DB::table('paper_user')
                    ->join('papers' , 'papers.id' ,'=', 'paper_user.paper_id' )
                    ->join('users' , 'users.id' , '=' , 'paper_user.user_id')
                    ->where('papers.conference_id',$conference->id)
                    ->where('users.id',$user->id)
                    ->count();


        $conflictPapers = \DB::table('papers')
                        ->join('authors' , 'papers.id' , '=' , 'authors.paper_id')
                        //->where('authors.is_corresponding',1)
                        ->where('papers.conference_id',$conference->id)
                        ->where(function($query) use ($user){
                                $query->where('authors.first_name',$user->first_name)
                                     ->where('authors.last_name',$user->last_name)
                                     ->orWhere('authors.affilation',$user->affilation);
                            })
                        ->select('papers.*')
                        ->get();
                        //return $conflictPapers;
         /*$conflictPapers = \DB::select('select authors.*,papers.* FROM papers
                                        INNER JOIN authors ON papers.id = authors.paper_id
                                        WHERE ( papers.conference_id = :confId
                                           AND authors.is_corresponding = 1 )
                                           OR ( authors.first_name = :first
                                           AND authors.last_name = :last)
                                           OR (authors.affilation = :aff )',
                                           ['confId' => $conference->id,
                                           'first' => $user->first_name,
                                           'last' => $user->last_name,
                                           'aff' => $user->affilation,
                                           ]);
*/

        
        $idsC = $conflictPapers->pluck('id')->all();

        $papers1 = \DB::table('papers')
                    ->join('paper_topic' , 'papers.id' ,'=', 'paper_topic.paper_id' )
                    ->join('topics' , 'topics.id' , '=' , 'paper_topic.topic_id')
                    ->whereIn('topics.id' , $idUsers)
                    ->whereNotIn('papers.id',$idsC)
                    ->where('papers.conference_id',$conference->id)
                    ->groupBy('papers.id')
                    ->select('papers.*','topics.id as tpId')
                    ->get();

        $idsP1 = $papers1->pluck('id')->all();

        $papers2 = \DB::table('papers')
                ->join('paper_topic' , 'papers.id' ,'=', 'paper_topic.paper_id' )
                ->join('topics' , 'topics.id' , '=' , 'paper_topic.topic_id')
                ->whereNotIn('papers.id',$idsC)
                ->whereNotIn('papers.id',$idsP1)
                ->where('papers.conference_id',$conference->id)
                ->groupBy('papers.id')
                ->select('papers.*','topics.id as tpId')
                ->get();
                    
       

       /* foreach($papers1 as $p){
            $papers2 = $query->where('papers.id','!=',$p->id)
                                ->where('papers.conference_id',$conference->id)
                                ->groupBy('papers.id')
                                ->select('papers.*','topics.id as tpId');
        }

        if(count($papers1) != 0){
            $papers2 = $papers2->get();

        }else{
            $papers2 =\DB::table('papers')
                    ->join('paper_topic' , 'papers.id' ,'=', 'paper_topic.paper_id' )
                    ->join('topics' , 'topics.id' , '=' , 'paper_topic.topic_id')
                    ->where('papers.conference_id',$conference->id)
                    ->groupBy('papers.id')
                    ->select('papers.*','topics.id as tpId')->get();
        }*/

       

        return view('conferences.comite.assign',compact('conference','papers1','papers2','user','assigned','conflictPapers'));

    }

    public function storeAssign($acronym,$edition,User $user,Request $request){

        $conf = new Conference;
        $conference = $conf->getConference($acronym,$edition);

        $this->validate(request(), [
            'papers'     =>  ['required'],
            'papers.*'     =>  ['max:255','regex:/^[^<>]+$/']
        ]);

        if($request->assigned != 0){
            \DB::table('paper_user')->where('paper_user.user_id' , '=' , $user->id)->delete();                      
        }


        foreach($request->papers as $paper){

             $count_revs = \DB::table('users')
                            ->join('paper_user' , 'users.id' ,'=', 'paper_user.user_id' )
                            ->join('papers' , 'papers.id' , '=' , 'paper_user.paper_id')
                            ->where('paper_user.paper_id' , '=' , $paper)
                            ->count();

            if($count_revs>=$conference->nb_reviewer_per_item)
                return back()->withErrors([
                        'message' => "reviewers already assigned for the paper ".$paper
                    ]);
            

            $is_rev = \DB::table('users')
                            ->join('paper_user' , 'users.id' ,'=', 'paper_user.user_id' )
                            ->join('papers' , 'papers.id' , '=' , 'paper_user.paper_id')
                            ->where('paper_user.user_id' , '=' , $user->id)
                            ->where('paper_user.paper_id' , '=' , $paper)
                            ->count();

            if($is_rev!=0)
                return back()->withErrors([
                        'message' => "reviewer already assigned for the paper ".$paper
                    ]);
        
            }

        foreach($request->papers as $p){
             \DB::table('paper_user')->insert([
                    'user_id'=>$user->id,
                    'paper_id'=>$p
             ]);
        }

        return redirect()->route('conferences.comite.index',[$acronym,$edition]);
    }

  

    public function myPapers($acronym,$edition,\App\User $user){

        $conf = new Conference;
        $conference = $conf->getConference($acronym,$edition,'R');

        if(count($conference) == 0 ){
            $conference = $conf->getConference($acronym,$edition);
        }

        return view('conferences.comite.mypapers',compact('conference'));

    }

    public function review($acronym,$edition,\App\Paper $paper){

        $conf = new Conference;
        $conference = $conf->getConference($acronym,$edition,'R');

        $is_rev = \DB::table('paper_user')
                            ->where('user_id' , '=' , auth()->user()->id)
                            ->where('paper_id' , '=' , $paper->id)
                            ->count();

        if($is_rev == 0){
            return redirect()->route('notfound',['404']);
        }
        

        $criterias = \App\Criteria::where('conference_id',$conference->id)->get();

        $rquestions= \App\Reviewquestion::where('conference_id',$conference->id)->get();

        $rqchoices =  \DB::table('rqchoices')
                        ->select('*')
                        ->orderBy('position')
                        ->get();
        
        $is_reviewed= \DB::table('paper_user')
                            ->where('user_id' , '=' , auth()->user()->id)
                            ->where('paper_id' , '=' , $paper->id)
                            ->where('is_reviewed' , '=' , 1)
                            ->count();
    
        
        $rev_criterias = \DB::table('reviewmarks')
                            ->join('criterias' , 'criterias.id' ,'=', 'reviewmarks.criteria_id' )
                            ->where('reviewmarks.user_id' , '=' , auth()->user()->id)
                            ->where('reviewmarks.paper_id' , '=' , $paper->id)
                            ->get();

        $reviews = \DB::table('reviews')
                            ->where('user_id' , '=' , auth()->user()->id)
                            ->where('paper_id' , '=' , $paper->id)
                            ->first();

       

        $paper_user_revq = \DB::table('paper_reviewquestion_user')
                            ->join('rqchoices' , 'paper_reviewquestion_user.rqchoice_id' ,'=', 'rqchoices.id' )
                            ->join('reviewquestions' , 'paper_reviewquestion_user.reviewquestion_id' ,'=', 'reviewquestions.id' )
                            ->where('paper_reviewquestion_user.user_id' , '=' , auth()->user()->id)
                            ->where('paper_reviewquestion_user.paper_id' , '=' , $paper->id)
                            ->get();

        $paperInfo = \DB::table('papers')
                    ->join('paper_topic' , 'papers.id' ,'=', 'paper_topic.paper_id' )
                    ->join('topics' , 'topics.id' , '=' , 'paper_topic.topic_id')
                    ->where('papers.id',$paper->id)
                    ->select('papers.*','topics.label')
                    ->first();                    
                            //return $paper_user_revq;




        return view('conferences.comite.review',compact('conference','paper','criterias','rquestions','rqchoices','is_reviewed','paper_user_revq','rev_criterias','reviews','paperInfo'));
    }

    public function storeReview($acronym,$edition,\App\Paper $paper,Request $request){

        $conf = new Conference;
        $conference = $conf->getConference($acronym,$edition,'R');



        $this->validate(request(), [
            'criterias.*'       =>  ['required','numeric','min:1','max:7','regex:/^[^<>]+$/'],
            'expertise'         =>  ['required','numeric','min:1','max:3','regex:/^[^<>]+$/'],
            'summary'           =>  ['required','min:4','regex:/^[^<>]+$/'],
            'comments'          =>  ['required','min:4','regex:/^[^<>]+$/'],
            'details'          =>  ['required','min:4','regex:/^[^<>]+$/'],
        ]);

        $rqs = \App\Reviewquestion::where('conference_id',$conference->id)->get();

        foreach($rqs as $rq){
            $idr = $rq->id ;

            \DB::table('paper_reviewquestion_user')->insert([
                    'paper_id' => $paper->id,
                    'reviewquestion_id' => $rq->id,
                    'user_id'=>auth()->user()->id,
                    'rqchoice_id'=> $request->$idr

                ]);
        }

        $criterias = \App\Criteria::where('conference_id',$conference->id)->get();
        
        foreach($criterias as $cr){

            $idcr = 'criteria'.$cr->id ;

            \DB::table('reviewmarks')->insert([
                        'paper_id' => $paper->id,
                        'user_id'=>auth()->user()->id,
                        'criteria_id'=> $cr->id,
                        'mark'=> $request->$idcr

                    ]);
        }
        
        $rev_criterias = \DB::table('reviewmarks')
                            ->join('criterias' , 'criterias.id' ,'=', 'reviewmarks.criteria_id' )
                            ->where('reviewmarks.user_id' , '=' , auth()->user()->id)
                            ->where('reviewmarks.paper_id' , '=' , $paper->id)
                            ->get();

        $s=0;
         foreach($rev_criterias as $rev_cr){
            $s=$s+($rev_cr->mark*$rev_cr->weight);
         }

         $overall = $s/100;

         \DB::table('reviews')->insert([
                        'paper_id' => $paper->id,
                        'user_id'=>auth()->user()->id,
                        'overall'=> $overall,
                        'reviewExpertise'=> $request->expertise,
                        'summary'=> $request->summary,
                        'details'=> $request->details,
                        'comments'=> $request->comments,

                    ]);
        
        \DB::table('paper_user')->where('paper_id',$paper->id)
                                ->where('user_id',auth()->user()->id)
                                ->update(['is_reviewed'=> '1' ]);


        
        $user=auth()->user();


                $admin = \DB::table('users')
                            ->join('conference_user' , 'users.id' , '=' , 'conference_user.user_id')
                            ->where('conference_id',$conference->id)
                            ->where('role','A')
                            ->select('users.*')
                            ->first();

                $adminArray['first'] = $admin->first_name;
                $adminArray['last'] = $admin->last_name;

                $paperArray['idPaper']=$paper->id;
                $paperArray['title']=$paper->title;
                $paperArray['abstract']=$paper->abstract;

               
               $data = $user->toArray()+$paperArray+$conference->toArray()+$adminArray;

                    switch ($conference->mail_on_review)
                        {
                            //to admin and author
                            case 1:
                                Mail::to($user['email'])->send(new ReviewSuccessRev($data));
                                Mail::to($admin->email)->send(new ReviewSuccessAdmin($data));
                                break;
                            
                            //to admin
                            case 2:
                                Mail::to($admin->email)->send(new ReviewSuccessAdmin($data));
                                break;

                            //to author
                            case 3:
                                Mail::to($user['email'])->send(new ReviewSuccessRev($data));
                                break;
                            
                            //no
                            default:       
                                break;

                        }


                

        return redirect()->route('conferences.comite.mypapers',[$acronym,$edition]);
    }

    public function updateReview($acronym,$edition,\App\Paper $paper, Request $request){

        $conf = new Conference;
        $conference = $conf->getConference($acronym,$edition,'R');

        $this->validate(request(), [
            'criterias.*'       =>  ['required','numeric','min:1','max:7','regex:/^[^<>]+$/'],
            'expertise'         =>  ['required','numeric','min:1','max:3','regex:/^[^<>]+$/'],
            'summary'           =>  ['required','min:4','regex:/^[^<>]+$/'],
            'comments'          =>  ['required','min:4','regex:/^[^<>]+$/'],
            'details'          =>  ['required','min:4','regex:/^[^<>]+$/'],
        ]);


        $rqs = \App\Reviewquestion::where('conference_id',$conference->id)->get();

        foreach($rqs as $rq){
            $idr = $rq->id ;

            \DB::table('paper_reviewquestion_user')->where('paper_id',$paper->id)
                                                   ->where('user_id',auth()->user()->id)
                                                   ->where('reviewquestion_id',$rq->id)
                                                   ->update(['rqchoice_id'=> $request->$idr]);
        }

        $criterias = \App\Criteria::where('conference_id',$conference->id)->get();
        
        foreach($criterias as $cr){

            $idcr = 'criteria'.$cr->id ;

            \DB::table('reviewmarks')->where('paper_id',$paper->id)
                                    ->where('user_id',auth()->user()->id)
                                    ->where('criteria_id',$cr->id)
                                    ->update(['mark'=> $request->$idcr ]);
        }

        $rev_criterias = \DB::table('reviewmarks')
                            ->join('criterias' , 'criterias.id' ,'=', 'reviewmarks.criteria_id' )
                            ->where('reviewmarks.user_id' , '=' , auth()->user()->id)
                            ->where('reviewmarks.paper_id' , '=' , $paper->id)
                            ->get();

        $s=0;
         foreach($rev_criterias as $rev_cr){
            $s=$s+($rev_cr->mark*$rev_cr->weight);
         }

         $overall = $s/100;

         \DB::table('reviews')->where('paper_id',$paper->id)
                              ->where('user_id',auth()->user()->id)
                              ->update([
                                    'overall'=> $overall,
                                    'reviewExpertise'=> $request->expertise,
                                    'summary'=> $request->summary,
                                    'details'=> $request->details,
                                    'comments'=> $request->comments,

                                ]);




        return redirect()->route('conferences.comite.mypapers',[$acronym,$edition]);
    }

    

    public function assignStatus($acronym,$edition){
        $conf = new Conference;
        $conference = $conf->getConference($acronym,$edition,'AC');
        
        $papers = \DB::table('papers')
                    ->join('paper_topic' , 'papers.id' ,'=', 'paper_topic.paper_id' )
                    ->join('topics' , 'topics.id' , '=' , 'paper_topic.topic_id')
                    ->join('paper_user' , 'papers.id' ,'=', 'paper_user.paper_id' )
                    ->join('users' , 'users.id' ,'=', 'paper_user.user_id' )
                    ->join('authors','authors.paper_id','=','papers.id')
                    ->where('papers.conference_id' , '=' , $conference->id)
                    ->where('paper_user.is_reviewed' , '=' , 1)
                    //->where('papers.id' , '=' , 20)
                    ->where('authors.is_corresponding' , '=' , 1)
                    //->where('user_id', '=' , auth()->id())
                    ->select('topics.*','users.*','authors.*','papers.*')
                    ->groupBy('papers.id')
                    ->orderBy('papers.created_at','DESC')
                    //->latest('papers.created_at')
                    ->get();

                    //return $papers;
        $papers = \DB::select('select topics.*,authors.*, papers.*, paperstatuses.label as psLabel, paperstatuses.camReadyRequired as camReadyRq from papers
                                INNER JOIN paper_topic ON papers.id = paper_topic.paper_id
                                INNER JOIN topics ON topics.id = paper_topic.topic_id
                                INNER JOIN authors ON authors.paper_id = papers.id

                                LEFT OUTER  JOIN paper_user ON papers.id = paper_user.paper_id
                                LEFT OUTER  JOIN users ON users.id = paper_user.user_id

                                LEFT OUTER JOIN paperstatuses ON paperstatuses.id = papers.paperstatus_id
                                WHERE papers.conference_id = :confId
                                    and authors.is_corresponding = 1
                                GROUP BY papers.id
                                ORDER BY papers.created_at
                                ',['confId' => $conference->id]);
     
        //return $papers;
        $paperStatus = \App\Paperstatus::where('conference_id',$conference->id)->get();
        return view('conferences.comite.assignStatus',compact('conference','papers','paperStatus'));
    }

    public function storeAssignStatus($acronym,$edition,Request $request){



        /*$this->validate(request(), [
            'status.*'                    =>  ['required','numeric','min:0','max:999','regex:/^[^<>]+$/'],
            'papers.*'                    =>  ['required','regex:/^[^<>]+$/'],
            ]);*/

        $i = 0;

        
        foreach($request->papers['papers'] as $paper){
            if($request->status['status'][$i] != 0){
                $p = \App\Paper::find($paper);
                $p->paperstatus_id = $request->status['status'][$i];
                $p->save();
            }

            $i++;
        }

        return $request->all();
        return back();
    }

    public function showReview($acronym,$edition,\App\Paper $paper, \App\User $user){
        

        $conf = new Conference;
        $conference = $conf->getConference($acronym,$edition,'AC');


        $criterias = \App\Criteria::where('conference_id',$conference->id)->get();

        $rquestions= \App\Reviewquestion::where('conference_id',$conference->id)->get();

        $rqchoices =  \DB::table('rqchoices')
                        ->select('*')
                        ->orderBy('position')
                        ->get();
        
  
        $rev_criterias = \DB::table('reviewmarks')
                            ->join('criterias' , 'criterias.id' ,'=', 'reviewmarks.criteria_id' )
                            ->where('reviewmarks.user_id' , '=' , $user->id)
                            ->where('reviewmarks.paper_id' , '=' , $paper->id)
                            ->get();


        $reviews = \DB::table('reviews')
                            ->where('user_id' , '=' , $user->id)
                            ->where('paper_id' , '=' , $paper->id)
                            ->first();

       

        $paper_user_revq = \DB::table('paper_reviewquestion_user')
                            ->join('rqchoices' , 'paper_reviewquestion_user.rqchoice_id' ,'=', 'rqchoices.id' )
                            ->join('reviewquestions' , 'paper_reviewquestion_user.reviewquestion_id' ,'=', 'reviewquestions.id' )
                            ->where('paper_reviewquestion_user.user_id' , '=' , $user->id)
                            ->where('paper_reviewquestion_user.paper_id' , '=' , $paper->id)
                            ->get();

                            
                            //return $paper_user_revq;




        return view('conferences.comite.showReview',compact('conference','paper','criterias','rquestions','rqchoices','user','paper_user_revq','rev_criterias','reviews'));
    }

    public function showReviewJson($acronym,$edition,$review){

        $conf = new Conference;
        $conference = $conf->getConference($acronym,$edition,'AC');


        $criterias = \App\Criteria::where('conference_id',$conference->id)->get();

        $rquestions= \App\Reviewquestion::where('conference_id',$conference->id)->get();

        $rqchoices =  \DB::table('rqchoices')
                        ->select('*')
                        ->orderBy('position')
                        ->get();
        

        $reviews = \DB::table('reviews')
                        ->where('reviews.id' , '=' , $review)->first();
  
        $paper = \DB::table('papers')
                    ->join('paper_topic' , 'papers.id' ,'=', 'paper_topic.paper_id' )
                    ->join('topics' , 'topics.id' , '=' , 'paper_topic.topic_id')
                    ->join('authors','authors.paper_id','=','papers.id')
                    ->where('papers.id' , '=' , $reviews->paper_id)
                    ->where('authors.is_corresponding' , '=' , 1)
                    ->select('topics.*','authors.*','papers.*')
                    ->groupBy('papers.id')
                    ->orderBy('papers.created_at','DESC')
                    ->first();

        $rev_criterias = \DB::table('reviewmarks')
                            ->join('criterias' , 'criterias.id' ,'=', 'reviewmarks.criteria_id' )
                            ->where('reviewmarks.user_id' , '=' , $reviews->user_id)
                            ->where('reviewmarks.paper_id' , '=' , $reviews->paper_id)
                            ->get();


        $paper_user_revq = \DB::table('paper_reviewquestion_user')
                            ->join('rqchoices' , 'paper_reviewquestion_user.rqchoice_id' ,'=', 'rqchoices.id' )
                            ->join('reviewquestions' , 'paper_reviewquestion_user.reviewquestion_id' ,'=', 'reviewquestions.id' )
                            ->where('paper_reviewquestion_user.user_id' , '=' , $reviews->user_id)
                            ->where('paper_reviewquestion_user.paper_id' , '=' , $reviews->paper_id)
                            ->get()->toArray();

        //$data=json_encode(array_merge((array)$reviews,(array)$rev_criterias));
        $reviews=(array)$reviews;
        $rev_criterias=(array)$rev_criterias;
        $crArray=array();
        foreach($rev_criterias as $key => $value){
            $crArray['items']= $rev_criterias[$key];
        }

        return \Response::json(array('reviews' => $reviews, 'rev_criterias'=> $crArray, 'paper' => $paper,'questions' => $paper_user_revq));
    }


    /*public function deleteReviewer($acronym,$edition,\App\Paper $paper, \App\User $user ){


        \DB::table('paper_user')->where('user_id' , '=' , $user->id)
                                ->where('paper_id' , '=' , $paper->id)
                                ->delete();  

        \DB::table('reviews')->where('user_id' , '=' , $user->id)
                                ->where('paper_id' , '=' , $paper->id)
                                ->delete();  

        return back();

    }*/
/*
    public function rate($acronym,$edition){

        $conf = new Conference;
        $conference = $conf->getConference($acronym,$edition,'R');
        
        $papers = \DB::table('papers')
                    ->join('paper_topic' , 'papers.id' ,'=', 'paper_topic.paper_id' )
                    ->join('topics' , 'topics.id' , '=' , 'paper_topic.topic_id')
                    ->join('paper_user' , 'papers.id' ,'=', 'paper_user.paper_id' )
                    ->join('users' , 'users.id' ,'=', 'paper_user.user_id' )
                    ->where('papers.conference_id' , '=' , $conference->id)
                    ->where('paper_user.user_id' , '=' , auth()->user()->id)
                    ->select('paper_user.*','papers.*')
                    ->groupBy('papers.id')
                    ->orderBy('papers.created_at','DESC')
                    ->get();

        $ratelabels = \App\Ratelabel::where('conference_id',$conference->id)->get();
        return view('conferences.comite.rate',compact('conference','papers','ratelabels'));

    }

    public function storeRate($acronym,$edition,Request $request){

        $this->validate(request(), [
            'rating.*'                    =>  ['required','numeric','min:0','max:999','regex:/^[^<>]+$/'],
            'papers.*'                    =>  ['required','regex:/^[^<>]+$/'],
            ]);

        $i = 0;

        foreach($request->papers as $paper){
            if($request->rating[$i] != 0){
                \DB::table('paper_user')->where('paper_id',$paper)
                                ->where('user_id',auth()->user()->id)
                                ->update(['ratelabel_id'=> $request->rating[$i] ]);

            }

            $i++;
        }

            
        return back();
        
    }
*/


public function sendMail($acronym,$edition,Request $request)
{

    $this->validate(request(), [
                'selectMsg'              =>  ['required','max:255','regex:/^[^<>]+$/'],
                'emails'                 =>  ['required','max:255','regex:/^[^<>]+$/'],
                ]);


  $conf = new \App\Conference;
  $conference = $conf->getConference($acronym,$edition);


  $emails=explode(';',$request->emails);
  
  if($request->selectMsg != 'free'){
    $msg=\App\Messagetemp::where('id',$request->selectMsg)->first();
    $content = $msg->body;
    $subj=$msg->title;
  }elseif($request->selectMsg == 'free'){
    $this->validate(request(), [
            'subject'                 =>  ['required','max:255','regex:/^[^<>]+$/'],
            'body'                   =>  ['required']
            ]);
      $content = $request->body;
      $subj=$request->subject;
  }
  


    $myfile = fopen(base_path('resources/views/emails/custom.blade.php'), "a+");
    file_put_contents(base_path('resources/views/emails/custom.blade.php'), $content);




    foreach($emails as $mail){
        $data=$conference->toArray()+\App\User::where('email',$mail)->first()->toArray();
        Mail::send('emails.custom',$data, function($message) use ($mail,$subj){
        $message->to($mail);
        $message->subject($subj);
    });
    sleep(5);
    }
    
  
  
  return back();

}



public function notifyAuthors($acronym,$edition,Request $request)
{



  $conf = new \App\Conference;
  $conference = $conf->getConference($acronym,$edition);

    $ids=$request->ids;
    
    $ids = str_replace('[','',$ids);
    $ids = str_replace(']','',$ids);
    $array = explode(',',$ids);


  foreach($array as $paper){

    $auth = \App\Author::where('paper_id',$paper)->where('is_corresponding',1)->first();

    $email = $auth->email;


    $p = \App\Paper::find($paper);
    if($p->paperstatus_id == null)
        return back()->withErrors([
                                'message' => "please assign status to th paper".$paper."before you notify her author"
                            ]);

    $status = \App\Paperstatus::find($p->paperstatus_id);

    $body = $status->msgTemplate;
    $subject = "Staus of paper ".$paper;
  
    $myfile = fopen(base_path('resources/views/emails/custom.blade.php'), "a+");

    
    $reviews = \DB::table('reviews')
                ->where('paper_id' , $paper)
                ->get();

    $content = "";
    $content .= $body."\n <hr>";
    $criterias = \App\Criteria::where('conference_id',$conference->id)->get();
    $i = 1;
    $s = 0;
    foreach($reviews as $rev){
        $content .= "<br> <strong>Reviewer ".$i.": </strong> <br> <br>";

        foreach($criterias as $cr){
            $mark = \DB::table('reviewmarks')
                        ->where('user_id',$rev->user_id)
                        ->where('paper_id',$paper)
                        ->where('criteria_id',$cr->id)
                        ->get();
            foreach ($mark as $m)
                $mr = $m->mark;
            $content .= "\n <strong>".$cr->label.": </strong> ".$mr."/7<br> ";
        }

        $content .= "\n <strong>Summary: </strong> ".$rev->summary."<br>";
        $content .= "\n <strong>Details: </strong> ".$rev->details."<br>";
        $content.="<strong>Overall Score: </strong>".$rev->overall."/7 <br>";

        $i++;
        $s=$s+$rev->overall;
    }

     $content .= "\n <br> <strong>Average score: </strong> ".$s/count($reviews)."<br>";

    file_put_contents(base_path('resources/views/emails/custom.blade.php'), $content);
    


    $data=$conference->toArray()+\App\User::where('email',$email)->first()->toArray()+$p->toArray();
        Mail::send('emails.custom',$data, function($message) use ($email,$subject){
        $message->to($email);
        $message->subject($subject);
    });




    sleep(5);

    
}
  
  return back() ;

}


}
