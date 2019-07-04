@extends('layouts.app')
<?php
$lang = Session::get('lang') ;
$RESET = parse_ini_file(base_path('language/'.$lang.'/RESET_PASS.ini'));
?>
@section('content')


<div class="container">
    


<div class="ui two column centered grid">
  <div class="column">
        <div class="ui segment">
            <h3 class="ui dividing header">
                {{ $RESET['TITLE']}}
            </h3>
            @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
            @endif
            <form class="ui form" role="form" method="POST" action="{{ route('password.email') }}" >
                {{ csrf_field() }}
               
                <div class="field{{ $errors->has('email') ? ' has-error' : '' }}">
                    <label for="email">Email</label>
                     <input id="email" type="email" name="email" value="{{ old('email') }}" required>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif

                </div>

                <div class="field text-center">
                    <button class="ui button primary" type="submit"> {{ $RESET['BTN_RESET']}}</button>
                </div>
                

            </form>
        </div>
  </div>
  
</div>

</div>

@endsection
