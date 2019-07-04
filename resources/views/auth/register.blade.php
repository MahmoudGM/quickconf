@extends('layouts.app')

@section('content')
<?php
    $lang = Session::get('lang') ;
    $REG = parse_ini_file(base_path('language/'.$lang.'/REGISTER.ini'));
?>
<div class="container">


<div class="ui two column centered grid">
  <div class="column">
        <div class="ui segment">
            <h3 class="ui dividing header">
                {{ $REG['BTN_REG']}}
            </h3>
            <form class="ui form" role="form" method="POST" action="{{ route('register') }}" >
                {{ csrf_field() }}
                <div class="required field{{ $errors->has('first_name') ? ' has-error' : '' }}">
                    <label for="first_name">{{ $REG['FIRST']}}</label>
                    <input id="first_name" type="text" name="first_name" value="{{ old('first_name') }}" required autofocus>
                        @if ($errors->has('first_name'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('first_name') }}</strong>
                                        </span>
                        @endif
                </div>

                <div class="required field{{ $errors->has('last_name') ? ' has-error' : '' }}">
                    <label for="last_name">{{ $REG['LAST']}}</label>
                    <input id="last_name" type="text" name="last_name" value="{{ old('last_name') }}" required autofocus>
                        @if ($errors->has('last_name'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('last_name') }}</strong>
                                        </span>
                        @endif
                </div>

                <div class="required field{{ $errors->has('email') ? ' has-error' : '' }}">
                    <label for="email">{{ $REG['EMAIL']}}</label>
                     <input id="email" type="email" name="email" value="{{ old('email') }}" required>

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
                    <input id="affilation" type="text" name="affilation" value="{{ old('affilation') }}" required autofocus>
                        @if ($errors->has('affilation'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('affilation') }}</strong>
                                        </span>
                        @endif
                </div>

                <div class="field required">
                    <label for="grade">{{ $REG['GRADE']}}</label>
                    <select class="grade" name="grade"  required>
                        <option class="placeholder" value="" disabled selected >Select</option>
                        <option value="Teaching Assistant">Teaching Assistant</option>
                        <option value="PhD Candidate">PhD Candidate</option>
                        <option value="Dr.">Dr.</option>
                        <option value="Master student">Master student</option>
                        <option value="Assoc. Prof. Dr.">Assoc. Prof. Dr.</option>
                        <option value="Prof.">Prof.</option>
                        <option value="Professional">Professional</option>
                    </select>
                </div>





                <div class="required field{{ $errors->has('password') ? ' has-error' : '' }}">
                    <label for="password">{{ $REG['PASS']}}</label>
                     <input id="email" type="password" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif

                </div>

                <div class="required field">
                    <label for="password-confirm">{{ $REG['CONF_PASS']}}</label>
                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                </div>

                <div class="field text-center">
                    <button class="ui button primary" type="submit">{{ $REG['BTN_REG']}}</button>
                    <button class="ui button " type="reset">{{ $REG['BTN_RESET']}}</button>
                </div>

            </form>
        </div>
  </div>

</div>

</div>

@push('script')

  <script>
    var scountry = '{{ $REG['S-COUNTRY'] }}';
    var scity = '{{ $REG['S-CITY'] }}';
  </script>

  <script src="{{ asset('js/countries.js') }}"></script>

  <script type="text/javascript">
    populateCountries("country");
  </script>


@endpush


@endsection
