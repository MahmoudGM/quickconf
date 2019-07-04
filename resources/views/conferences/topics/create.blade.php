@extends('layouts.app')
<?php
    $lang = Session::get('lang');
    $TOPIC = parse_ini_file(base_path('language/'.$lang.'/TOPICS.ini'));
?>

<title>{{ $TOPIC['BTN_ADD']}}</title>
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

{!! Breadcrumbs::render('crTopic',$conference) !!}
<div class="header ui dividing">{{ $TOPIC['BTN_ADD']}}</div>
@include('layouts.errors')

<div class="content">

<form class="ui form" method="POST" action="{{ route('conferences.topics.store', [$conference->confAcronym, $conference->confEdition] ) }}">
  {{ csrf_field() }}
  <input type="hidden" name="conference_id" value="{{$conference->id}}">

<div id="topics">

    <div class="two fields">
        <div class="field required twelve wide">
            <label for="label">{{ $TOPIC['LABEL']}}</label>
            <input type="text" name="label[]" id="label" required>
        </div>
        <div class="field required three wide">
            <label for="acronym">{{ $TOPIC['ACRONYM']}}</label>
            <input type="text" name="acronym[]" id="acronym" required>
        </div>

    </div>

</div>
    


    <span class="button ui green" id="append"><i class="icon square add"></i>{{ $TOPIC['BTN_MORE']}}</span>
    </div>
    <div class="field text-center" style="margin-bottom:20px">
        <button class="ui button primary" type="submit">{{ $TOPIC['BTN_SAVE']}}</button>
        <button class="ui button " type="reset">{{ $TOPIC['BTN_RESET']}}</button>
    </div>
    </form>

</div>


</div>

@push('script')
<script>
$(document).ready(function(){
 $('#append').click(function(){

            $('#topics').append( '<div class="two fields">'
                                +'<div class="field required twelve wide">'
                                +'<input type="text" name="label[]" id="label" required>'
                                +'</div>'
                                +'<div class="field required three wide">'
                                +'    <input type="text" name="acronym[]" id="acronym" required>'
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