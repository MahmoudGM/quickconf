@extends('layouts.app')
<?php
$lang = Session::get('lang');
$CONF = parse_ini_file(base_path('language/'.$lang.'/CONFERENCE.ini'));
?>

<title>{{$CONF['CREATE_TITLE']}}</title>

<script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
<script src="{{ asset('js/ckeditor/config.js') }}"></script>

<div class="container ui">


<h2 class="ui dividing header">{{$CONF['CREATE_TITLE']}}</h2>
@include('layouts.errors')

<form class="ui form tiny" method="POST" action="{{ route('conferences.store' ) }}">
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
          <input name="confAcronym" id="confAcronym"  type="text" autofocus>
        </div>
        <div class="field required">
          <label for="confName">{{$CONF['NAME']}}</label>
          <input name="confName" id="confName" required type="text">
        </div>
    </div>

    <div class="field required">
        <label for="researchArea">{{$CONF['H-RESEARCH']}}</label>
        <input name="researchArea" id="researchArea" required type="text">
      </div>

    <div class="field required">
         <label for="confDesc">{{$CONF['H-DESC']}}</label>
         <textarea name="confDesc" id="confDesc" cols="30" rows="10"></textarea>
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
          <label for="confCity">{{$CONF['CITY']}}</label>
          <select required name ="city" id ="city"></select>
        </div>
        <div class="field required">
          <label for="confAdress">{{$CONF['ADRESS']}}</label>
          <input name="confAdress" id="confAdress" required type="text">
        </div>

    </div>

  <div class="field">
    <div class="three fields">
        <div class="field required">
          <label for="confUrl">{{$CONF['URL']}}</label>
          <input name="confUrl" id="confUrl" required  type="url" autofocus>
        </div>
        <div class="field required">
          <label for="confMail">{{$CONF['MAIL']}}</label>
          <input name="confMail" id="confMail" required type="email">
        </div>
        <div class="field required">
          <label for="confEdition">{{$CONF['EDITION']}}</label>
          <input name="confEdition" id="confEdition" required type="number">
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
                  <input name="start_date" id="start_date" type="text" required>
                </div>
              </div>
          </div>

        <div class="field required">
          <label for="end_date">{{$CONF['END_DATE']}}</label>
              <div class="ui calendar" id="date_end">
                <div class="ui input left icon">
                  <i class="calendar icon"></i>
                  <input name="end_date" id="end_date" type="text" required>
                </div>
              </div>
          </div>

        <div class="field required">
          <label for="submission_deadline">{{$CONF['SUBM_DEAD']}}</label>
              <div class="ui calendar" id="deadline_sub">
                <div class="ui input left icon">
                  <i class="calendar icon"></i>
                  <input name="submission_deadline" id="submission_deadline" type="text" required>
                </div>
              </div>
          </div>

         <div class="field required">
            <label for="review_deadline">{{$CONF['REV_DEAD']}}</label>
                <div class="ui calendar" id="deadline_rev">
                  <div class="ui input left icon">
                    <i class="calendar icon"></i>
                    <input name="review_deadline" id="review_deadline" type="text" required>
                  </div>
                </div>
            </div>

        <div class="field required">
          <label for="cam_ready_deadline">{{$CONF['CAM_DEAD']}}</label>
              <div class="ui calendar" id="deadline_cam">
                <div class="ui input left icon">
                  <i class="calendar icon"></i>
                  <input name="cam_ready_deadline" id="cam_ready_deadline" type="text" required>
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
        <input name="organizer" id="organizer" required type="text">
      </div>
      <div class="field">
        <label for="organizerWebPage">{{$CONF['O_WEB']}}</label>
        <input name="organizerWebPage" id="organizerWebPage" type="url">
      </div>

      <div class="field">
        <label for="organizerMail">{{$CONF['O_MAIL']}}</label>
        <input name="organizerMail" id="organizerMail" type="email">
      </div>

      <div class="field required">
        <label for="phone">{{$CONF['PHONE']}}</label>
        <input name="phone" id="phone" required type="text" placeholder="000-0000-0000">
      </div>
    </div>
  </div>
</div>












    <div class="field text-center" style="margin-bottom:-725px">
        <button class="ui button primary" type="submit">{{ $CONF['BTN_CREATE'] }}</button>
        <button class="ui button " type="reset">{{ $CONF['BTN_RESET'] }}</button>
    </div>

  </form>
</div>



@push('script')
<script>

</script>
  <script src="{{ asset('js/countries.js') }}"></script>
<script>
    var scountry = '{{ $CONF['S-COUNTRY'] }}';
    var scity = '{{ $CONF['S-CITY'] }}';
	populateCountries("country", "city"); // first parameter is id of country drop-down and second parameter is id of state drop-down


</script>
@endpush
