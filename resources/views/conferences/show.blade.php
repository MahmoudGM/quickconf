@extends('layouts.app')

<?php
$lang = Session::get('lang');
$C = parse_ini_file(base_path('language/'.$lang.'/CONFERENCE.ini'));
?>

<title>{{$conference->confAcronym}} {{$conference->confEdition}}</title>
@section('content')

@push('style')
<style>

.boxes{
    margin-top: 30px;
    //padding-left: 44px;
    overflow: hidden;
    width: 100%;
}

.boxes>a>div{
    color:#ddd;
    background-color: #1abc9c;
    padding-top:30px;
    text-align:center;
    height: 120px;
    float: left;
    width: 22%;
    margin-left: 2%;
    margin-right: 2%;
    cursor:pointer;
    transition: background-color .3s ease-in
}



.boxes .first-div
{
    margin-left: 0%;
}

.boxes .last-div
{
    margin-right: 0%;
}

.boxes>a>div:hover{
    background-color:#ddd;
    color: #1abc9c;
}

.boxes>a>div span{
    display:block;
}
.boxes>a>div span:first-child{
    font-size:40px;
    margin-bottom:25px
}

 .panel .header
{
    background-color:#3498db;
    color:#fff
}
.panel .body{
    padding:0
}

.panel .body div{
    padding:10px
}

 .panel .body div:nth-child(even){
    background-color:#eee
}
 .panel .body div:nth-child(odd){
    background-color:#fff
}

.charts{
    overflow:hidden;
}

.pie-chart{
    width:30%;
    height:390px;
    margin-top:20px;
    float:left;
    background-color:#eee
}

.bar-chart{
    width:67%;
    height:390px;
    margin-top:20px;
    float:left;
    margin-left:3%;
    background-color:#eee;
    padding-top: 76px;
    
}





</style>
@endpush
<div class="container ui">
        {!! Breadcrumbs::render('homeConf',$conference,'home') !!}
        <h2 class="ui header dividing">{{ $conference->confName }} {{ $conference->confEdition }} </h2>
   

            <div class="boxes">
                <a href="{{route('conferences.comite.index',[$conference->confAcronym,$conference->confEdition])}}">
                    <div class="first-div">
                        <span>{{$cchairs}}</span>
                        <span><i class="ui icon spy large"></i>{{$C['CHAIRS']}}</span>
                    </div>
                </a>
            <a href="{{route('conferences.comite.index',[$conference->confAcronym,$conference->confEdition])}}">
                    <div>
                        <span>{{$crevs}}</span>
                        <span><i class="ui icon users large"></i>{{$C['REVS']}}</span>
                    </div>
                </a>
            <a href="{{route('conferences.papers.index',[$conference->confAcronym,$conference->confEdition])}}">            
                <div>
                    <span>{{$cpapers}}</span>
                    <span><i class="ui icon newspaper large"></i>{{$C['PAPERS']}}</span>
                </div>
            </a>
            <a href="{{route('conferences.authors.index',[$conference->confAcronym,$conference->confEdition])}}">            
                <div class="last-div">
                    <span>{{$cauthors}}</span>
                    <span><i class="ui icon write large"></i>{{$C['AUTH']}}</span>
                </div>
            </a>

            </div>

            <h3 class="ui header dividing">{{$C['STATICS']}}</h3>
            <div class="charts">
                    <div class="pie-chart">
                    <canvas id="myChart" ></canvas>
                    <center><h4 style="margin-top: 16px;">{{$C['H-STATUS']}}</h4></center>
                    </div>
                    <div class="bar-chart">
                        <canvas id="myChart1" width="600" height="200"></canvas>
                        <center><h4 style="margin-top: 25px;">{{$C['H-MONTH']}}</h4></center>
                    </div>
                </div>

            

            

        {{--
        <div class="history" style="margin-top:40px">
            <div class="panel">
                <div class="header">
                    <h3>{{$C['HIST']}}</h3>
                </div>
                <div class="body">
                    <div>History1</div>
                    <div>History1</div>
                    <div>History1</div>

                
                </div>
            </div>
        </div>
        --}}

        <h3 class="ui header dividing">{{$C['H-RECENT']}}</h3>
                <div class="panel">
                    <div class="header">
                        <h3>{{$C['LAST_P']}}</h3>
                    </div>
                    <div class="body">
                    @foreach($lastPapers as $lp)
                        <div>
                            <a href="{{route('conferences.papers.show',[$conference->confAcronym,$conference->confEdition,$lp->id] )}}">{{$C['PAPER']}} {{$lp->id}}</a>
                            <a href="{{route('conferences.papers.assign',[$conference->confAcronym,$conference->confEdition,$lp->id] )}}" style="margin-top:-5px" class="label ui pull-right teal" data-tooltip="{{$C['ASSIGN']}}"><i class="add user icon"></i></a>
                            <label style="margin:-5px 5px 0 0" class="ui label grey pull-right">{{$C['AUTHOR']}}: {{$lp->first_name}} {{$lp->last_name}}</label>
                            <label style="margin:-5px 5px 0 0" class="ui label grey pull-right">{{$C['TOPIC']}}: {{$lp->acronym}}</label>
                            @if($lp->psLabel != null)
                                <label style="margin:-5px 5px 0 0" class="ui label grey pull-right">{{$C['STATUS']}}: {{$lp->psLabel}}</label>
                            @endif
                        </div>
                    @endforeach

                    
                    </div>
                </div>

     
                <div class="panel" style="margin-bottom:80px">
                    <div class="header">
                        <h3>{{$C['LAST_R']}}</h3>
                    </div>
                    <div class="body">
                    @foreach($lastReviews as $lr)
                        <div>
                            <a href="{{route('conferences.comite.showReview',[$conference->confAcronym,$conference->confEdition,$lr->paper_id,$lr->user_id] )}}">{{$C['R_FOR']}} {{$lr->paper_id}}</a>
                            <label style="margin-top:-5px" class="ui grey label pull-right">{{$C['REV']}}: {{$lr->first_name}} {{$lr->last_name}}</label>
                            <label style="margin-top:-5px" class="ui grey label pull-right">{{$lr->overall}} <i class="icon star"></i></label>
                        </div>
                    @endforeach

                    
                    </div>
                </div>


