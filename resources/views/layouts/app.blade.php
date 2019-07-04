<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->

    <link href="{{ asset('assets/semantic-ui/semantic.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/semantic-ui-calendar/dist/calendar.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/buttons.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/select.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/editor.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/selectize.css') }}" rel="stylesheet">
    <link href="{{ asset('css/selectize.default.css') }}" rel="stylesheet">
    <link href="{{ asset('css/dropify.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/jquery-ui.min.css') }}" rel="stylesheet">

<style>

ul.ui-autocomplete.ui-menu{
  width:318px;
  top:27px !important;
  left:-302px !important;
  }

  .search-input{
    width:350px !important
  }

  #result-conf {
    display: block;
    position:relative
}
.ui-autocomplete {
    position: absolute;
}


body .menu{
    font-size: 15px !important
}
body{
    font-size: 15px !important
}

html, body {
  height: 100%;
  margin: 0;
}
body>.contentApp {
  min-height: 100%;
}

.ui.menu.fixed{
    //background-color:#95a5a6 !important;
}

.footer {
  height: 50px;
  margin-top: 10px;

  text-align:center;
  padding:13px
}

</style>



    @stack('style')


    <!-- Scripts -->
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    </script>
</head>
<body>
<?php
      //$lang=$request->session()->get('lang');
      if($view_name != 'install'){
      $lang = Session::get('lang') ;
      $MENU = parse_ini_file(base_path('language/'.$lang.'/MENU.ini'));
      $LOGIN_USER = parse_ini_file(base_path('language/'.$lang.'/LOGIN_USER.ini'));
      }

?>
<div class="contentApp">
<div id="app">
    <div class="ui container">


    @if (($view_name == 'home')||($view_name == 'conferences.create') || ($view_name == 'auth.settings') || ($view_name == 'conferences.comite.chooseTopics') || ($view_name == 'conferences.comite.editTopics') )
      @include('layouts.menuhome')
    @elseif(strpos($view_name,'admin') !== false)
      @include('layouts.menuadmin')
    @elseif($view_name == 'install')

    @else
      @include('layouts.menu')
    @endif


  </div>

        @yield('content')
    </div>

    </div>
<footer class="footer">
&copy; 2017 QuickConf All rights reserved
</footer>

    <!-- Scripts -->

    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('assets/semantic-ui/semantic.min.js') }}"></script>
    <script src="{{ asset('assets/semantic-ui-calendar/dist/calendar.min.js') }}"></script>
    <script src="{{ asset('js/sem.js') }}"></script>
    <script src="{{ asset('js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('js/jquery-confirm.js') }}"></script>

    <script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/dataTables.semanticui.min.js') }}"></script>
    <script src="{{ asset('js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('js/responsive.semanticui.min.js') }}"></script>
    <script src="{{ asset('js/handlebars-v4.0.5.js') }}"></script>
    <script src="{{ asset('js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('js/buttons.colVis.min.js') }}"></script>
    <script src="{{ asset('js/fnFilterClear.js') }}"></script>

    <script src="{{ asset('js/buttons.flash.min.js') }}"></script>
    <script src="{{ asset('js/jszip.min.js') }}"></script>
    <script src="{{ asset('js/pdfmake.min.js') }}"></script>
    <script src="{{ asset('js/vfs_fonts.js') }}"></script>
    <script src="{{ asset('js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('js/jscolor.min.js') }}"></script>
    


    <script src="{{ asset('js/selectize.js') }}"></script>


    @include('layouts.nooty')

    @stack('script')

</body>
</html>
