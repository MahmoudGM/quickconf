@extends('layouts.app')
<?php
$lang = Session::get('lang');
$H = parse_ini_file(base_path('language/'.$lang.'/HOME.ini'));
?>

@if (count($count_topics) == 0)
    <title>{{$H['CHOOSE']}}</title>
@else
    <title>{{$H['EDIT_CHOOSE']}}</title>
@endif


@section('content')
<div class="ui container">

    @if (count($count_topics) == 0)
    <h2>{{$H['CHOOSE']}}</h2>
    
    <form method="post" action="{{route('conferences.comite.storeChoose',[$conference->confAcronym,$conference->confEdition])}}" class="ui form">
        {{ csrf_field() }}
      <div class="field required">
        <label for="choose">{{$H['SELECT_T']}}</label>
        <select id="choose" class="ui fluid dropdown topics search" multiple="" name="topics[]" required="">
          @foreach($topics as $tp)
              <option value="{{$tp->id}}">{{$tp->label}} </option>
          @endforeach
        </select>
        </div>
        <div class="field text-center" style="margin:20px 0 40px 0">
          <button class="ui button primary" type="submit">{{$H['BTN_CHOOSE']}}</button>
          <button id="reset" class="ui button " type="reset">{{$H['BTN_RESET']}}</button>
      </div>
    </form>

    @else

    <h2>{{$H['EDIT_CHOOSE']}}</h2>
    
    <form method="post" action="{{route('conferences.comite.storeEdit',[$conference->confAcronym,$conference->confEdition])}}" class="ui form">
        {{ csrf_field() }}
      <div class="field required">
        <label for="choose">{{$H['SELECT_T']}}</label>
        <select id="choose" class="ui fluid dropdown topics search" multiple="" name="topics[]" required="">

            @foreach($count_topics as $ctp)
                <option selected  value="{{$ctp->id}}">{{$ctp->label}} </option>           
            @endforeach
            @foreach($topics2 as $tp)
                <option value="{{$tp->id}}">{{$tp->label}} </option>
            @endforeach
        </select>
        </div>
        <div class="field text-center" style="margin:20px 0 40px 0">
          <button class="ui button primary" type="submit">{{$H['BTN_EDIT']}}</button>
          <button id="reset" class="ui button " type="reset">{{$H['BTN_RESET']}}</button>
      </div>
    </form>

    @endif

</div>

@push('script')
<script>
$(document).ready(function() {

    $("#reset").click(function(){
        $('.topics').dropdown('restore defaults');
    })
    


});
</script>
@endpush

@endsection