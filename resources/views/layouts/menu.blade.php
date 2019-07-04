

<div class="ui container menu top fixed inverted full-menu">

      <a class="active item" href="/">
         <img src="{{ asset('img/logoApp.svg') }}" alt="logo" width="40px" style="width:102px">
      </a>

      @if (!Auth::guest())
      {{--<div class="ui dropdown item">
        Admin <i class="dropdown icon"></i>
        <div class="menu"> --}}
        @if(isset($conference->pivot->role))
          @if($conference->pivot->role=='A')
            <a href="{{ route('conferences.show', [$conference->confAcronym, $conference->confEdition] ) }}" class="item"> Dashboard {{--{{$conference->confAcronym}}{{$conference->confEdition}} --}}</a>
            
            <div class="ui dropdown item" >
            {{ $MENU['ADMIN_U']['config'] }} <i class="dropdown icon"></i>
              <div class="menu">
                  <a href="{{ route('conferences.edit', [$conference->confAcronym, $conference->confEdition] ) }}" class="item">{{ $MENU['ADMIN_U']['config-conf'] }}</a>
                  <a href="{{ route('conferences.submission.edit', [$conference->confAcronym, $conference->confEdition] ) }}" class="item">{{ $MENU['ADMIN_U']['config-sub'] }}</a>
                  <a class="item" href="{{ route('conferences.comite.index', [$conference->confAcronym, $conference->confEdition] ) }}"> Committee</a>
                  <a class="item" href="{{ route('conferences.messages.index', [$conference->confAcronym, $conference->confEdition] ) }}"> {{ $MENU['ADMIN_U']['msg'] }}</a>
                  <a class="item" href="{{ route('conferences.topics.index', [$conference->confAcronym, $conference->confEdition] ) }}">{{ $MENU['ADMIN_U']['topic'] }}</a>
                  <a class="item" href="{{ route('conferences.pquestions.index', [$conference->confAcronym, $conference->confEdition] ) }}">{{ $MENU['ADMIN_U']['pquestion'] }}</a>
                  <a class="item" href="{{ route('conferences.rquestions.index', [$conference->confAcronym, $conference->confEdition] ) }}">{{ $MENU['ADMIN_U']['rquestion'] }}</a>
                 
                  <a class="item" href="{{ route('conferences.criterias.index', [$conference->confAcronym, $conference->confEdition] ) }}">{{ $MENU['ADMIN_U']['criteria'] }}</a>
                  <a class="item" href="{{ route('conferences.paperstatus.index', [$conference->confAcronym, $conference->confEdition] ) }}">{{ $MENU['ADMIN_U']['pstatus'] }}</a>
                  

              </div>
            </diV>
            <a class="item" href="{{ route('conferences.papers.index', [$conference->confAcronym, $conference->confEdition] ) }}">{{ $MENU['ADMIN_U']['sub_papers'] }}</a>
            <a class="item" href="{{ route('conferences.authors.index', [$conference->confAcronym, $conference->confEdition] ) }}">{{ $MENU['ADMIN_U']['authors'] }}</a>
            <a class="item" href="{{ route('conferences.comite.assignStatus', [$conference->confAcronym, $conference->confEdition] ) }}">Assign status</a>
              {{--@if( ($conference->camReady === 'Y')and($conference->is_cam_ready_open === 'Y') )
                <a class="item" href="{{ route('conferences.papers.saveCamReady', [$conference->confAcronym, $conference->confEdition] ) }}">Save cameraReady</a>
              @endif --}}
                
            <a class="item" href="{{ route('conferences.slots.index', [$conference->confAcronym, $conference->confEdition] ) }}">Program</a>
            
            @elseif( ($conference->pivot->role!='A') and ($conference->pivot->role=='Aut') )
              <a class="item" href="{{ route('conferences.papers.create', [$conference->confAcronym, $conference->confEdition] ) }}">Submit paper</a>        
              <a class="item" href="{{ route('conferences.authors.mypapers', [$conference->confAcronym, $conference->confEdition] ) }}">My papers</a>                  
            @elseif( ($conference->pivot->role!='A') and ($conference->pivot->role=='C') )
              <a class="item" href="{{ route('conferences.papers.index', [$conference->confAcronym, $conference->confEdition] ) }}">{{ $MENU['ADMIN_U']['sub_papers'] }}</a>
              <a class="item" href="{{ route('conferences.authors.index', [$conference->confAcronym, $conference->confEdition] ) }}">{{ $MENU['ADMIN_U']['authors'] }}</a>
              <a class="item" href="{{ route('conferences.comite.assignStatus', [$conference->confAcronym, $conference->confEdition] ) }}">Assign status to papers</a>
            @elseif($conference->pivot->role =='R') 
              <a class="item" href="{{ route('conferences.comite.mypapers', [$conference->confAcronym, $conference->confEdition] ) }}">My papers</a>
           @endif
          @else
          
          @endif


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
            <a class="item" href="{{route('users.settings')}}">{{ $MENU['settings'] }}</a>
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

