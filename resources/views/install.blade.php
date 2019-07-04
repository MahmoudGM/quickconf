@extends('layouts.app')
<?php $lang = Session::get('lang') ; ?>
@section('content')
<div class="ui menu top fixed inverted">
{{--
<a href="" class="item">
    Installation
</a>

--}}
<div class="right menu">
<form action="/changeLanguage/fr" method="POST">
        {{csrf_field()}}
        @if($lang == 'fr')
          <button style="background-color:#21ba45;color:#000;padding: 8px;margin-top: 5px;" type="submit" class="inverted green lang button  ui">Fr</button>
        @else
          <button style="padding: 8px;margin-top: 5px;" type="submit" class="inverted green lang button  ui">Fr</button>
        @endif
      </form>
      <form action="/changeLanguage/en" method="POST">
        {{csrf_field()}}
        @if($lang == 'en')
          <button style="background-color:#21ba45;color:#000;padding: 8px;margin-top: 5px;" type="submit" class=" inverted green lang button  ui">En</button>
        @else
          <button style=" padding: 8px;margin-top: 5px;" type="submit" class=" inverted green lang button  ui">En</button>
        @endif
      </form>
      </div>
</div>
<div class="ui container">
<center><h1>Installation de l'application</h1></center>
<form class="ui form" action="{{route('install.store')}}" method="post" >
{{csrf_field()}}
<h3 class="dividing header ui">Admin informations</h3>
    <div class="field  required">
        <label for="first_name">First Name</label>
        <input name="first_name" id="first_name" required type="text">
      </div>
      <div class="field  required">
        <label for="last_name">Last Name</label>
        <input name="last_name" id="last_name" required type="text">
      </div>
      <div class="field  required">
        <label for="email">Email</label>
        <input name="email" id="email" required type="email">
      </div>
      <div class="field  required">
        <label for="password">Mot de passe</label>
        <input name="password" id="password" required type="password">
      </div>
<h3 class="dividing header ui">Database informations</h3>
    <div class="field  required">
        <label for="db_name">Database Name</label>
        <input name="db_name" id="db_name" required type="text">
      </div>
      <div class="field  required">
        <label for="db_user">Database Username</label>
        <input name="db_user" id="db_user" required type="text">
      </div>
      <div class="field  required">
        <label for="user_pass">Username password</label>
        <input name="user_pass" id="user_pass" required type="password">
      </div>
      <div class="field  required">
        <label for="db_type">Database Type</label>
        <input name="db_type" id="db_type" required type="text" placeholder="example: mysql">
      </div>
      <div class="field  required">
        <label for="db_host">Database Host</label>
        <input name="db_host" id="db_host" required type="text" placeholder="example: 127.0.0.1">
      </div>
        <div class="field text-center" style="margin:20px 0 40px 0">
            <button class="ui button primary" type="submit">Submit</button>
            <button id="reset" class="ui button " type="reset">Reset</button>
        </div>
</form>
</div>

@endsection