@extends('layouts.app')
<?php
    $lang = Session::get('lang');
    $CR = parse_ini_file(base_path('language/'.$lang.'/CRITERIAS.ini'));
?>

<title>{{ $CR['EDIT_CR']}}   {{  $criteria->id }}</title>

@section('content')


<div class="container ui">
{!! Breadcrumbs::render('editCr',$conference) !!}
  <h2 class="ui dividing header">{{ $CR['EDIT_CR']}}   {{  $criteria->id }} </h2>
  @include('layouts.errors')
<form class="ui form" method="POST" action="{{ route('conferences.criterias.update', [$conference->confAcronym, $conference->confEdition,$criteria->id] ) }}">
  {{ csrf_field() }}


        <div class="two fields">

        <div class="field required fifteen wide">
            <label for="label">{{ $CR['LABEL']}}</label>
            <input type="text" name="label" id="label" required autofocus value="{{$criteria->label}}">
        </div>

        <div class="field required two wide">
            <label for="weight">{{ $CR['WEIGHT']}}</label>
            <input type="number" name="weight" id="weight" required value="{{$criteria->weight}}">
        </div>
        
    </div>



    <div class="field required ">
        <label for="explanation">{{ $CR['EXP']}}</label>
        <textarea required name="explanation" id="" cols="30" rows="5">{{$criteria->explanation}}</textarea>
    </div>
  



    <div class="field text-center">
        <button class="ui button primary" type="submit">{{ $CR['BTN_EDIT']}}</button>
        <button class="ui button " type="reset">{{ $CR['BTN_RESET']}}</button>
    </div>

    </form>
</div>
@endsection