@extends('layouts.app')
<?php
    $lang = Session::get('lang') ;
    $REG = parse_ini_file(base_path('language/'.$lang.'/REGISTER.ini'));
?>

<title>{{$REG['SETTINGS']}}</title>
@section('content')
  <div class="ui container">
    <h3 class="ui header dividing">{{$REG['TITLE_SET']}}</h3>
    <form class="ui form" action="{{route('users.settings.store')}}" method="post">
      {{ csrf_field() }}
      <div class="required field{{ $errors->has('first_name') ? ' has-error' : '' }}">
          <label for="first_name">{{ $REG['FIRST']}}</label>
          <input value="{{$user->first_name}}" id="first_name" type="text" name="first_name" value="{{ old('first_name') }}" required autofocus>
              @if ($errors->has('first_name'))
                              <span class="help-block">
                                  <strong>{{ $errors->first('first_name') }}</strong>
                              </span>
              @endif
      </div>

      <div class="required field{{ $errors->has('last_name') ? ' has-error' : '' }}">
          <label for="last_name">{{ $REG['LAST']}}</label>
          <input value="{{$user->last_name}}" id="last_name" type="text" name="last_name" value="{{ old('last_name') }}" required autofocus>
              @if ($errors->has('last_name'))
                              <span class="help-block">
                                  <strong>{{ $errors->first('last_name') }}</strong>
                              </span>
              @endif
      </div>


      <div class="required field{{ $errors->has('email') ? ' has-error' : '' }}">
          <label for="email">{{ $REG['EMAIL']}}</label>
           <input value="{{$user->email}}" id="email" type="email" name="email" value="{{ old('email') }}" required>

                      @if ($errors->has('email'))
                          <span class="help-block">
                              <strong>{{ $errors->first('email') }}</strong>
                          </span>
                      @endif

      </div>

      <div class="field required">
          <label for="country">{{$REG['COUNTRY']}}</label>
          <select required id="country" name ="country"></select>

          @if ($errors->has('country'))
              <span class="help-block">
                  <strong>{{ $errors->first('country') }}</strong>
              </span>
          @endif

      </div>

      <div class="required field{{ $errors->has('affilation') ? ' has-error' : '' }}">
          <label for="affilation">{{ $REG['AFF']}}</label>
          <input value="{{$user->affilation}}" id="affilation" type="text" name="affilation" value="{{ old('affilation') }}" required autofocus>
              @if ($errors->has('affilation'))
                              <span class="help-block">
                                  <strong>{{ $errors->first('affilation') }}</strong>
                              </span>
              @endif
      </div>





      <div class="field{{ $errors->has('password') ? ' has-error' : '' }}">
          <label for="password">{{ $REG['NEW_PASS']}}</label>
           <input id="email" type="password" name="password">

                      @if ($errors->has('password'))
                          <span class="help-block">
                              <strong>{{ $errors->first('password') }}</strong>
                          </span>
                      @endif

      </div>

      <div class="field">
          <label for="password-confirm">{{ $REG['CONF_PASS']}}</label>
              <input id="password-confirm" type="password" name="password_confirmation">
      </div>

      <div class="field text-center">
          <button class="ui button primary" type="submit">{{ $REG['BTN_SAVE']}}</button>
          <button class="ui button " type="reset">{{ $REG['BTN_RESET']}}</button>
      </div>

    </form>
  </div>

  @push('script')

    <script>
      var scountry = '{{ $REG['S-COUNTRY'] }}';
      var scity = '{{ $REG['S-CITY'] }}';
    </script>

    <script src="{{ asset('js/countries.js') }}"></script>

    <script type="text/javascript">
      populateCountries("country");

      $(document).ready(function(){
        $('#country').val('{{$user->country}}');

      });
    </script>




  @endpush
@endsection
