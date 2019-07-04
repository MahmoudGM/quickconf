@extends('layouts.app')
<?php
    $lang = Session::get('lang');
    $CR = parse_ini_file(base_path('language/'.$lang.'/CRITERIAS.ini'));
?>

<title>{{ $CR['BTN_ADD']}}</title>
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

{!! Breadcrumbs::render('crCr',$conference) !!}
<div class="header ui dividing">{{ $CR['BTN_ADD']}}</div>
@include('layouts.errors')

<div class="content">

<form class="ui form" method="POST" action="{{ route('conferences.criterias.store', [$conference->confAcronym, $conference->confEdition] ) }}">
  {{ csrf_field() }}
  <input type="hidden" name="conference_id" value="{{$conference->id}}">


    <div class="two fields">

        <div class="field required fifteen wide">
            <label for="label">{{ $CR['LABEL']}}</label>
            <input type="text" name="label" id="label" required>
        </div>

        <div class="field required two wide">
            <label for="weight">{{ $CR['WEIGHT']}}</label>
            <input type="number" name="weight" id="weight" required>
        </div>
        
    </div>



    <div class="field required ">
        <label for="explanation">{{ $CR['EXP']}}</label>
        <textarea required name="explanation" id="" cols="30" rows="5"></textarea>
    </div>


    
    </div>
    <div class="field text-center" style="margin-bottom:20px">
        <button class="ui button primary" type="submit">{{ $CR['BTN_SAVE']}}</button>
        <button class="ui button " type="reset">{{ $CR['BTN_RESET']}}</button>
    </div>
    </form>

</div>


</div>


@endsection