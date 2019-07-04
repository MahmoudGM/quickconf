@extends('layouts.app')
<?php
    $lang = Session::get('lang');
    $RL = parse_ini_file(base_path('language/'.$lang.'/RATELABELS.ini'));
?>

<title>{{ $RL['BTN_ADD']}}</title>
@section('content')

@push('style')
<style>

    span.button.red {line-height: .8;}
    span i{
        margin-left: 4px !important
    }
    .choices input,.added input{
        margin:0 0 10px 0 !important;
    }

</style>
@endpush

<div class="container ui">


<div class="header ui dividing">{{ $RL['BTN_ADD']}}</div>
@include('layouts.errors')

<div class="content">

<form class="ui form" method="POST" action="{{ route('conferences.ratelabels.store', [$conference->confAcronym, $conference->confEdition] ) }}">
  {{ csrf_field() }}
  <input type="hidden" name="conference_id" value="{{$conference->id}}">

<div id="topics">

    <div class="two fields">
        <div class="field required fifteen wide">
            <label for="label">{{ $RL['LABEL']}}</label>
            <input type="text" name="label[]" id="label" required>
        </div>

    </div>

</div>
    


    <span class="button ui green" id="append"><i class="icon square add"></i>{{ $RL['BTN_MORE']}}</span>
    </div>
    <div class="field text-center" style="margin-bottom:20px">
        <button class="ui button primary" type="submit">{{ $RL['BTN_SAVE']}}</button>
        <button class="ui button " type="reset">{{ $RL['BTN_RESET']}}</button>
    </div>
    </form>

</div>


</div>

@push('script')
<script>
$(document).ready(function(){
 $('#append').click(function(){

            $('#topics').append( '<div class="two fields">'
                                +'<div class="field required sixteen wide">'
                                +'<input type="text" name="label[]" id="label" required>'
                                +'</div>'
                                +'<div class=" one wide field required">'
                                +'<span id="remove" class="button ui red"> <i class="icon delete"></i></span>'
                                +'</div>'
                                +'</div>'


                );
     });

     $('#topics').on("click", "#remove" , function(){
              //console.log(this);
                  $(this).parent().parent().remove();
          });

});

</script>
@endpush
@endsection