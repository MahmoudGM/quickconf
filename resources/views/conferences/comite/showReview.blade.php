@extends('layouts.app')

@section('content')

@push('style')


@endpush

<div class="ui container">
    
        <h2 class="ui header" >show review paper {{$paper->id}}  ({{$reviews->overall}}/7) </h2>
        <h3 class="ui header dividing" >Reviewed by {{$user->first_name}} {{$user->last_name}}</h3>

        <form action="{{route('conferences.comite.updateReview',[$conference->confAcronym,$conference->confEdition,$paper->id])}}" class="ui form" method="POST">
        <div class="panel">
            <div class="header">
                Criterias <label class="ui label teal small">( Strong Reject, Reject, Weak Reject, Neutral, Weak Accept, Accept, Strong Accept )</label>
            </div>
            <div class="body">

                @foreach($rev_criterias as $criteria)

                    <div class="message ui">
                        {{$criteria->label}}  <div data-rating="{{$criteria->mark}}" class="ui star huge rating criteria cr{{$criteria->id}}" id="{{$criteria->id}}"></div>
                    </div>
                   {{$criteria->explanation}}
  
                @endforeach
            </div>
        </div>
        
    

        <div class="panel">
            <div class="header">
                Review expertise <label class="ui label teal small">( Low,Meduim,High )</label>
            </div>
            <div class="body r_expertise">
                <div data-rating="{{$reviews->reviewExpertise}}" class="ui star huge rating expertise " ></div>
                <input style="display:none" value="{{$reviews->reviewExpertise}}" type="text" name="expertise" id="expertise" required tabindex="-1">
            </div>
        </div>

        <div class="panel">
            <div class="header">
            <label for="summary">Summary of contribution (shown to the authors)	</label>
            </div>
            <div class="body">
                {{$reviews->summary}}
            </div>
        </div>

        <div class="panel">
            <div class="header">
            <label for="comments">Detailed comments (shown to the authors)</label>
            </div>
            <div class="body">
                {{$reviews->comments}}
            </div>
        </div>

        <div class="panel">
            <div class="header">
            <label for="comments">Questions</label>
            </div>
            <div class="body">


            @foreach($paper_user_revq as $pur)
                <div class="field">
                    <label for="q{{$pur->id}}">{{$pur->question}}</label>
                        {{$pur->choice}}

                </div>
            @endforeach
        </div>
        </div>

        <div class="panel">
            <div class="header">
            <label for="details">Comments for Program Committee (not shown to the authors)	</label>
            </div>
            <div class="body">
                {{$reviews->details}}
            </div>
        </div>


 

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
        maxRating: 7
    });

    $('.ui.rating')
    .rating('disable');

    var rating = $('.ui.rating').rating("get rating");


        
    });
 


 

 


    


</script>
@endpush

@endsection