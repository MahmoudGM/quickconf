<div class="ui menu top fixed inverted">

      <a class="active item" href="/">
         <img src="{{ asset('img/logoApp.svg') }}" alt="logo" width="40px" style="width:102px">
      </a>
        
      @if (!Auth::guest())

          <a class="item" href="{{ route('conferences.papers.create', [$conference->confAcronym, $conference->confEdition] ) }}">Submit paper</a>        
          <a class="item" href="{{ route('conferences.authors.mypapers', [$conference->confAcronym, $conference->confEdition] ) }}">My papers</a>                  

          
        {{--</div>
      </div>--}}

        @endif




      <div class="right menu">
       <form action="/changeLanguage/fr" method="POST">
        {{csrf_field()}}
        @if($lang == 'fr')
          <button style="background-color:#21ba45;color:#000;padding: 8px;margin-top: 9px;" type="submit" class="inverted green lang button  ui">Fr</button>
        @else
          <button style="padding: 8px;margin-top: 9px;" type="submit" class="inverted green lang button  ui">Fr</button>
        @endif
      </form>
      <form action="/changeLanguage/en" method="POST">
        {{csrf_field()}}
        @if($lang == 'en')
          <button style="background-color:#21ba45;color:#000;padding: 8px;margin-top: 9px;" type="submit" class=" inverted green lang button  ui">En</button>
        @else
          <button style=" padding: 8px;margin-top: 9px;" type="submit" class=" inverted green lang button  ui">En</button>
        @endif
      </form>
       @if (Auth::guest())

            <a class="item" href="{{ route('login') }}">{{ $MENU['login'] }}</a>
            <a class="item" href="{{ route('register') }}">{{ $MENU['register'] }}</a>
        @else
        
        
        <div class="ui dropdown item">
           {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}  <i class="dropdown icon"></i>
          <div class="menu">
            <a class="item" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                    {{ $MENU['logout'] }}
            </a>

            <form class="item"  id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                            {{ csrf_field() }}
                                        </form>
            
          </div>
          
        </div>
        @endif
      </div>
    </div>
    </div>