<div class="small-menu">
      <a class="active item" href="/">
         {{-- config('app.name', 'Submission') --}}
         {{ $MENU['home'] }}
      </a>


      <span class="pull-right toggle-menu" style="margin-left:6px">
        <i class="icon content large" style="color:#fff;cursor:pointer"></i>
        <i class="icon close large" style="color:#fff ; display:none;cursor:pointer"></i>
      </span>

      <span class="pull-right">
       <form action="/changeLanguage/fr" method="POST" style="display:inline-block">
        {{csrf_field()}}
        @if($lang == 'fr')
          <button style="background-color:#21ba45;color:#000;padding: 4px;margin-top: -3px;" type="submit" class="inverted green lang button  ui">Fr</button>
        @else
          <button style="padding: 4px;margin-top: -3px;" type="submit" class="inverted green lang button  ui">Fr</button>
        @endif
      </form>
      <form action="/changeLanguage/en" method="POST" style="display:inline-block">
        {{csrf_field()}}
        @if($lang == 'en')
          <button style="background-color:#21ba45;color:#000;padding: 4px;margin-top: -3px;" type="submit" class=" inverted green lang button  ui">En</button>
        @else
          <button style=" padding: 4px;margin-top: -3px;" type="submit" class=" inverted green lang button  ui">En</button>
        @endif
      </form>
      </span>


      <div class="main-menu" style="display:none">
      @if (!Auth::guest())
      {{--<div class="ui dropdown item">
        Admin <i class="dropdown icon"></i>
        <div class="menu"> --}}
        @if(isset($conference->pivot->role))
          @if($conference->pivot->role=='A')
            <span>
              <a href="{{ route('conferences.show', [$conference->confAcronym, $conference->confEdition] ) }}" class="item">{{$conference->confAcronym}}{{$conference->confEdition}}</a>
            </span>
            <div class="parent-menu" >
            <span>
                {{ $MENU['ADMIN_U']['config'] }}  <i class="dropdown icon"></i>
            </span>
              <div class="child-menu" style="display:none">
                  <a href="{{ route('conferences.edit', [$conference->confAcronym, $conference->confEdition] ) }}" class="item">{{ $MENU['ADMIN_U']['config-conf'] }}</a>
                  <a href="{{ route('conferences.submission.edit', [$conference->confAcronym, $conference->confEdition] ) }}" class="item">{{ $MENU['ADMIN_U']['config-sub'] }}</a>
                  <a class="item" href="{{ route('conferences.comite.index', [$conference->confAcronym, $conference->confEdition] ) }}"> Committee</a>
                  <a class="item" href="{{ route('conferences.messages.index', [$conference->confAcronym, $conference->confEdition] ) }}"> {{ $MENU['ADMIN_U']['msg'] }}</a>
                  <a class="item" href="{{ route('conferences.topics.index', [$conference->confAcronym, $conference->confEdition] ) }}">{{ $MENU['ADMIN_U']['topic'] }}</a>
                  <a class="item" href="{{ route('conferences.pquestions.index', [$conference->confAcronym, $conference->confEdition] ) }}">{{ $MENU['ADMIN_U']['pquestion'] }}</a>
                  <a class="item" href="{{ route('conferences.rquestions.index', [$conference->confAcronym, $conference->confEdition] ) }}">{{ $MENU['ADMIN_U']['rquestion'] }}</a>
                 
                  <a class="item" href="{{ route('conferences.criterias.index', [$conference->confAcronym, $conference->confEdition] ) }}">{{ $MENU['ADMIN_U']['criteria'] }}</a>
                  <a class="item" href="{{ route('conferences.paperstatus.index', [$conference->confAcronym, $conference->confEdition] ) }}">{{ $MENU['ADMIN_U']['pstatus'] }}</a>
              </div>
            </diV>


            @endif
            @endif
            @endif
      </div>

      
</div>

@push('script')
<script>
 $(document).ready(function() {

    $('.toggle-menu').click(function(){
      $('.main-menu').slideToggle();
    });

    $('.icon.content').click(function(){
      $(this).hide();
      $(this).siblings('.close').show();
    });

    $('.close').click(function(){
      $(this).hide();
      $(this).siblings('.content').show();
    });

     $('.parent-menu>span').click(function(){
      $(this).siblings('.child-menu').slideToggle();
    });


 });
</script>
@endpush