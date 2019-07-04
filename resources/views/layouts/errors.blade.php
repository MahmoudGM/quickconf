<?php
    $lang = Session::get('lang');
    $ERR = parse_ini_file(base_path('language/'.$lang.'/ERRORS.ini'));
?>
@if (count($errors))

<div class="ui error message">
  <div class="header">
    {{$ERR['HEAD_MSG']}}
  </div>
  <ul class="list">
    @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
    @endforeach
  </ul>
</div>

@endif