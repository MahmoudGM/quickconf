@extends('layouts.app')

<?php
    $lang = Session::get('lang');
    $Q = parse_ini_file(base_path('language/'.$lang.'/PQUESTIONS.ini'));
?>

<title>{{$Q['BTN_ADD']}} </title>

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
{!! Breadcrumbs::render('crPq',$conference) !!}
<h3 class="dividing ui header">
    {{$Q['BTN_ADD']}} 
</h3>

@include('layouts.errors')

<form class="ui form" method="POST" action="{{ route('conferences.pquestions.store', [$conference->confAcronym, $conference->confEdition] ) }}">
  {{ csrf_field() }}
  <input type="hidden" name="conference_id" value="{{$conference->id}}">
  <div class="field">
    <div class="two fields">
        <div class="fourteen wide field required">
          <label for="question">{{$Q['QUESTION']}} </label>
          <input name="question" id="question" required  type="text" autofocus>
        </div>
        <div class="two wide field required">
          <label for="required">{{$Q['REQUIRED']}} </label>
          <input name="required" id="required" required type="number">
        </div>
    </div>
  </div>
  <div style="margin-top:20px;" class="two fields">
    <div class=" thirteen wide field required"> <label>{{$Q['LIST_CH']}}</label> </div>
    <div class=" three wide field required"> <label>{{$Q['POS']}}</label> </div>
  </div>
  <hr style="margin-bottom:20px;">
  
   <div id="choices">
        <div class="two fields choices">
        <div class=" thirteen wide field required">
              <input name="choice[]"  id="choice" required type="text">
              <input name="choice[]"  id="choice" required type="text">
        </div>
        <div class=" three wide field required">

              <input name="position[]"  id="position" required type="number">
              <input name="position[]"  id="position" required type="number">

        </div>
     </div>
     </div>



    <span class="button ui green pull-right" id="append"><i class="icon square add"></i>{{ $Q['BTN_MORE'] }}</span>
    <div class="field text-center">
        <button class="ui button primary" type="submit">{{$Q['BTN_SAVE']}} </button>
        <button class="ui button " type="reset">{{$Q['BTN_RESET']}} </button>
    </div>

    </form>
  </div>
@push('script')
  <script>
  $(document).ready(function(){
       $('#append').click(function(){

        $('#choices').append(' <div class="two fields added">'
                            +'<div class=" thirteen wide field required">'
                            +'<input name="choice[]" id="choice" required type="text">'
                            +'</div>'
                            +'<div class=" three wide field required">'
                            +'<input name="position[]" id="position" required type="number">'
                            +'</div>'
                            +'<div class=" one wide field required">'
                            +'<span id="remove" class="button ui red"> <i class="icon delete"></i></span>'
                            +'</div>');
          });


          $('#choices').on("click", "#remove" , function(){
              //console.log(this);
                  $(this).parent().parent().remove();
          });
  });
  </script>
  @endpush
  @endsection