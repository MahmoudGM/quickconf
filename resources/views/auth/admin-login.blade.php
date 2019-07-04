@extends('layouts.app')

@section('content')

<?php
    $lang = Session::get('lang') ;
    $LOGIN_ADMIN = parse_ini_file(base_path('language/'.$lang.'/LOGIN_ADMIN.ini'));
?>

<div class="container">



<div class="ui two column centered grid">
  <div class="column">
        <div class="ui segment">
            <h3 class="ui dividing header">
                {{ $LOGIN_ADMIN['TITLE'] }}
            </h3>
            <form class="ui form" role="form" method="POST" action="{{ route('admin.login.submit') }}" >
                {{ csrf_field() }}

                <div class="required field{{ $errors->has('email') ? ' has-error' : '' }}">
                    <label for="email">{{ $LOGIN_ADMIN['EMAIL'] }}</label>
                     <input id="email" type="email" name="email" value="{{ old('email') }}" required>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif

                </div>

                <div class="required field{{ $errors->has('password') ? ' has-error' : '' }}">
                    <label for="password">{{ $LOGIN_ADMIN['PASS'] }}</label>
                     <input id="email" type="password" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif

                </div>

                 <div class="field">
                                <div class="checkbox">
                                    <input type="checkbox" name="remember">
                                    <label>
                                       {{ $LOGIN_ADMIN['REMEMBER'] }}
                                    </label>
                                </div>
                </div>

                <div class="field text-center">
                    <button class="ui button primary" type="submit">Login</button>
                                    
                </div>

                </a>
            </form>
        </div>
  </div>

</div>

</div>




@endsection
