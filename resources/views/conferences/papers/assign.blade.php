@extends('layouts.app')
<title>Assign reviewers to paper {{$paper->id}}</title>
@section('content')




  <style>
@if( (count($reviewers1) != 0 ) or (count($reviewers2) != 0) or (count($conflictUsers) != 0) )
  @foreach($conflictUsers as $rev1)
    .item[data-value="{{$rev1->id}}"]{
      background-color: rgba(231, 76, 60, 0.34) !important ;
    }
  @endforeach
    @foreach($reviewers1 as $rev1)
    .item[data-value="{{$rev1->id}}"]{
      background-color: rgba(46, 204, 113, 0.34) !important
    }
  @endforeach

  @foreach($reviewers2 as $rev2)
    .item[data-value="{{$rev2->id}}"]{
      background-color: #f1c40f!important ;
    }
  @endforeach
@endif


  .revs .item{
    transition: background-color .5s ease-in;
  }
  .revs .item:hover{
    background-color:#eee !important;
  }

  .graph{
    margin:15px 0 20px 0
  }
  .graph div{
    margin:5px
  }

  .color{
    height: 20px;
    width: 20px;
    display: inline-block;
    margin-top:6px
    
  }
  .conflict{
    background-color: rgba(231, 76, 60, 0.34) ;
  }
  .match{
    background-color: rgba(46, 204, 113, 0.34) 
  }
  .not{
    background-color: #f1c40f
  }


  </style>


  <div class="ui container">

  {!! Breadcrumbs::render('paperAssign',$conference,$paper) !!}

  @include('layouts.errors')

  @if( (count($reviewers1) == 0 ) and (count($reviewers2) == 0) and (count($conflictUsers) == 0) )
    <div class="ui message negative">there is no reviewers</div>
  @else

  @if($assigned == 0)
      <h2 class="ui header dividing">
        Assign reviewers to paper {{$paper->id}}
      </h2>
    @else
      <h2 class="ui header dividing">
        Edit assigned reviewers to paper {{$paper->id}}
      </h2>
    @endif

     <div class="panel">
        <div class="header">
                Paper informations
                <i class="icon minus square large blue pull-right min-div" style="cursor:pointer"></i>
                <i class="icon plus square large blue pull-right max-div" style=" display:none; cursor:pointer"></i>
        </div>
        <div class="body">
            <strong>Title: </strong> {{$paper->title}} <br> <br>
            <strong>Abstract: </strong>
            <div style="border:1px solid #ddd;margin:10px 0 10px 0;border-radius:5px;padding:8px;background-color:#fff"> 
                {!!$paper->abstract!!}  
            </div>
            <strong>Topic: </strong>{{$paper->label}} <br> <br>
            <strong>Keywords: </strong>{{$paper->keywords}} <br> <br>
        </div>
    </div>

     <div class="graph">
      <div><span class="color conflict"></span> <span> Reviewers in conflict </span></div>
      <div><span class="color match"></span> <span> Reviewers has much topics with this paper </span> </div>
      <div><span class="color not"></span> <span> Reviewers has not much topics with this paper </span> </div>
    </div>

    <form class="ui form " action="{{route('conferences.papers.storeAssign',[$conference->confAcronym,$conference->confEdition,$paper->id])}}" method="post">
      {{ csrf_field() }}
      <input type="hidden" value="{{$assigned}}" name="assigned">
      <div class="field required">
        <label for="choose">Select reviewers</label>
        <select id="choose" class="ui fluid dropdown revs search" multiple="" name="reviewers[]" required="">
           @foreach($conflictUsers as $rev1)
            <?php
            $is_rev = \DB::table('users')
                            ->join('paper_user' , 'users.id' ,'=', 'paper_user.user_id' )
                            ->join('papers' , 'papers.id' , '=' , 'paper_user.paper_id')
                            ->where('paper_user.user_id' , '=' , $rev1->id)
                            ->where('paper_user.paper_id' , '=' , $paper->id)
                            ->count();

              $count_papers = \DB::table('users')
                            ->join('paper_user' , 'users.id' ,'=', 'paper_user.user_id' )
                            ->join('papers' , 'papers.id' , '=' , 'paper_user.paper_id')
                            ->where('users.id' , '=' , $rev1->id)
                            ->groupBy('paper_user.user_id','paper_user.user_id')
                            ->count();
            ?>
            @if($is_rev==0)
              <option value="{{$rev1->id}}">{{$rev1->email}} | {{$count_papers}}</option>
            @else
              <option selected value="{{$rev1->id}}">{{$rev1->email}} | {{$count_papers}}</option>              
            @endif
           @endforeach
          
          @foreach($reviewers1 as $rev1)
            <?php
            $is_rev = \DB::table('users')
                            ->join('paper_user' , 'users.id' ,'=', 'paper_user.user_id' )
                            ->join('papers' , 'papers.id' , '=' , 'paper_user.paper_id')
                            ->where('paper_user.user_id' , '=' , $rev1->id)
                            ->where('paper_user.paper_id' , '=' , $paper->id)
                            ->count();

              $count_papers = \DB::table('users')
                            ->join('paper_user' , 'users.id' ,'=', 'paper_user.user_id' )
                            ->join('papers' , 'papers.id' , '=' , 'paper_user.paper_id')
                            ->where('users.id' , '=' , $rev1->id)
                            ->groupBy('paper_user.user_id','paper_user.user_id')
                            ->count();
            ?>
            @if($is_rev==0)
              <option value="{{$rev1->id}}">{{$rev1->email}} | {{$count_papers}}</option>
            @else
              <option selected value="{{$rev1->id}}">{{$rev1->email}} | {{$count_papers}}</option>              
            @endif
           @endforeach

          @foreach($reviewers2 as $rev2)
            <?php
            $is_rev = \DB::table('users')
                            ->join('paper_user' , 'users.id' ,'=', 'paper_user.user_id' )
                            ->join('papers' , 'papers.id' , '=' , 'paper_user.paper_id')
                            ->where('paper_user.user_id' , '=' , $rev2->id)
                            ->where('paper_user.paper_id' , '=' , $paper->id)                            
                            ->count();

              $count_papers = \DB::table('users')
                            ->join('paper_user' , 'users.id' ,'=', 'paper_user.user_id' )
                            ->join('papers' , 'papers.id' , '=' , 'paper_user.paper_id')
                            ->where('users.id' , '=' , $rev2->id)
                            ->groupBy('paper_user.user_id','paper_user.user_id')
                            ->count();
            ?>

            @if($is_rev==0)
              <option value="{{$rev2->id}}">{{$rev2->email}} | {{$count_papers}}</option>
            @else
              <option selected value="{{$rev2->id}}">{{$rev2->email}} | {{$count_papers}}</option>
            @endif

          @endforeach
        </select>

      </div>

      <div class="field text-center" style="margin:20px 0 40px 0">
          <button class="ui button primary" type="submit">assign</button>
          <button id="reset" class="ui button " type="reset">reset</button>
      </div>

    </form>

    @endif


  </div>

<script>
var nbrev = {{$conference->nb_reviewer_per_item}};

</script>

@push('script')
<script>
$(document).ready(function() {

    $("#reset").click(function(){
        $('.revs').dropdown('restore defaults');
    });

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
