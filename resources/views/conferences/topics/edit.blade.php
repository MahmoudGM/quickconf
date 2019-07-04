@extends('layouts.app')
<?php
    $lang = Session::get('lang');
    $TOPIC = parse_ini_file(base_path('language/'.$lang.'/TOPICS.ini'));
?>

<title>{{ $TOPIC['EDIT_TOPIC']}}   {{  $topic->id }}</title>

@section('content')


<div class="container ui">
{!! Breadcrumbs::render('editTopic',$conference) !!}
  <h2 class="ui dividing header">{{ $TOPIC['EDIT_TOPIC']}}   {{  $topic->id }} </h2>
  @include('layouts.errors')
<form class="ui form" method="POST" action="{{ route('conferences.topics.update', [$conference->confAcronym, $conference->confEdition,$topic->id] ) }}">
  {{ csrf_field() }}



  
     

        <div class="two fields">
        <div class="field required twelve wide">
            <label for="label">{{ $TOPIC['LABEL']}}</label>
            <input type="text" name="label" id="label" required autofocus value="{{$topic->label}}">
        </div>
        <div class="field required three wide">
            <label for="acronym">{{ $TOPIC['ACRONYM']}}</label>
            <input type="text" name="acronym" id="acronym" required value="{{$topic->acronym}}">
        </div>

    </div>


    <div class="field text-center">
        <button class="ui button primary" type="submit">{{ $TOPIC['BTN_EDIT']}}</button>
        <button class="ui button " type="reset">{{ $TOPIC['BTN_RESET']}}</button>
    </div>

    </form>
</div>
@endsection