@extends('layouts.app')

@section('content')




  <style>
  
  @if( (count($papers1) != 0 ) or (count($papers2) != 0) or (count($conflictPapers) != 0) )

    @foreach($conflictPapers as $pC)
      .item[data-value="{{$pC->id}}"]{
        background-color: rgba(231, 76, 60, 0.34) !important ;
      }
    @endforeach

    @foreach($papers1 as $p1)
      .item[data-value="{{$p1->id}}"]{
        background-color: rgba(46, 204, 113, 0.34) !important
      }
    @endforeach

    @foreach($papers2 as $p2)
      .item[data-value="{{$p2->id}}"]{
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

  
  {!! Breadcrumbs::render('revAssign',$conference,$user) !!}

  @if( (count($conflictPapers) == 0 ) and (count($papers1) == 0 ) and (count($papers2) == 0) and (count($conflictPapers) == 0) )
    <div class="ui message negative">there is no papers</div>
  @else
  
    @if($assigned == 0)
      <h2 class="ui header dividing">
        Assign papers to reviewer {{$user->first_name}} {{$user->last_name}}
      </h2>
    @else
      <h2 class="ui header dividing">
        Edit assigned papers to reviewer {{$user->first_name}} {{$user->last_name}}
      </h2>
    @endif

    @include('layouts.errors')
    <div class="graph">
      <div><span class="color conflict"></span> <span> Papers in conflict </span></div>
      <div><span class="color match"></span> <span> Papers has much topics with this reviewer </span> </div>
      <div><span class="color not"></span> <span> Papers has not much topics with this reviewer </span> </div>
    </div>
    <form class="ui form " action="{{route('conferences.comite.storeAssign',[$conference->confAcronym,$conference->confEdition,$user->id])}}" method="post">
      {{ csrf_field() }}
      
      <input type="hidden" value="{{$assigned}}" name="assigned">
      <div class="field required">
        <label for="choose">Select papers</label>
        <select id="choose" class="ui fluid dropdown revs search" multiple="" name="papers[]" required="">
          @foreach($conflictPapers as $p1)
            <?php
             $is_rev = \DB::table('users')
                            ->join('paper_user' , 'users.id' ,'=', 'paper_user.user_id' )
                            ->join('papers' , 'papers.id' , '=' , 'paper_user.paper_id')
                            ->where('paper_user.user_id' , '=' , $user->id)
                            ->where('paper_user.paper_id' , '=' , $p1->id)                            
                            ->count();

            $count_revs = \DB::table('users')
                            ->join('paper_user' , 'users.id' ,'=', 'paper_user.user_id' )
                            ->join('papers' , 'papers.id' , '=' , 'paper_user.paper_id')
                            ->where('paper_user.paper_id' , '=' , $p1->id)
                            ->count();
            ?>
        
            @if(($is_rev==0)and($count_revs<$conference->nb_reviewer_per_item))
              <option value="{{$p1->id}}">{{$p1->id}} - {{$p1->title}} | {{$count_revs}} reviewers </option>
            @elseif($is_rev!=0) 
              <option selected value="{{$p1->id}}">{{$p1->id}} - {{$p1->title}} | {{$count_revs}} reviewers </option>                       
            @endif
          @endforeach
          
          @foreach($papers1 as $p1)
            <?php
             $is_rev = \DB::table('users')
                            ->join('paper_user' , 'users.id' ,'=', 'paper_user.user_id' )
                            ->join('papers' , 'papers.id' , '=' , 'paper_user.paper_id')
                            ->where('paper_user.user_id' , '=' , $user->id)
                            ->where('paper_user.paper_id' , '=' , $p1->id)                            
                            ->count();

            $count_revs = \DB::table('users')
                            ->join('paper_user' , 'users.id' ,'=', 'paper_user.user_id' )
                            ->join('papers' , 'papers.id' , '=' , 'paper_user.paper_id')
                            ->where('paper_user.paper_id' , '=' , $p1->id)
                            ->count();
            ?>
        
            @if(($is_rev==0)and($count_revs<$conference->nb_reviewer_per_item))
              <option value="{{$p1->id}}">{{$p1->id}} - {{$p1->title}} | {{$count_revs}} reviewers </option>
            @elseif($is_rev!=0) 
              <option selected value="{{$p1->id}}">{{$p1->id}} - {{$p1->title}} | {{$count_revs}} reviewers </option>                       
            @endif
          @endforeach

          @foreach($papers2 as $p2)
            <?php
             $is_rev = \DB::table('users')
                            ->join('paper_user' , 'users.id' ,'=', 'paper_user.user_id' )
                            ->join('papers' , 'papers.id' , '=' , 'paper_user.paper_id')
                            ->where('paper_user.user_id' , '=' , $user->id)
                            ->where('paper_user.paper_id' , '=' , $p2->id)                            
                            ->count();

            $count_revs = \DB::table('users')
                            ->join('paper_user' , 'users.id' ,'=', 'paper_user.user_id' )
                            ->join('papers' , 'papers.id' , '=' , 'paper_user.paper_id')
                            ->where('paper_user.paper_id' , '=' , $p2->id)
                            ->count();
            ?>
        
            @if(($is_rev==0)and($count_revs<$conference->nb_reviewer_per_item))
              <option value="{{$p2->id}}">{{$p2->id}} - {{$p2->title}} | {{$count_revs}} reviewers</option>
            @elseif($is_rev!=0)
              <option selected value="{{$p2->id}}">{{$p2->id}} - {{$p2->title}} | {{$count_revs}} reviewers</option>              
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



@push('script')
<script>
$(document).ready(function() {

    $("#reset").click(function(){
        $('.revs').dropdown('restore defaults');
    })
    


});
</script>
@endpush

@endsection
