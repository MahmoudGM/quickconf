<!DOCTYPE html>
<?php
      $lang = Session::get('lang') ;
      $ERR = parse_ini_file(base_path('language/'.$lang.'/ERRORS.ini'));
?>
<html lang="{{ $lang }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
        <link href="{{ asset('assets/semantic-ui/semantic.min.css') }}" rel="stylesheet">
        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Raleway', sans-serif;
                font-weight: 100;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 220px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 12px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 130px;
            }


        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            @if (Route::has('login'))
                <div class="top-right links">
                   
                </div>
            @endif

            <div class="content">
                <div class="title m-b-md">
                   {{$code}}
                </div>

                <div class="links" style="font-size:20px">
                    @if($code == 404)
                        <i class="frown icon"></i> {{$ERR['404']}} <br><br>
                    @else
                        <i class="frown icon"></i> {{$ERR['500']}}<br><br>
                    @endif
                     <a class="ui button teal mini" href="{{ URL::previous() }}"> {{$ERR['BACK']}}</a>
                     <a class="ui button teal mini" href="{{ url('/') }}"> {{$ERR['HOME']}}</a>
                </div>
            </div>
        </div>
    </body>
</html>
