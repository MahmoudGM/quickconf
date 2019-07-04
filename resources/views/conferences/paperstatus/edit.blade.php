@extends('layouts.app')
<?php
    $lang = Session::get('lang');
    $PS = parse_ini_file(base_path('language/'.$lang.'/P_STATUS.ini'));
?>

<title>{{ $PS['EDIT_PS']}}   {{  $pstatus->id }}</title>

@section('content')
<script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
<script src="{{ asset('js/ckeditor/config.js') }}"></script>
@push('style')
<style>

    select > .placeholder {
            display: none;
        }

</style>
@endpush

<div class="container ui">
{!! Breadcrumbs::render('editPs',$conference) !!}
  <h2 class="ui dividing header">{{ $PS['EDIT_PS']}}   {{  $pstatus->id }} </h2>
  @include('layouts.errors')
<form class="ui form" method="POST" action="{{ route('conferences.paperstatus.update', [$conference->confAcronym, $conference->confEdition,$pstatus->id] ) }}">
  {{ csrf_field() }}



  


        <div class="two fields">
        <div class="field required thirteen wide">
            <label for="label">{{ $PS['LABEL']}}</label>
            <input type="text" name="label" id="label" required value="{{$pstatus->label}}">
        </div>
        <div class="field required three wide">
            <label for="camReady">{{ $PS['CAMREADY']}}</label>
            <select name="camReady" id="camReady" required>
                <option class="placeholder" value="" disabled selected >{{$PS['SELECT']}}</option>
                <option @if($pstatus->camReadyRequired == 1) selected @endif value="1">{{ $PS['Y']}}</option>
                <option @if($pstatus->camReadyRequired == 0) selected @endif value="0">{{ $PS['N']}}</option>
            </select>
        </div>
        <div class="field required two wide">
            <label for="camReady">{{ $PS['ACC']}}</label>
            <select name="accepted" id="accepted" required>
                <option class="placeholder" value="" disabled selected >{{$PS['SELECT']}}</option>
                <option @if($pstatus->accepted == 1) selected @endif value="1">{{ $PS['Y']}}</option>
                <option @if($pstatus->accepted == 0) selected @endif value="0">{{ $PS['N']}}</option>
            </select>
        </div>

    </div>
    
    <div class="field required ">
        <label for="msgTemplate">{{$PS['MSG']}}</label>
        <div class="message ui">
        To be able to integrate the data in the messages sent by email please use the names of the field of the database as follows: <br> <br>
        <p style="line-height:25px">
        <strong>&#123;{$first_name}&#125; :</strong> First name. <br>
        <strong>&#123;{$last_name}&#125; :</strong> Last name. <br>
        <strong>&#123;{$confName}&#125; :</strong> Conference name.  <br>
        <strong>&#123;{$confEdition}&#125; :</strong> Conference edition. <br>
        <strong>&#123;{$paperId}&#125; :</strong> Paper id. <br>
        <strong>&#123;{$title}&#125; :</strong> Paper title. <br>
        <strong>&#123;{$abstract}&#125; :</strong> Paper abstract. <br>
        <strong>&#123;{$keywords}&#125; :</strong> Paper keywords. <br>
        <strong>&#123;{$cam_ready_deadline}&#125; :</strong> The deadline of submission of the camera-ready version. <br>
        
        
        
        </p>
        </div>
        <textarea required name="msgTemplate" id="msgTemplate" cols="30" rows="10">{{$pstatus->msgTemplate}}</textarea>
        <script>
            CKEDITOR.replace('msgTemplate');
        </script>
    </div>


    <div class="field text-center">
        <button class="ui button primary" type="submit">{{ $PS['BTN_EDIT']}}</button>
        <button class="ui button " type="reset">{{ $PS['BTN_RESET']}}</button>
    </div>

    </form>
</div>
@endsection