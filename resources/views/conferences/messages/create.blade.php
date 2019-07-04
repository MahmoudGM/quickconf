@extends('layouts.app')
<?php
    $lang = Session::get('lang');
    $MSG = parse_ini_file(base_path('language/'.$lang.'/MESSAGE_TEMP.ini'));
?>
<title>{{$MSG['BTN_ADD']}}</title>
@section('content')
<script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
<script src="{{ asset('js/ckeditor/config.js') }}"></script>
<div class="ui container">
{!! Breadcrumbs::render('crMsgTemp',$conference) !!}
<h3 class="header dividing ui">{{$MSG['BTN_ADD']}}</h3>
    @include('layouts.errors')
    <div class="message ui">
    To be able to integrate the data in the messages sent by email please use the names of the field of the database as follows: <br>
    {$first_name}: First name <br>
    </div>
    <form class="ui form" method="POST" action="{{ route('conferences.messages.store', [$conference->confAcronym, $conference->confEdition] ) }}">
  {{ csrf_field() }}
  <input type="hidden" name="conference_id" value="{{$conference->id}}">
  <div class="field">
    <div class="two fields">
        <div class="field required">
          <label for="name">{{$MSG['NAME']}}</label>
          <input name="name" id="name" required  type="text" autofocus>
        </div>
        <div class="field required">
          <label for="title">{{$MSG['TITLE']}}</label>
          <input name="title" id="title" required type="text">
        </div>
    </div>
  </div>

    <div class="field required">
        <label for="bodyField">{{$MSG['BODY']}}</label>
        <textarea name="body" id="bodyField"  cols="30" rows="10"></textarea>
        <script>
            CKEDITOR.replace('bodyField');
        </script>
    </div>

    <div class="field text-center" style="margin-bottom:30px">
        <button class="ui button primary" type="submit">{{$MSG['BTN_ADD']}}</button>
        <button class="ui button " type="reset">{{$MSG['BTN_RESET']}}</button>
    </div>

    </form>

  </div>


  @endsection