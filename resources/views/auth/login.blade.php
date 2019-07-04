@extends('layouts.app')

@section('content')

<?php
    if (Session::get('lang')  == NULL){
            Session::put('lang','en');
        }

    $lang = Session::get('lang') ;
    $LOGIN_USER = parse_ini_file(base_path('language/'.$lang.'/LOGIN_USER.ini'));

    

?>
<div class="container">



<div class="ui two column centered grid">

  <div class="column">   
  <div class="message    ui"><strong>QuickConf </strong> is a conference management system, 
which facilitate the mnanipulation of your conferences with their multiple editions.</div>
        <div class="ui segment">
            <h3 class="ui dividing header">
                {{ $LOGIN_USER['TITLE'] }}
            </h3>
            <form class="ui form" role="form" method="POST" action="{{ route('login') }}" >
                {{ csrf_field() }}
                
                @if($message = Session::get('success'))
                    <div class="ui green message" >
                        <p>
                            {{ $message }}
                        </p>
                    </div>
                @endif

                @if($message = Session::get('warning'))
                    <div class="ui red message" >
                        <p>
                            {{ $message }}
                        </p>
                    </div>
                @endif



               
                <div class="required field{{ $errors->has('email') ? ' has-error' : '' }}">
                    <label for="email">{{ $LOGIN_USER['EMAIL'] }}</label>
                     <input id="email" type="email" name="email" value="{{ old('email') }}" required>

                                 @foreach($errors->all() as $error)
                                    <span class="help-block">
                                        <strong>{{ $error }}</strong>
                                    </span>
                                @endforeach

                </div>

                <div class="required field{{ $errors->has('password') ? ' has-error' : '' }}">
                    <label for="password">{{ $LOGIN_USER['PASS'] }}</label>
                     <input id="email" type="password" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif

                </div>

                 <div class="field">
                                <div class="ui checkbox">
                                    <input type="checkbox" name="remember"> 
                                    <label>
                                        {{ $LOGIN_USER['REMEMBER'] }}
                                    </label>
                                </div>
                        </div>

                <div class="field text-center">
                    <button class="ui button primary" type="submit">{{ $LOGIN_USER['BTN'] }}</button>
                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                    {{ $LOGIN_USER['FORGOT'] }}
                </div>
                
                </a>
            </form>
        </div>
  </div>
  
</div>

</div>




@endsection
