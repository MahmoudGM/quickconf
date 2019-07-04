<?php

namespace App\Http\Controllers;
use Illuminate\Mail\Markdown;
use App\Http\Middleware\CheckIfAdmin;

use Illuminate\Http\Request;
use App\Conference;
use App\Messagetemp;
use App\Topic;
use DB;
use Mail;
use App\Mail\Confactivation;
use App\Mail\ConfDeleteRequest;
use URL;
use Session;

class ConferencesController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
        $this->middleware(CheckIfAdmin::class)->only('show');
    }
    public function create(Request $request)
    {
        return view('conferences.create');
    }

    public function store(Request $request)
    {

        $this->validate(request(), [
            'confAcronym'               =>  ['required','min:4','max:10','regex:/^[A-Z]+$/','unique:conferences'],
            'confName'                  =>  ['required','min:10','max:80','regex:/^[^<>]+$/'],
            'country'                   =>  ['required','max:80','regex:/^[^<>]+$/','string'],
            'city'                      =>  ['required','max:80','regex:/^[^<>]+$/','string'],
            'confAdress'                =>  ['required','min:8','max:255','regex:/^[^<>]+$/'],
            'confUrl'                   =>  ['required','min:5','max:255','url'],
            'confMail'                  =>  ['required','email','min:4','max:255','regex:/^[^<>]+$/'],
            'confEdition'               =>  ['required','numeric','min:1','regex:/^[^<>]+$/'],
            'submission_deadline'       =>  ['required','date','','regex:/^[^<>]+$/','before:start_date'],
            'review_deadline'           =>  ['required','date','','regex:/^[^<>]+$/','before:start_date'],
            'cam_ready_deadline'        =>  ['required','date','','regex:/^[^<>]+$/','before:start_date'],
            'start_date'                =>  ['required','date','','regex:/^[^<>]+$/','before:end_date'],
            'end_date'                  =>  ['required','date','','regex:/^[^<>]+$/','after:start_date'],
            'organizer'                 =>  ['required','min:3','max:80','regex:/^[^<>]+$/'],
            'organizerMail'             =>  ['email','min:4','max:255','regex:/^[^<>]+$/'],
            'organizerWebPage'          =>  ['url','min:3','max:80','regex:/^[^<>]+$/'],
            'phone'                     =>  ['required','size:11'],
            'researchArea'              =>  ['required','min:8','max:255','regex:/^[^<>]+$/'],
            'confDesc'                  =>  ['required','min:20'],

            ]);

        Conference::create([
            'confAcronym'=>request('confAcronym'),
            'confName'=>request('confName'),
            'country'=>request('country'),
            'city'=>request('city'),
            'confAdress'=>request('confAdress'),
            'confUrl'=>request('confUrl'),
            'confMail'=>request('confMail'),
            'confEdition'=>request('confEdition'),
            'submission_deadline'=>request('submission_deadline'),
            'review_deadline'=>request('review_deadline'),
            'cam_ready_deadline'=>request('cam_ready_deadline'),
            'start_date'=>request('start_date'),
            'end_date'=>request('end_date'),
            'organizer'=>request('organizer'),
            'organizerMail'=>request('organizerMail'),
            'organizerWebPage'=>request('organizerWebPage'),
            'phone'=>request('phone'),
            'researchArea'=>request('researchArea'),
            'confDesc'=>request('confDesc'),
            'chairMail'=>Auth()->user()->email,

        ]);

        $conf = new Conference;
        $confId = $conf->orderBy('id', 'DESC')->first();


        \DB::table('conference_user')->insert([
            'user_id'=>auth()->user()->id,
            'role'=>'A',
            'conference_id'=> $confId->id

        ]);


        $user = auth()->user()->toArray();
        $data = $request->all() + $user;
        Mail::to($user['email'])->send(new Confactivation($data));

        $lang=$request->session()->get('lang');
        $CONF = parse_ini_file(base_path('language/'.$lang.'/CONFERENCE.ini'));
        Session::flash('success_create_conf',$CONF['CREATE_SUCCESS']);

        return redirect('/');
    }

    public function show($acronym,$edition,Request $request)
    {
    
        $conf = new Conference;
        $conference = $conf->getConference($acronym,$edition);

        $cpapers=\App\Paper::where('conference_id',$conference->id)->count();
        $authors = \DB::table('papers')
                    ->join('authors','authors.paper_id','papers.id')
                    ->where('papers.conference_id' , '=' , $conference->id)
                    ->select('authors.*')
                    ->groupBy('authors.email')
                    ->orderBy('authors.id','DESC')
                    ->get();

       $cauthors = count($authors);

       $revs = \DB::table('conferences')
                    ->join('conference_user' , 'conferences.id' ,'=', 'conference_user.conference_id' )
                    ->join('users' , 'users.id' , '=' , 'conference_user.user_id')
                    ->where('conference_user.conference_id' , '=' , $conference->id)
                    ->where('conference_user.role' , '=' , 'R')
                    ->select('users.*','conference_user.role')
                    ->get();

        $chairs = \DB::table('conferences')
                    ->join('conference_user' , 'conferences.id' ,'=', 'conference_user.conference_id' )
                    ->join('users' , 'users.id' , '=' , 'conference_user.user_id')
                    ->where('conference_user.conference_id' , '=' , $conference->id)
                    ->where('conference_user.role' , '=' , 'C')
                    ->select('users.*','conference_user.role')
                    ->get();
        
        $crevs = count($revs);
        $cchairs = count($chairs);

        $lastPapers=\App\Paper::where('conference_id',$conference->id)->orderBy('id','desc')->take(5)->get();

        $lastPapers = \DB::select('select topics.*,authors.*, papers.*, paperstatuses.label as psLabel, paperstatuses.camReadyRequired as camReadyRq from papers
                                    INNER JOIN paper_topic ON papers.id = paper_topic.paper_id
                                    INNER JOIN topics ON topics.id = paper_topic.topic_id
                                    INNER JOIN authors ON authors.paper_id = papers.id
                                    LEFT OUTER JOIN paperstatuses ON paperstatuses.id = papers.paperstatus_id
                                    WHERE papers.conference_id = :confId
                                        and authors.is_corresponding = 1
                                    GROUP BY papers.id
                                    ORDER BY papers.created_at DESC
                                    LIMIT 5
                                    ',['confId' => $conference->id]);

        $lastReviews=\DB::table('reviews')
                        ->join('papers' , 'papers.id' , '=' , 'reviews.paper_id')
                        ->join('users' , 'users.id' , '=' , 'reviews.user_id')
                        ->where('papers.conference_id',$conference->id)
                        ->select('users.*','reviews.*')
                        ->orderBy('reviews.id','desc')->take(5)->get();
        
        $paper_topic=\DB::table('papers')
                        ->join('paper_topic' , 'papers.id' , '=' , 'paper_topic.paper_id')
                        ->where('papers.conference_id',$conference->id)
                        ->get();
        
        $array=array();
        
        foreach($conference->topics as $tp){
            $i=0;
            foreach($paper_topic as $pt)
                if($tp->id == $pt->topic_id)
                  $i++; 
                  $array[]=$i;
            }

            $topics = implode(',',$array);
  


        return view('conferences.show', compact('conference','cpapers','cauthors','crevs','cchairs','lastPapers','lastReviews','topics'));


    }

    public function edit($acronym,$edition,Request $req)
    {


        $conf = new Conference;
        $conference = $conf->getConference($acronym,$edition);
        if ( count($conference) == 0 )
        {
            return redirect()->route('notfound',['404']);
        }
        else
        {
            return view('conferences.edit', compact('conference'));
        }
    }

    public function update($acronym,$edition, Request $request)
    {
        $this->validate(request(), [
            'confAcronym'               =>  ['required','min:4','max:10','regex:/^[A-Z]+$/'],
            'confName'                  =>  ['required','min:10','max:80','regex:/^[^<>]+$/'],
            'country'                   =>  ['required','max:80','regex:/^[^<>]+$/','string'],
            'city'                      =>  ['required','max:80','regex:/^[^<>]+$/','string'],
            'confAdress'                =>  ['required','min:8','max:255','regex:/^[^<>]+$/'],
            'confUrl'                   =>  ['required','min:5','max:255','url'],
            'confMail'                  =>  ['required','email','min:4','max:255','regex:/^[^<>]+$/'],
            'confEdition'               =>  ['required','numeric','min:1','regex:/^[^<>]+$/'],
            'submission_deadline'       =>  ['required','date','','regex:/^[^<>]+$/','before:start_date'],
            'review_deadline'           =>  ['required','date','','regex:/^[^<>]+$/','before:start_date'],
            'cam_ready_deadline'        =>  ['required','date','','regex:/^[^<>]+$/','before:start_date'],
            'start_date'                =>  ['required','date','','regex:/^[^<>]+$/','before:end_date'],
            'end_date'                  =>  ['required','date','','regex:/^[^<>]+$/','after:start_date'],
            'organizer'                 =>  ['required','min:3','max:80','regex:/^[^<>]+$/'],
            'organizerMail'             =>  ['required','email','min:4','max:255','regex:/^[^<>]+$/'],
            'organizerWebPage'          =>  ['url','min:3','max:80','regex:/^[^<>]+$/'],
            'phone'                     =>  ['required','size:11'],
            'researchArea'              =>  ['required','min:8','max:255','regex:/^[^<>]+$/'],
            'confDesc'                  =>  ['required','min:20'],

            ]);

        $conf = new Conference;
        $conference = $conf->getConference($acronym,$edition);

        $conference->confName = request('confName');
        $conference->confAcronym = request('confAcronym');
        $conference->country = request('country');
        $conference->city = request('city');
        $conference->confAdress = request('confAdress');
        $conference->confUrl = request('confUrl');
        $conference->confMail = request('confMail');
        $conference->confEdition = request('confEdition');
        $conference->submission_deadline = request('submission_deadline');
        $conference->review_deadline = request('review_deadline');
        $conference->cam_ready_deadline = request('cam_ready_deadline');
        $conference->start_date = request('start_date');
        $conference->end_date = request('end_date');
        $conference->organizer = request('organizer');
        $conference->organizerMail = request('organizerMail');
        $conference->organizerWebPage = request('organizerWebPage');
        $conference->phone = request('phone');
        $conference->researchArea = request('researchArea');
        $conference->confDesc = request('confDesc');

        $conference->save();


        $lang=$request->session()->get('lang');
        $CONF = parse_ini_file(base_path('language/'.$lang.'/CONFERENCE.ini'));
        Session::flash('success_edit_conf',$CONF['EDIT_SUCCESS']);

        return redirect('/conferences/'.request('confAcronym').'/'.request('confEdition'));

    }

    //Function to create new conference from an old eidtion

    public function createFrom(Conference $conference, Request $request)
    {


      $this->validate(request(), [
          'submission_deadline'       =>  ['required','date','','regex:/^[^<>]+$/','before:start_date'],
          'review_deadline'           =>  ['required','date','','regex:/^[^<>]+$/','before:start_date'],
          'cam_ready_deadline'        =>  ['required','date','','regex:/^[^<>]+$/','before:start_date'],
          'start_date'                =>  ['required','date','','regex:/^[^<>]+$/','before:end_date'],
          'end_date'                  =>  ['required','date','','regex:/^[^<>]+$/','after:start_date'],
          ]);

         $confEdt =  Conference::where('confAcronym' , '=' , $conference->confAcronym)->orderBy('id', 'DESC')->first();
         //return $confEdt;
        Conference::create([
            'confAcronym'=>$conference->confAcronym,
            'confName'=>$conference->confName,
            'confUrl'=>$conference->confUrl,
            'confMail'=>$conference->confMail,
            'confEdition'=>$confEdt->confEdition+1,
            'confDesc'=>$conference->confDesc,
            'country'=>$conference->country,
            'chairMail'=>auth()->user()->email,
            'blind_review'=>$conference->blind_review,
            'extended_submission_form'=>$conference->extended_submission_form,
            'is_submission_open'=>$conference->is_submission_open,
            'is_cam_ready_open'=>$conference->is_cam_ready_open,
            'camReady'=>$conference->camReady,
            'discussion_mode'=>$conference->discussion_mode,
            'ballot_mode'=>$conference->ballot_mode,
            'upload_dir'=>$conference->upload_dir,
            'nb_reviewer_per_item'=>$conference->nb_reviewer_per_item,
            'mail_on_upload'=>$conference->mail_on_upload,
            'date_format'=>$conference->date_format,
            'submission_deadline'=>request('submission_deadline'),
            'review_deadline'=>request('review_deadline'),
            'cam_ready_deadline'=>request('cam_ready_deadline'),
            'start_date'=>request('start_date'),
            'end_date'=>request('end_date'),



        ]);



        $conf = new Conference;
        $confId = $conf->orderBy('id', 'DESC')->first();


        \DB::table('conference_user')->insert([
            'user_id'=>auth()->user()->id,
            'role'=>'A',
            'conference_id'=> $confId->id

        ]);

        //copy messages

        $msgs = Messagetemp::where('conference_id' , '=' , $conference->id)->get();


        $n = count($msgs);

        if($n!==0){

        for($i=0;$i<$n;$i++){
            Messagetemp::create([
                'name'=>$msgs[$i]->name,
                'title'=>$msgs[$i]->title,
                'body'=>$msgs[$i]->body,
                'conference_id'=>$confId->id,
            ]);
        }

        }

        //copy topics

        $topics = Topic::where('conference_id' , '=' , $conference->id)->get();

        $n = count($topics);

        if($n!==0){

        for($i=0;$i<$n;$i++){
            Topic::create([
                'label'=>$topics[$i]->label,
                'conference_id'=>$confId->id,

            ]);
        }

        }

        return redirect('/');

        /*

        Messagetemp::create([
            'title'=>request('title'),
            'type'=>request('type'),
            'body'=>request('body'),
            'conference_id'=>request('conference_id'),
        ]);

        return redirect('/');
        */
    }

    public function destroy(Conference $conference)
    {
        \DB::table('conferences')
                    ->where('id',$conference->id )
                    ->update(['is_deleted' => 1]);
        /*
        $admin=\App\Admin::all()->first()->toArray();
        $data = $conference->toArray() + $admin ;
        Mail::to($admin['email'])->send(new ConfDeleteRequest($data));
        */
        //$conference->delete();
        return redirect()->back();
    }

    public function editSubmission($acronym,$edition){
        $conf = new Conference;
        $conference = $conf->getConference($acronym,$edition);
        if ( count($conference) == 0 )
        {
            return redirect()->route('notfound',['404']);
        }
        else
        {
            return view('conferences.editSubmission', compact('conference'));
        }
    }

    public function updateSubmission($acronym,$edition,Request $request){

        $this->validate(request(), [
            'extended_submission_form'      =>  ['required','size:1','regex:/^[A-Z]+$/'],
            'file_type'                     =>  ['required','regex:/^[^<>]+$/'],
            'is_submission_open'            =>  ['required','size:1','regex:/^[^<>]+$/'],
            'is_cam_ready_open'             =>  ['required','size:1','regex:/^[^<>]+$/'],
            'camReady'                      =>  ['required','size:1','regex:/^[^<>]+$/'],
            'discussion_mode'               =>  ['required','size:1','regex:/^[^<>]+$/'],
            'ballot_mode'                   =>  ['required','size:1','regex:/^[^<>]+$/'],
            'blind_review'                  =>  ['required','size:1','regex:/^[^<>]+$/'],
            'nb_reviewer_per_item'          =>  ['required','numeric','min:1','regex:/^[^<>]+$/'],
            'mail_on_review'                =>  ['required','size:1','regex:/^[^<>]+$/'],
            'mail_on_upload'                =>  ['required','size:1','regex:/^[^<>]+$/'],

            ]);

        $conf = new Conference;
        $conference = $conf->getConference($acronym,$edition);

        $conference->extended_submission_form = request('extended_submission_form');
        $conference->file_type = request('file_type');
        $conference->is_submission_open = request('is_submission_open');
        $conference->is_cam_ready_open = request('is_cam_ready_open');
        $conference->camReady = request('camReady');
        $conference->discussion_mode = request('discussion_mode');
        $conference->ballot_mode = request('ballot_mode');
        $conference->blind_review = request('blind_review');
        $conference->nb_reviewer_per_item = request('nb_reviewer_per_item');
        $conference->mail_on_review = request('mail_on_review');
        $conference->mail_on_upload = request('mail_on_upload');

        $conference->save();


        $lang=$request->session()->get('lang');
        $CONF = parse_ini_file(base_path('language/'.$lang.'/CONFIG_SUB.ini'));
        Session::flash('success_edit_conf_sub',$CONF['EDIT_SUCCESS']);

        return redirect('/conferences/'.$acronym.'/'.$edition);

    }
}
