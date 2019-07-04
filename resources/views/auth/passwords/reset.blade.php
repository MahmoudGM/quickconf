@extends('layouts.app')

@section('content')


<div class="container">
    


<div class="ui two column centered grid">
  <div class="column">
        <div class="ui segment">
            <h3 class="ui dividing header">
                Reset Password
            </h3>

            @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
            @endif

            <form class="ui form" role="form" method="POST" action="{{ route('password.request',[$lang]) }}" >
                {{ csrf_field() }}
                  <input type="hidden" name="token" value="{{ $token }}">
               

                <div class="field{{ $errors->has('email') ? ' has-error' : '' }}">
                    <label for="email">Email</label>
                     <input id="email" type="email" name="email" value="{{ old('email') }}" required>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif

                </div>

                <div class="field{{ $errors->has('password') ? ' has-error' : '' }}">
                    <label for="password">Password</label>
                     <input id="email" type="password" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif

                </div>

                <div class="field{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                    <label for="password-confirm">Confirm Password</label>
                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                            @if ($errors->has('password_confirmation'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('password_confirmation') }}</strong>
                                        </span>
                            @endif
                </div>


                <button class="ui button primary" type="submit">Submit</button>
                <button class="ui button " type="reset">Reset</button>
            </form>
        </div>
  </div>
  
</div>

</div>



@endsection