</div>

<?php
$assignedPapersRv = \DB::table('papers')
                ->join('paper_user' , 'papers.id' ,'=', 'paper_user.paper_id' )
                ->where('papers.conference_id' , $conference->id )
                ->where('is_reviewed' , 1 )
                ->groupBy('paper_user.paper_id')->get();

$assignedPapersNr = \DB::table('papers')
                ->join('paper_user' , 'papers.id' ,'=', 'paper_user.paper_id' )
                ->where('papers.conference_id' , $conference->id )
                ->where('is_reviewed' , 0 )
                ->groupBy('paper_user.paper_id')->get();

$papers = \App\Paper::where('conference_id',$conference->id)->get();

$papersWithoutStatus = \App\Paper::where('paperstatus_id',null)->where('conference_id',$conference->id)->count();

$array=[];

$paperSt=\App\Paperstatus::where('conference_id',$conference->id)->get();

foreach($paperSt as $ps){
    $array[$ps->label] =  \App\Paper::where('paperstatus_id',$ps->id)->count();
}



?>

@push('script')
<script src="{{ asset('js/Chart.min.js') }}"></script>
<script>

var ctx = document.getElementById("myChart");

var myPieChart = new Chart(ctx,{
    type: 'pie',
    data: {
        labels: ["{{$C['P_WITHOUT']}}", 
                    @foreach($paperSt as $ps)
                        "{{$ps->label}}",
                    @endforeach
                ],
        datasets: [{
            data: [{{ $papersWithoutStatus }}, 
                    @foreach($array as $ar)
                     {{$ar}},
                    @endforeach
                    ],
            backgroundColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
            ],

            borderWidth: 1
        }]
    },
    
  
});

var ctx1 = document.getElementById("myChart1");

var myLineChart = new Chart(ctx1, {
    type: 'line',
    data: {
        labels: ["{{$C['1']}}", "{{$C['2']}}", "{{$C['3']}}", "{{$C['4']}}", "{{$C['5']}}", "{{$C['6']}}","{{$C['7']}}","{{$C['8']}}","{{$C['9']}}","{{$C['10']}}","{{$C['11']}}","{{$C['12']}}"],
        datasets: [{
            label: '{{$C['NUMBER']}}',
            data: [
                @for($i=1;$i<=12;$i++)
                    <?php 
                        
                        $ps = \App\Paper::where('conference_id',$conference->id)
                                        ->whereYear('created_at', '=', date("Y"))
                                        ->whereMonth('created_at', '=', $i)
                                        ->count();
                        
                    ?>
                    {{$ps}},
                @endfor

            ],


            borderWidth: 1
        }]
    },
    options: {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero:true
                }
            }]
        }
    }

});

var ctx = document.getElementById("myChart2");

var myPieChart = new Chart(ctx,{
    type: 'line',
    data: {
        
        labels: [@foreach($conference->topics as $tp)
                        "{{$tp->acronym}}",
                    @endforeach
                ],
        datasets: [{
            label: '{{$C['NUMBER']}}',
            data: [
                        {{$topics}}
                    ],
            
            borderWidth: 4,
            fill: false,
            borderColor: '#2185d0',
        }]
    },
    
  
});

</script>
@endpush

@endsection