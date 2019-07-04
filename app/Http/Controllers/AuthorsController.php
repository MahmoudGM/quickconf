<?php

namespace App\Http\Controllers;
use App\Http\Middleware\CheckIfAdmin;
use App\Http\Middleware\CheckIfAuthor;

use Illuminate\Http\Request;
use Datatables;
use App\Author;
use App\User;
use App\Paper;

class AuthorsController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
        $this->middleware(CheckIfAdmin::class)->except('getPapersAuthors','myPapers');
        $this->middleware(CheckIfAuthor::class)->only('getPapersAuthors','myPapers','editPaper');

    }



    public function getData($acronym,$edition, Request $request){

        $lang=$request->session()->get('lang');
        $PAPERS = parse_ini_file(base_path('language/'.$lang.'/AUTHORS.ini'));
        $conference = \App\Conference::where('confAcronym' , '=' , $acronym)
                                        ->where('confEdition' , '=' , $edition)
                                        ->first();

        $GLOBALS['C'] = $conference;
        $GLOBALS['M'] = $PAPERS;
        //return $GLOBALS['C'];
       $authors = \DB::table('papers')
                    ->join('authors','authors.paper_id','papers.id')
                    ->where('papers.conference_id' , '=' , $conference->id)
                    ->select('authors.*')
                    ->groupBy('authors.email')
                    ->orderBy('authors.id','DESC')
                    //->latest('papers.created_at')
                    ->get();
        //return $authors;

        $GLOBALS['L'] = $lang;
        return Datatables::of($authors)->addColumn('action', function ($author) {
                    return ' <a href="/conferences/'.$GLOBALS['C']->confAcronym.'/'.$GLOBALS['C']->confEdition.'/authors/'.$author->id.'/papers" class="button ui primary"><i class="book icon"></i>  '.$GLOBALS['M']['PAPERS'].'</a>
                            <input type="hidden" value="'.$author->email.'" class="mailInput" name="idForMail[]"> ';
                },5)
                ->make(true);

}

