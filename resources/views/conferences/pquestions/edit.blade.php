@extends('layouts.app')
<?php
    $lang = Session::get('lang');
    $Q = parse_ini_file(base_path('language/'.$lang.'/PQUESTIONS.ini'));
?>

<title>{{$Q['BTN_EDIT']}} {{$Q['QUESTION']}} {{$pquestion->id}}</title>
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
{!! Breadcrumbs::render('editPq',$conference) !!}
<h3 class="dividing ui header">
    {{$Q['BTN_EDIT']}} {{$Q['QUESTION']}} {{$pquestion->id}}
</h3>

@include('layouts.errors')


<form id="form" class="ui form" method="POST">
  {{ csrf_field() }}
  <div class="field">
    <div class="two fields">
        <div class="fourteen wide field required">
          <label for="question">{{$Q['QUESTION']}}</label>
          <input name="question" value="{{$pquestion->question}}" id="question" required  type="text" autofocus>
        </div>
        <div class="two wide field required">
          <label for="required">{{$Q['REQUIRED']}}</label>
          <input name="required" value="{{$pquestion->required}}" id="required" required type="number">
        </div>
    </div>
  </div>
  <div style="margin-top:20px;" class="two fields">
    <div class=" thirteen wide field required"> <label>{{$Q['LIST_CH']}}</label> </div>
    <div class=" three wide field required"> <label>{{$Q['POS']}}</label> </div>
  </div>
  <hr style="margin-bottom:20px;">
  <div id="choices">

      @for ($i=0; $i < count($pqchoice) ; $i++)
     <div class="two fields choices">
        <div class=" thirteen wide field required">
              <input type="hidden" name="id_pc[]" value="{{$pqchoice[$i]->id}}">
              <input name="choice[]" value="{{$pqchoice[$i]->choice}}" id="choice" required type="text">

        </div>
        <div class=" three wide field required">

              <input name="position[]" value="{{$pqchoice[$i]->position}}" id="position" required type="number">

        </div>
        @if (count($pqchoice)>=3)
          <div class=" one wide field required">


                {{ csrf_field() }}
                <button onclick="submitForm('edit/{{$pqchoice[$i]->id}}/delete')" type="submit" name="button" class="button ui red"><i class="icon delete"></i></button>


          </div>
        @endif
     </div>
       @endfor
       </div>
       <span class="button ui green pull-right" id="append"><i class="icon square add"></i>{{$Q['BTN_MORE']}}</span>

     <div class="field text-center">
         <button onclick="submitForm('{{ route('conferences.pquestions.update', [$conference->confAcronym, $conference->confEdition,$pquestion->id] ) }}')" class="ui button primary" type="submit">{{$Q['BTN_EDIT']}}</button>
         <button class="ui button " type="reset">{{$Q['BTN_RESET']}}</button>
     </div>

    </form>

</div>

@push('script')
<script>

          function submitForm(action)
          {
              document.getElementById('form').action = action;
              document.getElementById('form').submit();
          }
  $(document).ready(function(){
    $('#append').click(function(){

        $('#choices').append(' <div class="two fields added">'
                            +'<div class=" thirteen wide field required">'
                            +'<input name="added_choice[]" id="choice" required type="text">'
                            +'</div>'
                            +'<div class=" three wide field required">'
                            +'<input name="added_position[]" id="position" required type="number">'
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
