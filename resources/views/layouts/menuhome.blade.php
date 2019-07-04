
@push('style')

@endpush
<div class="ui menu top fixed inverted" style="z-index: 0;">

      <a class="active item" href="/">
         <img src="{{ asset('img/logoApp.svg') }}" alt="logo" width="40px" style="width:102px">
      </a>





      <div class="right menu">

      {{--
    <div class="ui right aligned category search item search-conf" style="background-color: #fff;margin-right: 16px;border: 2px solid #3d3e3f;width: 322px; " >
      <div class="ui transparent icon input" >
        <input class="search-input" autocomplete="off" name="q" type="search" placeholder="Search conferences">
        <div id="result-conf"></div>
        <a style="display:inline;margin-left: -10px;" class="search-btn"><i  class="search link icon"></i></a>
      </div>
    </div>

    --}}

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
    <?php $Allconferences = \DB::table('conferences')->select('confName','confAcronym','confEdition')->get()->toArray();


          $array = array_values($Allconferences);
          $new_array = array();
            foreach( $array as $key => $value) {
                $new_array[] = array( $value->confName.' '.$value->confAcronym.' '.$value->confEdition);
            }

             ?>
    @push('script')
      <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>

$(document).ready(function () {
    $(document).tooltip();
        var availableTags = [];
        <?php
        for($i=0;$i<count($new_array);$i++){ ?>
          availableTags.push({value:'{{route('conferences.papers.create',[$Allconferences[$i]->confAcronym,$Allconferences[$i]->confEdition])}}',label:'<?php echo (string)$new_array[$i][0] ?>'}) ;
        <?php } ?>

    $( ".search-input" ).autocomplete({

      source: function(request, response) {
        var results = $.ui.autocomplete.filter(availableTags, request.term);
        response(results.slice(0, 10));
    },
      appendTo: "#result-conf",
      //source: availableTags,
      select: function( event, ui ) {
            window.location.href = ui.item.value;
        }
    });
  } );

/*
      var path = "{{ route('find') }}";
       $(".search-input").typeahead({
          source: function(query, process) {
            return $.get(path, {query: query}, function(data){
              return process(data);
            });
          }
       });
*/


/*
       var engine = new Bloodhound({
        remote: {
            url: '/find?q=%QUERY%',
            wildcard: '%QUERY%'
        },
        datumTokenizer: Bloodhound.tokenizers.whitespace('q'),
        queryTokenizer: Bloodhound.tokenizers.whitespace
    });

    $(".search-input").typeahead({
        hint: true,
        highlight: true,
        minLength: 1
    }, {
        source: engine.ttAdapter(),

        // This will be appended to "tt-dataset-" to form the class name of the suggestion menu.
        name: 'confList',

        // the key from the array we want to display (name,id,email,etc...)
        templates: {
            empty: [
                '<div style="padding:10px;margin-left:-16px;width: 318px;margin-top:9px;background-color:#eee" >Nothing found.</div>'
            ],
            header: [
                '<div class="list-group search-results-dropdown">'
            ],
            suggestion: function (data) {
                return '<div style="padding:10px;margin-left:-16px;width: 318px;background-color:#eee" >' + data.confAcronym + ' ' + data.confEdition + '</div> '
      }
        }
    });
*/


    </script>
    @endpush
