@extends('layouts.app')

@section('content')
@push('style')
<style>
.day{
    padding: 20px;
}
.minimize-day,.minimize-slot,.minimize-session{
    cursor:pointer;
    margin:5px
}


.minimize-day h4{
    color: #00b5ad !important;
}

.hide{
    display:none;
}

.slot{
    padding:5px 0 5px 20px
}

.session{
    padding:5px 0 5px 40px
}

.content-session{
    padding:5px 0 5px 40px
}
</style>
@endpush

<div class="ui container">
{!! Breadcrumbs::render('preview',$conference) !!}
    <h2 class="ui header ">Program of {{$conference->confName}} {{$conference->confEdition}} 
        <a href="{{route('conferences.slots.docProgram',[$conference->confAcronym,$conference->confEdition])}}" class="blue button ui  pull-right"> generate .docx program</a>
    </h2>
    <?php $i = 1 ?>
    @foreach($days as $day)
    
    <div class="day">
        <div class="minimize-day">
            <h4 class="ui horizontal divider header">
                <i class="calendar icon"></i>
                <span style="margin:10px;display:inline-block">Day {{$i}}: {{$day->date}}</span>
                <span class="hide"><i class="chevron up icon"></i></span>
                <span><i  class="chevron down icon"></i></span>
            </h4>
            
            
        </div>
        <div class="content-day">
        <?php  $slots = App\Slot::where('conference_id',$conference->id)->where('date',$day->date)->orderBy('begin')->get(); ?>
             @foreach($slots as $slot)
             <?php $sessions = App\Session::where('slot_id',$slot->id)->orderBy('position')->get(); ?>
            <div class="slot">
                @if(count($sessions) != 0)
                    <div class="minimize-slot">
                        <span class="hide"><i class="caret right icon"></i></span>
                        <span><i  class="caret down icon"></i></span>
                        <strong style="color:#21ba45">{{$slot->name}}</strong> 
                        <label class="ui label mini">{{date('G:i', strtotime($slot->begin))}} - {{date('G:i', strtotime($slot->end))}}</label>
                    </div>
                @else
                    <div style="padding-left:28px">
                        <strong style="color:#21ba45">@if($slot->type == 2) Break @endif</strong>
                        <label class="ui label mini">{{date('G:i', strtotime($slot->begin))}} - {{date('G:i', strtotime($slot->end))}}</label>                        
                    </div>
                @endif
                <div class="content-slot">
                    
                      @foreach($sessions as $session)
                     <div class="session">
                        <div class="minimize-session">
                            <span class="hide"><i class="caret right icon"></i></span>
                            <span><i  class="caret down icon"></i></span>
                            {{$session->name}}, ({{$session->capacity}}), room: {{$session->room}}
                        </div>
                        <div class="content-session">
                        <?php $papers= App\Paper::where('session_id',$session->id)->orderBy('pos_in_session')->get(); 
                        $papers = \DB::table('papers')
                                    ->join('authors','authors.paper_id','papers.id')
                                    ->where('authors.is_corresponding',1)
                                    ->where('session_id',$session->id)
                                    ->groupBy('papers.id')
                                    ->orderBy('pos_in_session')
                                    ->select('authors.*','papers.*','authors.id as authId')
                                    ->get();
                        ?>
                            <div class="ui list">
                                @foreach($papers as $paper)
                                    <div class="item">
                                    <i class="file icon"></i>
                                    <div class="content">
                                        <div class="header"><a href="{{route('conferences.papers.show',[$conference->confAcronym,$conference->confEdition,$paper->id])}}">{{$paper->title}}</a></div>
                                        <div class="description">By : 
                                        <a href="{{route('conferences.authors.papers',[$conference->confAcronym,$conference->confEdition,$paper->authId])}}">{{$paper->first_name}} {{$paper->last_name}}</a>
                                        </div>
                                    </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <?php $i++ ?>

    @endforeach

@push('script')
<script>

$( function() {
    $('.minimize-day').click(function(){
        $(this).parent().find('.content-day')
            .stop(true, true)
            .animate({
                height:"toggle",
                opacity:"toggle"
            },500);
        $(this).find('span').toggleClass("hide");

    });

    $('.minimize-slot').click(function(){
        $(this).parent().find('.content-slot')
            .stop(true, true)
            .animate({
                height:"toggle",
                opacity:"toggle"
            },500);
        $(this).find('span').toggleClass("hide");

    });

    $('.minimize-session').click(function(){
        $(this).parent().find('.content-session')
            .stop(true, true)
            .animate({
                height:"toggle",
                opacity:"toggle"
            },500);
        $(this).find('span').toggleClass("hide");

    });



});

</script>
@endpush

@endsection