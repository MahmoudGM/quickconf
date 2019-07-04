
<div class="ui menu top fixed inverted">

      <a class="active item" href="{{ route('admin.dashboard') }}">
         <img src="{{ asset('img/logoApp.svg') }}" alt="logo" width="40px" style="width:102px">
      </a>

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
       
  
        
        <?php $admin = \App\Admin::all()->first() ;?>
        @if (!auth('admin')->guest())
        <div class="ui dropdown item">
           {{ $admin->first_name }}<i class="dropdown icon"></i>
          <div class="menu">
            <a class="item" href="{{ route('admin.logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                    {{ $MENU['logout'] }}
            </a>

            <form class="item"  id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                                            {{ csrf_field() }}
                                        </form>
            
          </div>
          
        </div>
        @else

        @endif
      </div>
    </div>