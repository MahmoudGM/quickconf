@extends('layouts.app')
<?php
    $lang = Session::get('lang');
    $RL = parse_ini_file(base_path('language/'.$lang.'/RATELABELS.ini'));
?>

<title>{{ $RL['EDIT_TOPIC']}}   {{  $ratelabel->id }}</title>

@section('content')


<div class="container ui">
  <h2 class="ui dividing header">{{ $RL['EDIT_TOPIC']}}   {{  $ratelabel->id }} </h2>
  @include('layouts.errors')
<form class="ui form" method="POST" action="{{ route('conferences.ratelabels.update', [$conference->confAcronym, $conference->confEdition,$ratelabel->id] ) }}">
  {{ csrf_field() }}



  
        <div class="field required">
          <label for="label">{{ $RL['LABEL']}}</label>
          <input name="label" id="label"   type="text" autofocus value="{{$ratelabel->label}}">
          
        </div>


    <div class="field text-center">
        <button class="ui button primary" type="submit">{{ $RL['BTN_EDIT']}}</button>
        <button class="ui button " type="reset">{{ $RL['BTN_RESET']}}</button>
    </div>

    </form>
</div>
@endsection