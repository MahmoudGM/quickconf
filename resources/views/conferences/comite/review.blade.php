@extends('layouts.app')
<title>Review paper {{$paper->id}}</title> 
@section('content')

@push('style')

<style>
select > .placeholder {
            display: none;
        }
.star .hover:hover{
    background-color:#eee !important;
}

.message{
    position:absolute
}

.message span{
    position: relative;
    background: #444;
    color: #fff;
    font-size: 11px;
    padding: 3px;
    border-radius: 5px;
    top: 20px;

}

.message span:nth-child(2){
    right: 200px;
    opacity:0

}

.message span:nth-child(3){
    right: 232px;
    opacity:0
}

.message span:nth-child(4){
    right: 258px;
    opacity:0
}

.message span:nth-child(5){
    right: 292px;
    opacity:0
}

.message span:nth-child(6){
    right: 321px;
    opacity:0
}

.message span:nth-child(7){
    right: 361px;
    opacity:0
}

.message span:nth-child(8){
    right: 390px;
    opacity:0
}

.show{
    opacity:1
}
.hide{
    opacity:0
}
</style>



@endpush

<div class="ui container">
{!! Breadcrumbs::render('paperRev',$conference,$paper) !!}
    @if ($is_reviewed == 0)
    <h2 class="ui header dividing">Review paper {{$paperInfo->id}}</h2>
    @include('layouts.errors')
    <div class="panel">
        <div class="header">
        Paper info
            <i class="icon minus square large blue pull-right min-div" style="cursor:pointer"></i>
            <i class="icon plus square large blue pull-right max-div" style="display:none;cursor:pointer"></i>
        </div>
        <div class="body">
            <label class="ui label" style="margin:0 5px 10px 0">Title: </label> {{$paperInfo->title}} <br> 
            <label class="ui label" style="margin:0 5px 0 0">Abstract: </label>
            <div style="border:1px solid #ddd;margin:10px 0 10px 0;border-radius:5px;padding:8px;background-color:#fff"> 
                {!!$paperInfo->abstract!!}  
            </div>
            <label class="ui label" style="margin:0 5px 10px 0">Topic: </label>{{$paperInfo->label}} <br>
            <label class="ui label" style="margin:0 5px 10px 0">Keywords: </label>{{$paperInfo->keywords}} <br>
        </div>
    </div>
    <form action="{{route('conferences.comite.storeReview',[$conference->confAcronym,$conference->confEdition,$paperInfo->id])}}" class="ui form" method="POST">
        <div class="panel">
            <div class="header">
                Criterias <label class="ui label teal small">( Strong Reject, Reject, Weak Reject, Neutral, Weak Accept, Accept, Strong Accept )</label>
                <i class="icon minus square large blue pull-right min-div" style="cursor:pointer"></i>
                <i class="icon plus square large blue pull-right max-div" style="display:none;cursor:pointer"></i>
            </div>
            <div class="body">

                @foreach($criterias as $criteria)

                    <div class="message ui ">
                        {{$criteria->label}}  <div class="ui star huge rating criteria" id="{{$criteria->id}}" ></div>
                            <span class="rate1">Strong Reject</span>
                            <span class="rate2">Reject</span>
                            <span class="rate3">Weak Reject</span>
                            <span class="rate4">Neutral</span>
                            <span class="rate5">Weak Accept</span>
                            <span class="rate6">Accept</span>
                            <span class="rate7">Strong Accept</span>
                    </div>

                    <input type="text" name="criteria{{$criteria->id}}" id="criteria{{$criteria->id}}" required style="display:none" tabindex="-1" >
                    {{$criteria->explanation}}

  

        @endforeach
            </div>
        </div>
        
    

        <div class="panel">
            <div class="header">
                Review expertise <label class="ui label teal small">( Low,Meduim,High )</label>
                <i class="icon minus square large blue pull-right min-div" style="cursor:pointer"></i>
                <i class="icon plus square large blue pull-right max-div" style="display:none;cursor:pointer"></i>
            </div>
            <div class="body expertise">
                <div class="ui message">
                    <div class="ui star huge rating expertise " ></div>
                    <span class="rate1" style="right:80px">Low</span>
                    <span class="rate2" style="right:90px">Meduim</span>
                    <span class="rate3" style="right:107px">High</span>
                </div>
                <input type="text" name="expertise" id="expertise" required style="display:none" tabindex="-1">
            
            </div>
        </div>

        <div class="panel">
            <div class="header">
            <label for="summary">Summary of contribution (shown to the authors)	</label>
                <i class="icon minus square large blue pull-right min-div" style="cursor:pointer"></i>
                <i class="icon plus square large blue pull-right max-div" style="display:none;cursor:pointer"></i>
            </div>
            <div class="body">
                <div class="field">
                    <textarea name="summary" id="summary" cols="30" rows="8"></textarea>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="header">
            <label for="details">Detailed comments (shown to the authors)</label>
                <i class="icon minus square large blue pull-right min-div" style="cursor:pointer"></i>
                <i class="icon plus square large blue pull-right max-div" style="display:none;cursor:pointer"></i>
            </div>
            <div class="body">
                <div class="field">
                    <textarea name="details" id="details" cols="30" rows="10"></textarea>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="header">
            <label>Questions</label>
                <i class="icon minus square large blue pull-right min-div" style="cursor:pointer"></i>
                <i class="icon plus square large blue pull-right max-div" style="display:none;cursor:pointer"></i>
            </div>
            <div class="body">
                @foreach($rquestions as $rq)
                    <div class="field required">
                        <label for="q{{$rq->id}}">{{$rq->question}}</label>
                        <select name="{{$rq->id}}" id="q{{$rq->id}}">
                            <option class="placeholder" value="" disabled selected >select</option>
                            @foreach($rqchoices as $rqc)
                                @if($rqc->reviewquestion_id == $rq->id)
                                    <option value="{{$rqc->id}}">{{$rqc->choice}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                @endforeach
            </div>
        </div>

         <div class="panel">
            <div class="header">
            <label for="comments">Comments for Program Committee (not shown to the authors)	</label>
                <i class="icon minus square large blue pull-right min-div" style="cursor:pointer"></i>
                <i class="icon plus square large blue pull-right max-div" style="display:none;cursor:pointer"></i>
            </div>
            <div class="body">
                <div class="field">
                    <textarea name="comments" id="comments" cols="30" rows="10"></textarea>
                </div>
            </div>
        </div>


        <div class="field text-center" style="margin:20px 0 40px 0">
            <button class="ui button primary" type="submit">Submit</button>
            <button id="reset" class="ui button " type="reset">Reset</button>
        </div>
        


        </form>

    @else 

        <h2 class="ui header dividing">Edit review paper {{$paperInfo->id}}  ({{$reviews->overall}}/7) </h2>
        @include('layouts.errors')

        <div class="panel">
            <div class="header">Paper informations</div>
            <div class="body">
                <label class="ui label" style="margin:0 5px 10px 0">Title: </label> {{$paperInfo->title}} <br> 
                <label class="ui label" style="margin:0 5px 0 0">Abstract: </label>
                <div style="border:1px solid #ddd;margin:10px 0 10px 0;border-radius:5px;padding:8px;background-color:#fff"> 
                    {!!$paperInfo->abstract!!}  
                </div>
                <label class="ui label" style="margin:0 5px 10px 0">Topic: </label>{{$paperInfo->label}} <br>
                <label class="ui label" style="margin:0 5px 10px 0">Keywords: </label>{{$paperInfo->keywords}} <br>
            </div>
        </div>

        <form action="{{route('conferences.comite.updateReview',[$conference->confAcronym,$conference->confEdition,$paperInfo->id])}}" class="ui form" method="POST">
        <div class="panel">
            <div class="header">
                Criterias <label class="ui label teal small">( Strong Reject, Reject, Weak Reject, Neutral, Weak Accept, Accept, Strong Accept )</label>
            </div>
            <div class="body">

                @foreach($rev_criterias as $criteria)

                    <div class="message ui crt{{$criteria->id}}">
                        {{$criteria->label}}  <div data-rating="{{$criteria->mark}}" class="ui star huge rating criteria cr{{$criteria->id}}" id="{{$criteria->id}}"></div>

                            <span class="rate1">Strong Reject</span>
                            <span class="rate2">Reject</span>
                            <span class="rate3">Weak Reject</span>
                            <span class="rate4">Neutral</span>
                            <span class="rate5">Weak Accept</span>
                            <span class="rate6">Accept</span>
                            <span class="rate7">Strong Accept</span>
 
                    </div>

                    <input style="display:none" type="text" value="{{$criteria->mark}}" name="criteria{{$criteria->id}}" id="criteria{{$criteria->id}}" required  tabindex="-1" >
                    {{$criteria->explanation}}
  

        @endforeach
            </div>
        </div>
        
    

        <div class="panel">
            <div class="header">
                Review expertise <label class="ui label teal small">( Low,Meduim,High )</label>
                <i class="icon minus square large blue pull-right min-div" style="cursor:pointer"></i>
                <i class="icon plus square large blue pull-right max-div" style="display:none;cursor:pointer"></i>
            </div>
            <div class="body r_expertise">
            <div class="ui message">
                    
                <div data-rating="{{$reviews->reviewExpertise}}" class="ui star huge rating expertise " ></div>
                <input style="display:none" value="{{$reviews->reviewExpertise}}" type="text" name="expertise" id="expertise" required tabindex="-1">
                <span class="rate1" style="right:80px">Low</span>
                <span class="rate2" style="right:90px">Meduim</span>
                <span class="rate3" style="right:107px">High</span>
            </div>
            </div>
        </div>

        <div class="panel">
            <div class="header">
            <label for="summary">Summary of contribution (shown to the authors)	</label>
            </div>
            <div class="body">
                <div class="field">
                    <textarea name="summary" id="summary" cols="30" rows="8">{{$reviews->summary}}</textarea>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="header">
            <label for="details">Detailed comments (shown to the authors)</label>
                <i class="icon minus square large blue pull-right min-div" style="cursor:pointer"></i>
                <i class="icon plus square large blue pull-right max-div" style="display:none;cursor:pointer"></i>
            </div>
            <div class="body">
                <div class="field">
                    <textarea name="details" id="details" cols="30" rows="10">{{$reviews->details}}</textarea>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="header">
            <label>Questions</label>
            <i class="icon minus square large blue pull-right min-div" style="cursor:pointer"></i>
                <i class="icon plus square large blue pull-right max-div" style="display:none;cursor:pointer"></i>
            </div>
            <div class="body">


            @foreach($paper_user_revq as $pur)
                <div class="field required">
                    <label for="q{{$pur->id}}">{{$pur->question}}</label>
                    <select name="{{$pur->reviewquestion_id}}" id="q{{$pur->reviewquestion_id}}">
                        <option class="placeholder" value="" disabled selected >select</option>
                        <option selected value="{{$pur->rqchoice_id}}">{{$pur->choice}}</option>
                        <?php
                            $rqchoices1 =  \DB::table('rqchoices')
                                            ->where('reviewquestion_id',$pur->reviewquestion_id)
                                            ->orderBy('position')
                                            ->get();
                        ?>
                        @foreach($rqchoices1 as $rqc)
                            @if($pur->rqchoice_id != $rqc->id)
                                <option value="{{$rqc->id}}">{{$rqc->choice}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            @endforeach
        </div>
        </div>

        <div class="panel">
            <div class="header">
            <label for="comments">Comments for Program Committee (not shown to the authors)	</label>
            <i class="icon minus square large blue pull-right min-div" style="cursor:pointer"></i>
                <i class="icon plus square large blue pull-right max-div" style="display:none;cursor:pointer"></i>
            </div>
            <div class="body">
                <div class="field">
                    <textarea name="comments" id="comments" cols="30" rows="10">{{$reviews->comments}}</textarea>
                </div>
            </div>
        </div>


        <div class="field text-center" style="margin:20px 0 40px 0">
            <button class="ui button primary" type="submit">Submit</button>
            <button id="resetEdit" class="ui button " type="reset">Reset</button>
        </div>
        


        </form>

    @endif

</div>

@push('script')
<script>
$(document).ready(function(){

    $('.ui.rating.expertise').rating({
        initialRating: 0,
        maxRating: 3
    });

    $('.ui.rating')
    .rating({
        initialRating: 0,
        maxRating: 7,
        className : {
            active     : 'active',
            hover      : 'hover',
            loading    : 'loading'
            },
    });

    var rating = $('.ui.rating').rating("get rating");

    $('.ui.rating.criteria').rating('setting', 'onRate', function(value) {
       var rating = $('.ui.rating').rating("get rating");
        //$('.ui.rating .icon').attr('id',"1");
        $('#criteria'+this.id).val(value);

    });

    @for($i=1;$i<=7;$i++)

    $('.message .ui.rating .icon:nth-child('+{{$i}}+')').mouseover(function(){
        //console.log(this);
        $(this).parent('div').parent('.message').find('.rate'+{{$i}}).animate({
            opacity: 1,
        },10);
    });

    $('.message .ui.rating .icon:nth-child('+{{$i}}+')').mouseout(function(){
        //console.log(this);
        $(this).parent('div').parent('.message').find('.rate'+{{$i}}).animate({
            opacity: 0,
        },10);
    });

    @endfor

    $('.ui.rating.expertise').rating('setting', 'onRate', function(value) {
       var rating = $('.ui.rating').rating("get rating");
        $('#expertise').val(value);

    });

    $('#reset').click(function(){
       $('.ui.rating').rating("clear rating");

    });

    @if($is_reviewed != 0)

    $('#resetEdit').click(function(){

       $('.ui.rating.expertise').remove();
       $('.r_expertise').append('<div data-rating="{{$reviews->reviewExpertise}}" class="ui star huge rating expertise " ></div>');
       
       $('.ui.rating.expertise').rating({
            initialRating: 0,
            maxRating: 3
        });

        @foreach($rev_criterias as $criteria)
            $('.cr{{$criteria->id}}').remove();
            $('.crt{{$criteria->id}}').append('<div data-rating="{{$criteria->mark}}" class="ui star huge rating criteria cr{{$criteria->id}}" id="{{$criteria->id}}"></div>');
        @endforeach

        $('.ui.rating')
        .rating({
            initialRating: 0,
            maxRating: 7
        });

        

        $('.ui.rating.criteria').rating('setting', 'onRate', function(value) {
            var rating = $('.ui.rating').rating("get rating");
            $('#criteria'+this.id).val(value);

        });

         $('.ui.rating.expertise').rating('setting', 'onRate', function(value) {
            var rating = $('.ui.rating').rating("get rating");
                $('#expertise').val(value);

        });


        
    });
        @endif



     $('.min-div').on('click',function () {
        $(this).hide();
        $(this).parent().siblings('.body').slideToggle();
        $(this).siblings('.max-div').show();
    });

    $('.max-div').on('click',function () {
        $(this).hide();
        $(this).parent().siblings('.body').slideToggle();
        $(this).siblings('.min-div').show();
    });


 

 


    

});
</script>
@endpush

@endsection