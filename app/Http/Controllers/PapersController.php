<?php

namespace App\Http\Controllers;
use App\Http\Middleware\CheckIfAdmin;
use App\Http\Middleware\CheckIfAdminChair;
use App\Http\Middleware\CheckIfAuthor;
use App\Http\Middleware\CheckIfReviewer;

use App\Paper;
use Illuminate\Http\Request;

use Session;
use App\Conference;
use App\Paperquestion;
use Storage;
use App\Mail\UploadSuccessAuthor;
use App\Mail\UploadSuccessAdmin;
use Datatables;
use Mail;


class PapersController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
        $this->middleware(CheckIfAdmin::class)->except('index','getData','show','create','store','download','assign','storeAssign','uploadCr','storeCr');
        $this->middleware(CheckIfAdminChair::class)->only('index','getData','assign','storeAssign');

        //$this->middleware(CheckIfAuthor::class)->only('show');
        //$this->middleware(CheckIfReviewer::class)->only('show');
    }

    public function getData($acronym,$edition){
        $conference = \App\Conference::where('confAcronym' , '=' , $acronym)
                                        ->where('confEdition' , '=' , $edition)
                                        ->first();
        $lang = Session::get('lang');
        $P = parse_ini_file(base_path('language/'.$lang.'/PAPERS.ini'));

        $GLOBALS['C'] = $conference;
        $GLOBALS['L'] = $P;
        //return $GLOBALS['C'];
        /*$papers =  \App\Conference::where('confAcronym' , '=' , $acronym)
                                        ->where('confEdition' , '=' , $edition)
                                        ->first()
                                        ->papers;
        */
        $papers = \DB::select('select topics.*,authors.*, papers.*, paperstatuses.label as psLabel, paperstatuses.camReadyRequired as camReadyRq from papers
                                    INNER JOIN paper_topic ON papers.id = paper_topic.paper_id
                                    INNER JOIN topics ON topics.id = paper_topic.topic_id
                                    INNER JOIN authors ON authors.paper_id = papers.id
                                    LEFT OUTER JOIN paperstatuses ON paperstatuses.id = papers.paperstatus_id
                                    WHERE papers.conference_id = :confId
                                        and authors.is_corresponding = 1
                                    GROUP BY papers.id
                                    ORDER BY papers.created_at
                                    ',['confId' => $conference->id]);


        /*
        $papers = \DB::table('papers')
                    ->join('paper_topic' , 'papers.id' ,'=', 'paper_topic.paper_id' )
                    ->join('topics' , 'topics.id' , '=' , 'paper_topic.topic_id')
                    ->join('authors','authors.paper_id','=','papers.id')
                    ->join('paperstatuses',function($join){
                        $join->on('paperstatuses.id','=','papers.paperstatus_id')
                            ->whereNull('papers.paperstatus_id');
                    })
                    ->where('papers.conference_id' , '=' , $conference->id)
                    ->where('authors.is_corresponding' , '=' , 1)
                    //->where('user_id', '=' , auth()->id())
                    ->select('topics.*','authors.*','papers.*','paperstatuses.label as psLabel','paperstatuses.camReadyRequired as camReady')
                    ->groupBy('papers.id')
                    ->orderBy('papers.created_at','DESC')
                    //->latest('papers.created_at')
                    ->get();
                    //->unique('papers.id');
            */
      
        return Datatables::of($papers)->addColumn('action', function ($paper) {
                    return '

                        <form id="formDeletePaper" action="/conferences/'.$GLOBALS['C']->confAcronym.'/'.$GLOBALS['C']->confEdition.'/papers/'.$paper->id.'/delete" method = "POST">
                            <a class="button ui primary mini" href="/conferences/'.$GLOBALS['C']->confAcronym.'/'.$GLOBALS['C']->confEdition.'/papers/'.$paper->id.'/download/0" data-tooltip="'.$GLOBALS['L']['DOWN_BTN'].'">  <i class="icon download cloud"></i></a> </h2> 
                            <a href="/conferences/'.$GLOBALS['C']->confAcronym.'/'.$GLOBALS['C']->confEdition.'/papers/'.$paper->id.'" class="button ui green mini" data-tooltip="'.$GLOBALS['L']['SHOW_BTN'].'" ><i class="eye icon"></i></a>
                            <a href="/conferences/'.$GLOBALS['C']->confAcronym.'/'.$GLOBALS['C']->confEdition.'/papers/assign/'.$paper->id.'" class="button ui teal mini" data-tooltip="'.$GLOBALS['L']['ASSIGN_BTN'].'"><i class="add user icon"></i></a>
                            <button id="btnDeletePaper" type="submit" class="button ui red mini" data-tooltip="'.$GLOBALS['L']['DELETE_BTN'].'"><i class="delete icon"></i></button>
                            <input type="hidden" value="'.$paper->id.'" class="zipInput" id="'.$paper->id.'" name="idForZip[]"> 
                            <input type="hidden" value="'.$paper->email.'" class="mailInput" name="idForMail[]"> 

                        </form>
                        
                        
                        ';
                        //<a href="#show-'.$msg->id.'" class="button ui teal"><i class="unhide icon"></i>  Show</a>';
                },5)
                ->addColumn('camReady',function($paper){
                    if($paper->camReadyRq === 1){
                        if($GLOBALS['C']->camReady == 'Y'){
                            if ((\File::exists('papers/CR_'.strtoupper($paper->psLabel))) and ($GLOBALS['C']->is_cam_ready_open == 'Y') ){
                                return '<a class="button ui primary mini" href="/conferences/'.$GLOBALS['C']->confAcronym.'/'.$GLOBALS['C']->confEdition.'/papers/'.$paper->id.'/download/1" data-tooltip="'.$GLOBALS['L']['DOWN_CR_BTN'].'">  <i class="icon download cloud"></i></a> </h2>';
                            }elseif((\File::exists('papers/CR'.$paper->psLabel)) and ($GLOBALS['C']->is_cam_ready_open == 'N') ){
                                return '<a class="button ui primary" href="/conferences/'.$GLOBALS['C']->confAcronym.'/'.$GLOBALS['C']->confEdition.'/papers/'.$paper->id.'/download/CR/1" data-tooltip="'.$GLOBALS['L']['DOWN_CR_BTN'].'">  <i class="icon download cloud"></i></a> </h2>
                                        ';
                            }
                        }
                    }
                })
                ->addColumn('Reviewers',function($paper){
                    $revs = \DB::table('paper_user')->where('paper_id',$paper->id)->get()->toArray();


                    
                })
                ->rawColumns(['camReady','action'])
                ->editColumn('first_name','{{$first_name}}  {{$last_name}}')
                ->make(true);

}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($acronym,$edition)
    {

    $conf = new Conference;
    $conference = $conf->getConference($acronym,$edition,'AC');

    $papers = \DB::select('select topics.*,authors.*, papers.*, paperstatuses.label as psLabel, paperstatuses.camReadyRequired as camReadyRq from papers
                                    INNER JOIN paper_topic ON papers.id = paper_topic.paper_id
                                    INNER JOIN topics ON topics.id = paper_topic.topic_id
                                    INNER JOIN authors ON authors.paper_id = papers.id
                                    LEFT OUTER JOIN paperstatuses ON paperstatuses.id = papers.paperstatus_id
                                    WHERE papers.conference_id = :confId
                                        and authors.is_corresponding = 1
                                    GROUP BY papers.id
                                    ORDER BY papers.created_at
                                    ',['confId' => $conference->id]);
    $paperStatus = \App\Paperstatus::where('conference_id',$conference->id)->get();

    return view('conferences.papers.index',compact('conference','papers','paperStatus'));
      
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($acronym,$edition, Request $request)
    {
        if ($request->session()->get('lang') == NULL){
            $request->session()->put('lang', 'en');
        }
        
        $conf = new Conference;
        $conference = $conf->getConference($acronym,$edition);

        if (count($conference) == 0){
            $conference = \App\Conference::where('confAcronym' , '=' , $acronym)
                                        ->where('confEdition' , '=' , $edition)
                                        ->first();
        }

        $questions = Paperquestion::where('conference_id',$conference->id)->get();
        $pqchoice =  \DB::table('pqchoices')
                        ->select('*')
                        ->orderBy('position')
                        ->get();
        //return $pqchoice;
        return view('conferences.papers.create',compact('edition','conference','acronym','questions','pqchoice'));
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

        if (count($conference) == 0){
            $conference = \App\Conference::where('confAcronym' , '=' , $acronym)
                                        ->where('confEdition' , '=' , $edition)
                                        ->first();
        }

        $file_type=$conference->file_type;
  
        $first = request('first');

        $this->validate(request(), [
            'title'     =>  ['required','min:5','max:255','regex:/^[^<>]+$/'],
            'abstract'  =>  ['required','min:10'],
            'keywords'  =>  ['required','max:255','regex:/^[^<>]+$/'],
            'captcha'   =>  ['required','captcha'],
            'first.*'   =>  ['required','min:2','max:255','regex:/^[^<>]+$/'],
            'last.*'    =>  ['required','min:2','max:255','regex:/^[^<>]+$/'],
            'aff.*'     =>  ['required','min:1','max:255','regex:/^[^<>]+$/'],
            'grade.*'     =>  ['required','min:1','max:255','regex:/^[^<>]+$/'],
            'email.*'   =>  ['required','min:4','max:255','regex:/^[^<>]+$/'],
            'country.*'  =>  ['required','min:2','max:255','regex:/^[^<>]+$/'],
            'question.*' =>['required','min:1','max:255','regex:/^[^<>]+$/'],
            'file'         =>['required','file','mimes:'.$file_type],
            'correspond.*'  =>  ['required','min:1','max:10'],
            ]);

   


        Paper::create([
                'title' => request('title'),
                'abstract' => request('abstract'),
                'keywords' => request('keywords'),
                'conference_id' => $conference->id
            ]);

            $paper = new Paper;
            $paperId = $paper->orderBy('id', 'DESC')->first();


            //Handle Authors
            
            $first = request('first');
            $last = request('last');
            $aff = request('aff');
            $grade = request('grade');
            $email = request('email');
            $country = request('country');
            $n = count($first);
            
            $countV =0 ;
            
            for($i=0;$i<count($request->is_correspond);$i++){
                if ($request->is_correspond[$i] == 1)
                    $countV++;
            }

            if($countV != 1){
                return back()->withErrors([
                        'message' => "select one correspond author"
                    ]);
            }

            $v = (count($email) != count(array_unique($email)));
                if($v){
                     return back()->withErrors([
                        'message' => "duplicate authors"
                    ]);
                }


            for($i=0;$i<$n;$i++)
            {
                if($request->is_correspond[$i] == 1){
                    \DB::table('authors')->insert([
                    'first_name'=>$first[$i],
                    'last_name'=>$last[$i],
                    'affilation'=>$aff[$i],
                    'grade'=>$grade[$i],
                    'email'=>$email[$i],
                    'country'=>$country[$i],
                    'paper_id'=> $paperId->id,
                    'is_corresponding' => '1'
                ]);
                
                $user=[];
                $user['email'] = $email[$i];
                $user['first_name'] = $first[$i];
                $user['last_name'] = $last[$i];
                $user['country'] = $country[$i];

                $admin = \DB::table('users')
                            ->join('conference_user' , 'users.id' , '=' , 'conference_user.user_id')
                            ->where('conference_id',$conference->id)
                            ->where('role','A')
                            ->select('users.*')
                            ->first();

                $adminArray['first'] = $admin->first_name;
                $adminArray['last'] = $admin->last_name;
               
               $data = $user+$paperId->toArray()+$conference->toArray()+$adminArray;

                    switch ($conference->mail_on_upload)
                        {
                            //to admin and author
                            case 1:
                                Mail::to($user['email'])->send(new UploadSuccessAuthor($data));
                                Mail::to($admin->email)->send(new UploadSuccessAdmin($data));
                                break;
                            
                            //to admin
                            case 2:
                                Mail::to($admin->email)->send(new UploadSuccessAdmin($data));
                                break;

                            //to author
                            case 3:
                                Mail::to($user['email'])->send(new UploadSuccessAuthor($data));
                                break;
                            
                            //no
                            default:       
                                break;

                        }
                    
                }else{
                    
                    \DB::table('authors')->insert([
                    'first_name'=>$first[$i],
                    'last_name'=>$last[$i],
                    'affilation'=>$aff[$i],
                    'grade'=>$grade[$i],
                    'email'=>$email[$i],
                    'country'=>$country[$i],
                    'paper_id'=> $paperId->id
                ]);
                }

                if(\App\User::where('email',$email[$i])->count() != 0 ){
                        
                        if(\DB::table('conferences')
                            ->join('conference_user' , 'conferences.id' , 'conference_user.conference_id' )
                            ->join('users' , 'users.id' , '=' , 'conference_user.user_id')
                            ->where('users.email',$email[$i])
                            ->where('conference_user.conference_id',$conference->id)
                            ->where('conference_user.role','Aut')
                            ->count() == 0){

                                $userId = \App\User::where('email',$email[$i])->first();

                                \DB::table('conference_user')->insert([
                                        'user_id'=>$userId->id,
                                        'role'=>'Aut',
                                        'conference_id'=> $conference->id
                                    ]);

                            }
                    }
                
            }

            


            

            //Handle Topics

            $n = count(request('topics'));

             if($n == 0){
                     return back()->withErrors([
                        'message' => "select at least one topic"
                    ]);
                }

            for($i=0;$i<$n;$i++)
            {
                \DB::table('paper_topic')->insert([
                    'topic_id'=>request('topics')[$i],
                    'paper_id'=> $paperId->id
                ]);
            }
            
            //Handle pquestions

            $rqs = \App\Paperquestion::where('conference_id',$conference->id)->get();

            foreach($rqs as $pq){
                $idp = $pq->id ;

                \DB::table('paper_paperquestion')->insert([
                        'paper_id' => $paperId->id,
                        'paperquestion_id' => $pq->id,
                        'pqchoice_id'=> $request->$idp

                    ]);
            }

            


               //handle the file

            $file = $request->file;
            //$fileName=$file->getClientOriginalName();

            $fileName=$paperId->id;
            
            $destinationPath = 'papers/'.$fileName;
            $uploaded = Storage::put($destinationPath, file_get_contents($file->getRealPath()));

            

            

            return redirect('/');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($acronym,$edition,Paper $paper)
    {
        $conf = new Conference;
        $conference = $conf->getConference($acronym,$edition);


        if (count($conference) == 0){
            $conference = $conf->getConference($acronym,$edition,'AC');
                if (count($conference) == 0){
                    $conference = $conf->getConference($acronym,$edition,'R');
                        if (count($conference) == 0){
                            $conference = $conf->getConference($acronym,$edition,'Aut');
                        }
                }
        }

        $conferenceA = $conf->getConference($acronym,$edition,'author');
        $conferenceR = $conf->getConference($acronym,$edition,'R');
        $conferenceAdmin = $conf->getConference($acronym,$edition);

        

        if(count($conference) == 0)
            return redirect()->route('notfound',['404']);
        if($paper->conference_id != $conference->id)
            return redirect()->route('notfound',['404']);

    

        $topics = \DB::table('papers')
                    ->join('paper_topic' , 'papers.id' ,'=', 'paper_topic.paper_id' )
                    ->join('topics' , 'topics.id' , '=' , 'paper_topic.topic_id')
                    ->where('papers.id',$paper->id)
                    ->select('topics.*')
                    ->get();

        $authors = \DB::table('papers')
                    ->join('authors','authors.paper_id','papers.id')
                    ->where('authors.paper_id',$paper->id)
                    ->get();
        $users = \App\User::all();

        $pquestions=\DB::table('papers')
                    ->join('paper_paperquestion','paper_paperquestion.paper_id','papers.id')
                    ->join('paperquestions','paper_paperquestion.paperquestion_id','paperquestions.id')
                    ->join('pqchoices','paper_paperquestion.pqchoice_id','pqchoices.id')
                    ->groupBy('paperquestions.question')
                    ->where('paper_paperquestion.paper_id',$paper->id)
                    ->get();

        
        $is_Author = \App\Author::where('paper_id',$paper->id)->where('email',auth()->user()->email)->count();

        $is_rev = \DB::table('users')
                            ->join('paper_user' , 'users.id' ,'=', 'paper_user.user_id' )
                            ->join('papers' , 'papers.id' , '=' , 'paper_user.paper_id')
                            ->where('paper_user.user_id' , '=' , auth()->user()->id)
                            ->where('paper_user.paper_id' , '=' , $paper->id)
                            ->count();  
    

        if(count($conferenceAdmin) == 0){

            if( (count($conferenceA) !=0) and (count($conferenceR) != 0) ){
                if ( ($is_Author == 0) and ($is_rev == 0) ){
                    return redirect()->route('notfound',['404']); }
            }elseif((count($conferenceA) !=0) and (count($conferenceR) == 0)){
                if ( $is_Author == 0 ){
                    return redirect()->route('notfound',['404']); }
            }elseif((count($conferenceA) ==0) and (count($conferenceR) != 0)){
                if ( $is_rev == 0 ){
                    return redirect()->route('notfound',['404']); }
            }

        }
        
        
        
        return view('conferences.papers.show',compact('conference','paper','topics','users','authors','pquestions'));
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
    public function destroy($acronym,$edition,Paper $paper)
    {
        $paper->delete();
        return back();
    }

     public function download($acronym,$edition,Paper $paper,$CR)
    {
 
        if($CR==0)
            return response()->download(public_path('papers/'.$paper->id));
        else{
            $papers = \DB::table('paperstatuses')
                    ->join('papers' , 'papers.paperstatus_id' ,'=', 'paperstatuses.id' )
                    ->where('papers.id' , $paper->id)
                    ->select('paperstatuses.label')
                    ->first();

            return response()->download(public_path('papers/CR_'.strtoupper($papers->label).'/'.$paper->id));
            
        }
               
    }
    
    public function keywords($acronym,$edition,$keys){

        $conf = new Conference;
        $conference = $conf->getConference($acronym,$edition);
        
        $papers = Paper::where('keywords','LIKE', '%'.$keys.'%')->get();

        $papers = \DB::table('papers')
                    ->join('paper_topic' , 'papers.id' ,'=', 'paper_topic.paper_id' )
                    ->join('topics' , 'topics.id' , '=' , 'paper_topic.topic_id')
                    ->join('authors','authors.paper_id','papers.id')
                    ->where('papers.keywords','LIKE', '%'.$keys.'%')
                    ->select('topics.label','authors.*','papers.*','authors.id as authorId')
                    ->get();
                    
        
                    
        return view('conferences.papers.keywords',compact('conference','papers','keys'));
      
    }

    public function assign($acronym,$edition,Paper $paper){


        $conf = new Conference;
        $conference = $conf->getConference($acronym,$edition,'AC');

        $papers = \DB::table('papers')
                    ->join('paper_topic' , 'papers.id' ,'=', 'paper_topic.paper_id' )
                    ->join('topics' , 'topics.id' , '=' , 'paper_topic.topic_id')
                    ->where('papers.id',$paper->id)
                    ->select('topics.id')
                    ->get();

        $idPapers= $papers->pluck('id')->all();

        $assigned = \DB::table('paper_user')
                    ->join('papers' , 'papers.id' ,'=', 'paper_user.paper_id' )
                    ->join('users' , 'users.id' , '=' , 'paper_user.user_id')
                    ->where('papers.conference_id',$conference->id)
                    ->where('papers.id',$paper->id)
                    ->count();

        $authors = \DB::table('papers')
                        ->join('authors' , 'papers.id' , '=' , 'authors.paper_id')
                        ->where('papers.id',$paper->id)
                        ->where('papers.conference_id',$conference->id)
                        ->select('authors.email')
                        ->get();

        $mailUsers= $authors->pluck('email')->all();
        //return $mailUsers;

        $users = \App\User::whereIn('email',$mailUsers)->get();

        $firstUsers= $users->pluck('first_name')->all();
        $lastUsers= $users->pluck('last_name')->all();
        $affUsers= $users->pluck('affilation')->all();


        $conflictUsers = \DB::table('users')
                    ->join('conference_user' , 'users.id' , '=' , 'conference_user.user_id')
                    ->join('conferences' , 'conferences.id' , '=' , 'conference_user.conference_id')
                    ->join('topic_user' , 'users.id' ,'=', 'topic_user.user_id' )
                    ->join('topics' , 'topics.id' , '=' , 'topic_user.topic_id')
                    ->where('conference_user.role','R')
                    ->where('conference_user.conference_id',$conference->id)
                    ->whereIn('first_name',$firstUsers)
                    ->whereIn('last_name', $lastUsers)
                    ->orWhereIn('affilation',$affUsers)
                    ->groupBy('users.id')
                    ->select('users.*','topics.id as tpId')
                    ->get();


        $CfUsersIds = $conflictUsers->pluck('id')->all();

        $reviewers1 = \DB::table('users')
                    ->join('conference_user' , 'users.id' , '=' , 'conference_user.user_id')
                    ->join('conferences' , 'conferences.id' , '=' , 'conference_user.conference_id')
                    ->join('topic_user' , 'users.id' ,'=', 'topic_user.user_id' )
                    ->join('topics' , 'topics.id' , '=' , 'topic_user.topic_id')
                    ->whereIn('topics.id' , $idPapers)
                    ->whereNotIn('users.id' , $CfUsersIds)
                    ->where('conference_user.role','R')
                    ->where('conference_user.conference_id',$conference->id)
                    ->groupBy('users.id')
                    ->select('users.*','topics.id as tpId')
                    ->get();

        $idRev1= $reviewers1->pluck('id')->all();        
    
                    

    $reviewers2 = \DB::table('users')
                ->join('conference_user' , 'users.id' , '=' , 'conference_user.user_id')
                ->join('conferences' , 'conferences.id' , '=' , 'conference_user.conference_id')
                ->join('topic_user' , 'users.id' ,'=', 'topic_user.user_id' )
                ->join('topics' , 'topics.id' , '=' , 'topic_user.topic_id')
                ->whereNotIn('users.id',$idRev1)
                ->whereNotIn('users.id' , $CfUsersIds)
                ->where('conference_user.role','R')
                ->where('conference_user.conference_id',$conference->id)
                ->groupBy('users.id')
                ->select('users.*','topics.id as tpId')
                ->get();




       


        

        return view('conferences.papers.assign',compact('conference','assigned','reviewers1','reviewers2','paper','conflictUsers'));

    }

    public function storeAssign($acronym,$edition,Paper $paper,Request $request){

        $conf = new Conference;
        $conference = $conf->getConference($acronym,$edition,'AC');

        $this->validate(request(), [
            'reviewers'     =>  ['required'],
            'reviewers.*'     =>  ['max:255','regex:/^[^<>]+$/'],
        ]);

        if($request->assigned != 0){
            \DB::table('paper_user')->where('paper_user.paper_id' , '=' , $paper->id)->delete();                      
        }

        if(count($request->reviewers) > $conference->nb_reviewer_per_item)
            return back()->withErrors([
                        'message' => "please max ".$conference->nb_reviewer_per_item." reviewers"
                    ]);

        foreach($request->reviewers as $rev){
         $is_rev = \DB::table('users')
                            ->join('paper_user' , 'users.id' ,'=', 'paper_user.user_id' )
                            ->join('papers' , 'papers.id' , '=' , 'paper_user.paper_id')
                            ->where('paper_user.user_id' , '=' , $rev)
                            ->where('paper_user.paper_id' , '=' , $paper->id)
                            ->count();  

        if($is_rev!=0)
                return back()->withErrors([
                        'message' => "reviewer already assigned for the paper ".$paper->id
                    ]);
        }

        $count_papers = \DB::table('users')
                            ->join('paper_user' , 'users.id' ,'=', 'paper_user.user_id' )
                            ->join('papers' , 'papers.id' , '=' , 'paper_user.paper_id')
                            ->where('paper_user.paper_id' , '=' , $paper->id)
                            ->count();

        if($count_papers>=$conference->nb_reviewer_per_item)
                return back()->withErrors([
                        'message' => "reviewers already assigned for the paper ".$paper->id
                    ]);

        foreach($request->reviewers as $rev){
             \DB::table('paper_user')->insert([
                    'user_id'=>$rev,
                    'paper_id'=>$paper->id
             ]);
        }

        return redirect()->route('conferences.papers.index',[$acronym,$edition]);
    }

    public function saveCamReady($acronym,$edition){

        $conf = new Conference;
        $conference = $conf->getConference($acronym,$edition,'AC');

        if( ($conference->camReady != 'Y')or($conference->is_cam_ready_open != 'Y') ){
            return back();
        }

        $papers = \DB::table('paperstatuses')
                    ->join('papers' , 'papers.paperstatus_id' ,'=', 'paperstatuses.id' )
                    ->where('papers.conference_id' , $conference->id)
                    ->where('paperstatuses.label' , 'Accepted')
                    ->select('papers.*')
                    ->get();


        if(!is_dir('papers/CR_ACCEPTED/')){
            $result = \File::makeDirectory('papers/CR_ACCEPTED/');
        }
        foreach($papers as $p){
            $destinationPath = 'papers/'.$p->id;
            \File::copy($destinationPath,'papers/CR_ACCEPTED/'.$p->id);
        }

        return back();
        


        return $papers;
        
    }

    public function uploadCr($acronym,$edition,Paper $paper){

        $conf = new Conference;
        $conference = $conf->getConference($acronym,$edition);

        $crRequired = \DB::table('paperstatuses')
                    ->join('papers' , 'papers.paperstatus_id' ,'=', 'paperstatuses.id' )
                    ->where('papers.id' , $paper->id)
                    ->select('paperstatuses.*')
                    ->first();
        

        if (count($conference) == 0){
            $conference = \App\Conference::where('confAcronym' , '=' , $acronym)
                                        ->where('confEdition' , '=' , $edition)
                                        ->first();
        }

        $authors=\DB::table('papers')
                        ->join('authors','authors.paper_id','papers.id')
                        ->where('papers.id',$paper->id)->get();

        $questions = Paperquestion::where('conference_id',$conference->id)->get();
        $pqchoice =  \DB::table('pqchoices')
                        ->select('*')
                        ->orderBy('position')
                        ->get();
        
        $paper_pq = \DB::table('paper_paperquestion')
                            ->join('pqchoices' , 'paper_paperquestion.pqchoice_id' ,'=', 'pqchoices.id' )
                            ->join('paperquestions' , 'paper_paperquestion.paperquestion_id' ,'=', 'paperquestions.id' )
                            ->where('paper_paperquestion.paper_id' , '=' , $paper->id)
                            ->get();
        

        

        return view('conferences.papers.uploadCr',compact('paper_pq','authors','paper','conference','questions','pqchoice','crRequired'));

    }

    public function storeCr($acronym,$edition,Paper $paper,Request $request){

        //return $request->all();
        $conf = new Conference;
        $conference = $conf->getConference($acronym,$edition);

        if (count($conference) == 0){
            $conference = \App\Conference::where('confAcronym' , '=' , $acronym)
                                        ->where('confEdition' , '=' , $edition)
                                        ->first();
        }

        $file_type=$conference->file_type;
  
        $first = request('first');

        $this->validate(request(), [
            'title'     =>  ['required','min:5','max:255','regex:/^[^<>]+$/'],
            'abstract'  =>  ['required','min:10'],
            'keywords'  =>  ['required','max:255','regex:/^[^<>]+$/'],
            'captcha'   =>  ['required','captcha'],
            'first.*'   =>  ['required','min:2','max:255','regex:/^[^<>]+$/'],
            'last.*'    =>  ['required','min:2','max:255','regex:/^[^<>]+$/'],
            'aff.*'     =>  ['required','min:1','max:255','regex:/^[^<>]+$/'],
            'grade.*'     =>  ['required','min:1','max:255','regex:/^[^<>]+$/'],
            'email.*'   =>  ['required','min:4','max:255','regex:/^[^<>]+$/'],
            'country.*'  =>  ['required','min:2','max:255','regex:/^[^<>]+$/'],
            'question.*' =>['required','min:1','max:255','regex:/^[^<>]+$/'],
            'file'         =>['required','file','mimes:'.$file_type],
            'correspond.*'  =>  ['required','min:1','max:10'],
            ]);

            $paper->title=$request->title;
            $paper->abstract=$request->abstract;
            $paper->keywords=$request->keywords;

            $paper->save();





        $v = (count($request->email) != count(array_unique($request->email)));
                if($v){
                     return back()->withErrors([
                        'message' => "duplicate authors"
                    ]);
                }

 



        //Handle Authors
            
            $first = request('first');
            $last = request('last');
            $aff = request('aff');
            $grade = request('grade');
            $email = request('email');
            $country = request('country');
            $n = count($first);
            
            $countV =0 ;
            
            for($i=0;$i<count($request->is_correspond);$i++){
                if ($request->is_correspond[$i] == 1)
                    $countV++;
            }

            if($countV != 1){
                return back()->withErrors([
                        'message' => "select one correspond author"
                    ]);
            }


            $v = (count($email) != count(array_unique($email)));
                if($v){
                     return back()->withErrors([
                        'message' => "duplicate authors"
                    ]);
                }


            \DB::table('authors')->where('paper_id',$paper->id)->delete();



            for($i=0;$i<$n;$i++)
            {
                if($request->is_correspond[$i] == 1){
                    \DB::table('authors')->insert([
                    'first_name'=>$first[$i],
                    'last_name'=>$last[$i],
                    'affilation'=>$aff[$i],
                    'grade'=>$grade[$i],
                    'email'=>$email[$i],
                    'country'=>$country[$i],
                    'paper_id'=> $paper->id,
                    'is_corresponding' => '1'
                ]);
                    
                }else{
                    
                    \DB::table('authors')->insert([
                    'first_name'=>$first[$i],
                    'last_name'=>$last[$i],
                    'affilation'=>$aff[$i],
                    'grade'=>$grade[$i],
                    'email'=>$email[$i],
                    'country'=>$country[$i],
                    'paper_id'=> $paper->id
                ]);
                }

                if(\App\User::where('email',$email[$i])->count() != 0 ){
                        
                        if(\DB::table('conferences')
                            ->join('conference_user' , 'conferences.id' , 'conference_user.conference_id' )
                            ->join('users' , 'users.id' , '=' , 'conference_user.user_id')
                            ->where('users.email',$email[$i])
                            ->where('conference_user.conference_id',$conference->id)
                            ->where('conference_user.role','Aut')
                            ->count() == 0){

                                $userId = \App\User::where('email',$email[$i])->first();

                                \DB::table('conference_user')->insert([
                                        'user_id'=>$userId->id,
                                        'role'=>'Aut',
                                        'conference_id'=> $conference->id
                                    ]);

                            }
                    }
                
            }

            //Handle Topics

            $n = count(request('topics'));


             if($n == 0){
                     return back()->withErrors([
                        'message' => "select at least one topic"
                    ]);
                }

            \DB::table('paper_topic')->where('paper_id',$paper->id)->delete();

            for($i=0;$i<$n;$i++)
            {
                \DB::table('paper_topic')->insert([
                    'topic_id'=>request('topics')[$i],
                    'paper_id'=> $paper->id
                ]);
            }
            
            //Handle pquestions

            $rqs = \App\Paperquestion::where('conference_id',$conference->id)->get();

            \DB::table('paper_paperquestion')->where('paper_id',$paper->id)->delete();

            foreach($rqs as $pq){
                $idp = $pq->id ;

                \DB::table('paper_paperquestion')->insert([
                        'paper_id' => $paper->id,
                        'paperquestion_id' => $pq->id,
                        'pqchoice_id'=> $request->$idp

                    ]);
            }

            


            //Handle the file

            $status = \DB::table('paperstatuses')
                    ->join('papers' , 'papers.paperstatus_id' ,'=', 'paperstatuses.id' )
                    ->where('papers.id' , $paper->id)
                    ->select('paperstatuses.label')
                    ->first();

            $label = strtoupper($status->label);

            $file = $request->file;

            $fileName=$paper->id;
            
            $destinationPath = 'papers/CR_'.$label.'/'.$fileName;
            $uploaded = Storage::put($destinationPath, file_get_contents($file->getRealPath()));

            return redirect()->route('home');

            



    }


}