public function getPapersAuthors($acronym,$edition, Request $request){

        $lang=$request->session()->get('lang');
        $AUTH = parse_ini_file(base_path('language/'.$lang.'/AUTHORS.ini'));

        $conference = \App\Conference::where('confAcronym' , '=' , $acronym)
                                        ->where('confEdition' , '=' , $edition)
                                        ->first();

        $GLOBALS['C'] = $conference;
        $GLOBALS['L'] = $AUTH;
        $GLOBALS['LN'] = $AUTH;

        /*$papers = \DB::table('papers')
                    ->join('paper_topic' , 'papers.id' ,'=', 'paper_topic.paper_id' )
                    ->join('topics' , 'topics.id' , '=' , 'paper_topic.topic_id')
                    ->join('authors','authors.paper_id','=','papers.id')
                    ->where('authors.email' , '=' , auth()->user()->email)
                    ->where('papers.conference_id' , '=' , $conference->id)
                    ->select('topics.*','authors.*','papers.*','authors.id as authorId')
                    ->groupBy('papers.id')
                    ->orderBy('papers.created_at','DESC')
                    ->get();*/

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

        $GLOBALS['L'] = $lang;
        return Datatables::of($papers)->addColumn('action', function ($paper) {
                    if($paper->is_corresponding == 0){
                        return '<a href="/conferences/'.$GLOBALS['C']->confAcronym.'/'.$GLOBALS['C']->confEdition.'/papers/'.$paper->id.'" data-tooltip="show" class="button ui green"><i class="eye icon"></i></a>';
                    }
                    else{

                        return ' <form id="formDeletePaper" action="/conferences/'.$GLOBALS['C']->confAcronym.'/'.$GLOBALS['C']->confEdition.'/papers/'.$paper->id.'/delete" method = "POST">
                                    <a href="/conferences/'.$GLOBALS['C']->confAcronym.'/'.$GLOBALS['C']->confEdition.'/papers/'.$paper->id.'" data-tooltip="Show" class="button ui green"><i class="eye icon"></i></a>
                                    <a href="/conferences/'.$GLOBALS['C']->confAcronym.'/'.$GLOBALS['C']->confEdition.'/authors/papers/'.$paper->id.'/edit" data-tooltip="Edit" class="button ui teal"><i class="edit icon"></i></a>
                                    <button id="btnDeletePaper" type="submit" data-tooltip="Delete" class="button ui red"><i class="delete icon"></i></button>
                                </form>
                                ';

                    }
                },5)
                 ->addColumn('camReady',function($paper){
                    if($paper->camReadyRq === 1){
                        if($GLOBALS['C']->camReady == 'Y'){
                            if ((\File::exists('papers/CR_'.strtoupper($paper->psLabel))) and ($GLOBALS['C']->is_cam_ready_open == 'Y') ){
                                return '<a class="button ui primary mini" href="/conferences/'.$GLOBALS['C']->confAcronym.'/'.$GLOBALS['C']->confEdition.'/papers/'.$paper->id.'/download/1" data-tooltip="'.$GLOBALS['LN']['DOWN_CR_BTN'].'">  <i class="icon download cloud"></i></a> </h2>
                                        <a class="button ui mini" href="/conferences/'.$GLOBALS['C']->confAcronym.'/'.$GLOBALS['C']->confEdition.'/papers/'.$paper->id.'/camReady" data-tooltip="'.$GLOBALS['LN']['UP_BTN'].'"><i class="icon cloud upload"></i></a>
                                        ';
                            }elseif((\File::exists('papers/CR'.$paper->psLabel)) and ($GLOBALS['C']->is_cam_ready_open == 'N') ){
                                return '<a class="button ui primary" href="/conferences/'.$GLOBALS['C']->confAcronym.'/'.$GLOBALS['C']->confEdition.'/papers/'.$paper->id.'/download/CR/1" data-tooltip="'.$GLOBALS['LN']['DOWN_CR_BTN'].'">  <i class="icon download cloud"></i></a> </h2>
                                        ';
                            }elseif(!(\File::exists('papers/CR'.$paper->psLabel)) and ($GLOBALS['C']->is_cam_ready_open == 'Y') ){
                                if( \Carbon\Carbon::now()->format('Y-m-d') <= $GLOBALS['C']->cam_ready_deadline)
                                    return '<a class="button ui mini" href="/conferences/'.$GLOBALS['C']->confAcronym.'/'.$GLOBALS['C']->confEdition.'/papers/'.$paper->id.'/camReady" data-tooltip="'.$GLOBALS['LN']['UP_BTN'].'"><i class="icon cloud upload"></i></a>';
                            }
                        }
                    }
                })
                ->rawColumns(['camReady','action'])
                ->make(true);

}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($acronym,$edition,Request $request)
    {


        $conf = new \App\Conference;
        $conference = $conf->getConference($acronym,$edition);

        return view('conferences.authors.index',compact('conference'));


    }

    public function papersAuthor($acronym,$edition,Author $author){

        $conf = new \App\Conference;
        $conference = $conf->getConference($acronym,$edition);


            $papers = \DB::table('papers')
                    ->join('authors','authors.paper_id','papers.id')
                    ->join('paper_topic','paper_topic.paper_id','papers.id')
                    ->join('topics','topics.id','paper_topic.topic_id')
                    ->where('papers.conference_id' , '=' , $conference->id)
                    ->where('authors.email' , '=' , $author->email)
                    ->groupBy('paper_topic.paper_id')
                    ->select('authors.*','papers.*','topics.label')
                    ->latest('papers.created_at')
                    ->get();

            $authors=\App\Author::all();

            return view('conferences.authors.papers',compact('conference','papers','author','authors'));


    }

    public function myPapers($acronym,$edition){
        
        $conf = new \App\Conference;
        $conference = $conf->getConference($acronym,$edition,'author');

        if ( count($conference) == 0 )
            return redirect()->route('notfound',['404']);


        $papers = \DB::table('papers')
                    ->join('authors','authors.paper_id','papers.id')
                    ->where('papers.conference_id' , '=' , $conference->id)
                    ->where('authors.email' , '=' , auth()->user()->email)
                    ->select('authors.*','papers.*')
                    ->latest('papers.created_at')
                    ->get();

        if (count($papers) == 0 )
            return redirect()->route('notfound',['404']);

        return view('conferences.authorsUsers.mypapers',compact('conference','papers'));
    }

    public function editPaper($acronym,$edition,Paper $paper)
    {
        $conf = new \App\Conference;
        $conference = $conf->getConference($acronym,$edition,'author');

        $questions=\DB::table('papers')
                    ->join('paper_paperquestion','paper_paperquestion.paper_id','papers.id')
                    ->join('paperquestions','paper_paperquestion.paperquestion_id','paperquestions.id')
                    ->join('pqchoices','paper_paperquestion.pqchoice_id','pqchoices.id')
                    ->groupBy('paperquestions.question')
                    ->where('paper_paperquestion.paper_id',$paper->id)
                    ->get();
        //return $questions;

        $topics = \DB::table('papers')
                    ->join('paper_topic' , 'papers.id' ,'=', 'paper_topic.paper_id' )
                    ->join('topics' , 'topics.id' , '=' , 'paper_topic.topic_id')
                    ->where('papers.id',$paper->id)
                    ->select('topics.*','paper_topic.paper_id')
                    //->groupBy('topics.label')
                    ->get();

        $authors = \DB::table('papers')
                    ->join('authors','authors.paper_id','papers.id')
                    ->where('authors.paper_id',$paper->id)
                    ->get();
        //return $authors;

        return view('conferences.authorsUsers.editPaper',compact('conference','paper','questions','topics','authors'));
    }





}
