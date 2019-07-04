@extends('layouts.app')

<?php
$lang = Session::get('lang');
$MSG = parse_ini_file(base_path('language/'.$lang.'/MESSAGE_TEMP.ini'));
?>

<title>{{$MSG['EDIT_MESSAGE']}}</title>
<script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
<script src="{{ asset('js/ckeditor/config.js') }}"></script>
@section('content')

<div class="container ui">
{!! Breadcrumbs::render('editMsgTemp',$conference) !!}
<form class="ui form" method="POST" action="{{ route('conferences.messages.edit', [$conference->confAcronym, $conference->confEdition,$messagetemp->id] ) }}">
  {{ csrf_field() }}

  <h2 class="ui dividing header">{{$MSG['EDIT_MESSAGE']}} {{  $messagetemp->id }} </h2>
  <div class="field">
    <div class="two fields">
        <div class="field required">
          <label for="name">{{$MSG['NAME']}}</label>
          <input name="name" id="name" required type="text"  value="{{$messagetemp->name}}" >
        </div>
        <div class="field required">
          <label for="title">{{$MSG['TITLE']}}</label>
          <input name="title" id="title" required  type="text" autofocus value="{{$messagetemp->title}}">
        </div>
    </div>
  </div>

    <div class="field required">
        <label for="body">{{$MSG['BODY']}}</label>
        <textarea name="body" id="bodyField"  cols="30" rows="10">{{$messagetemp->body}}</textarea>
        <script>
            CKEDITOR.replace('bodyField');
        </script>
    </div>

    <div class="field text-center">
        <button class="ui button primary" type="submit">{{$MSG['BTN_EDIT']}}</button>
        <button class="ui button " type="reset">{{$MSG['BTN_RESET']}}</button>
    </div>

    </form>
</div>
@endsection
