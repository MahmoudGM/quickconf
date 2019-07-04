@extends('layouts.app')
<?php
$lang = Session::get('lang');
$CONF = parse_ini_file(base_path('language/'.$lang.'/CONFERENCE.ini'));
?>

<title>{{$CONF['EDIT_TITLE']}}</title>
@section('content')

<script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
<script src="{{ asset('js/ckeditor/config.js') }}"></script>

<div class="container ui">

{!! Breadcrumbs::render('editConf',$conference) !!}
<h2 class="ui dividing header">{{$CONF['EDIT_TITLE']}} {{ $conference->confAcronym }} {{ $conference->confEdition }}</h2>
    @include('layouts.errors')

<form class="ui form tiny" method="POST" action="{{ route('conferences.edit',[$conference->confAcronym, $conference->confEdition ] ) }}">
  {{ csrf_field() }}

  <div class="panel">
  <div class="header">
    <h3>{{$CONF['H-NAME']}}</h3>
   </div>
   <div class="body">
  <div class="field">
    <div class="two fields">
        <div class="field required">
          <label for="confAcronym">{{$CONF['ACRONYM']}}</label>
          <input value="{{ $conference->confAcronym }}" name="confAcronym" id="confAcronym"  type="text" autofocus>
        </div>
        <div class="field required">
          <label for="confName">{{$CONF['NAME']}}</label>
          <input value="{{ $conference->confName }}"  name="confName" id="confName" required type="text">
        </div>
    </div>

    <div class="field required">
        <label for="researchArea">{{$CONF['H-RESEARCH']}}</label>
        <input value="{{ $conference->researchArea }}"  name="researchArea" id="researchArea" required type="text">
      </div>

    <div class="field required">
         <label for="confDesc">{{$CONF['H-DESC']}}</label>
         <textarea name="confDesc" id="confDesc" cols="30" rows="10">{{ $conference->confDesc }}</textarea>
     </div>

     <script>
          CKEDITOR.replace('confDesc');
      </script>
     
  </div>
  </div>
  </div>



  <div class="panel">
  <div class="header">
    <h3>{{$CONF['H-EVENT']}}</h3>
  </div>
  <div class="body">
  <div class="three fields">
        <div class="field required">
            <label for="country">{{$CONF['COUNTRY']}}</label>
            <select required id="country" name ="country"></select>
        </div>
        <div class="field required">
          <label for="city">{{$CONF['CITY']}}</label>
          <input value="{{ $conference->city }}" type="text"  required name="city" id="city">
        </div>
        <div class="field required">
          <label for="confAdress">{{$CONF['ADRESS']}}</label>
          <input value="{{ $conference->confAdress }}"  name="confAdress" id="confAdress" required type="text">
        </div>

    </div>

  <div class="field">
    <div class="three fields">
        <div class="field required">
          <label for="confUrl">{{$CONF['URL']}}</label>
          <input value="{{ $conference->confUrl }}"  name="confUrl" id="confUrl" required  type="url" autofocus>
        </div>
        <div class="field required">
          <label for="confMail">{{$CONF['MAIL']}}</label>
          <input value="{{ $conference->confMail }}"  name="confMail" id="confMail" required type="email">
        </div>
        <div class="field required">
          <label for="confEdition">{{$CONF['EDITION']}}</label>
          <input value="{{ $conference->confEdition }}"  name="confEdition" id="confEdition" required type="number">
        </div>
    </div>
  </div>
  </div>
</div>


<div class="panel">
<div class="header">
<h3>{{$CONF['H-DATE']}}</h3>
</div>
  <div class="body">
  <div class="field">
    <div class="three fields">

    <div class="field required">
        <label for="start_date">{{$CONF['START_DATE']}}</label>
            <div class="ui calendar" id="date_start">
              <div class="ui input left icon">
                <i class="calendar icon"></i>
                <input  value="{{ $conference->start_date }}" name="start_date" id="start_date" type="text">
              </div>
            </div>
        </div>

        <div class="field required">
        <label for="end_date">{{$CONF['END_DATE']}}</label>
            <div class="ui calendar" id="date_end">
              <div class="ui input left icon">
                <i class="calendar icon"></i>
                <input value="{{ $conference->end_date }}" name="end_date" id="end_date" type="text">
              </div>
            </div>
        </div>

        <div class="field required">
        <label for="submission_deadline">{{$CONF['SUBM_DEAD']}}</label>
            <div class="ui calendar" id="deadline_sub">
              <div class="ui input left icon">
                <i class="calendar icon"></i>
                <input value="{{ $conference->submission_deadline }}" name="submission_deadline" id="submission_deadline" type="text">
              </div>
            </div>
        </div>

        <div class="field required">
        <label for="review_deadline">{{$CONF['REV_DEAD']}}</label>
            <div class="ui calendar" id="deadline_rev">
              <div class="ui input left icon">
                <i class="calendar icon"></i>
                <input value="{{ $conference->review_deadline }}" name="review_deadline" id="review_deadline" type="text">
              </div>
            </div>
        </div>

        <div class="field required">
        <label for="cam_ready_deadline">{{$CONF['CAM_DEAD']}}</label>
            <div class="ui calendar" id="deadline_cam">
              <div class="ui input left icon">
                <i class="calendar icon"></i>
                <input value="{{ $conference->cam_ready_deadline }}" name="cam_ready_deadline" id="cam_ready_deadline" type="text">
              </div>
            </div>
        </div>

        </div>

    </div>
</div>
</div>

<div class="panel">
<div class="header">
  <h3>{{$CONF['H-ORG']}}</h3>
</div>
  <div class="body">
    <div class="three fields">
      <div class="field required">
        <label for="organizer">{{$CONF['ORG']}}</label>
        <input value="{{ $conference->organizer }}" name="organizer" id="organizer" required type="text">
      </div>
      <div class="field">
        <label for="organizerWebPage">{{$CONF['O_WEB']}}</label>
        <input value="{{ $conference->organizerWebPage }}"  name="organizerWebPage" id="organizerWebPage" type="url">
      </div>

      <div class="field">
        <label for="organizerMail">{{$CONF['O_MAIL']}}</label>
        <input value="{{ $conference->organizerMail }}"  name="organizerMail" id="organizerMail" type="email">
      </div>


      <div class="field required">
        <label for="phone">{{$CONF['PHONE']}}</label>
        <input value="{{ $conference->phone }}" name="phone" id="phone" required type="text" placeholder="000-0000-0000">
      </div>

    </div>
  </div>
</div>

  </div>

</div>








    <div class="field text-center" style="margin-bottom:50px">
        <button class="ui button primary" type="submit">{{ $CONF['BTN_EDIT'] }}</button>
        <button class="ui button " type="reset">{{ $CONF['BTN_RESET'] }}</button>
    </div>

  </form>


</div>
@push('script')
<script>

$(document).ready(function(){
  $('#country').val('{{$conference->country}}');
  $('#city').val('{{$conference->confCity}}');
  
});
var scountry = '{{ $CONF['S-COUNTRY'] }}';
var scity = '{{ $CONF['S-CITY'] }}';
</script>
  <script src="{{ asset('js/countries.js') }}"></script>
<script>
	populateCountries("country", "city"); // first parameter is id of country drop-down and second parameter is id of state drop-down

  $(document).ready(function(){
    
    $('#country').val('{{$conference->country}}');
    $('#city').val('{{$conference->city}}');
  });

 
</script>
@endpush
@endsection