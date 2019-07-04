@extends('layouts.app')

<?php
$lang = Session::get('lang');
$CONFIG = parse_ini_file(base_path('language/'.$lang.'/CONFIG_SUB.ini'));
?>
<title>{{ $CONFIG['TITLE'] }} </title>

@section('content')

<div class="container ui">
{!! Breadcrumbs::render('editSub',$conference) !!}
 <h2 class="ui dividing header"> {{ $CONFIG['TITLE'] }} {{ $conference->confAcronym }} {{ $conference->confEdition }} </h2>
    @include('layouts.errors')
    <form class="ui form" method="POST" action="{{ route('conferences.submission.update',[$conference->confAcronym, $conference->confEdition ] ) }}">
    {{ csrf_field() }}
       
        
        <div class="panel">
            <div class="header">
                <h3>  {{ $CONFIG['H-FORM'] }}  </h3>
            </div>
                <div class="body">

                  <div class="three fields">
                      <div class="field required">
                          <label for="extended_submission_form">{{ $CONFIG['EXTENDED'] }}</label>
                          <div class="inline fields">
                              <div class="field">
                                  <div class="ui radio checkbox">
                                      <input name="extended_submission_form" @if( $conference->extended_submission_form == 'Y') {{ 'checked' }} @endif value="Y" tabindex="0" class="hidden" type="radio">
                                      <label>{{ $CONFIG['Y'] }}</label>
                                  </div>
                              </div>
                              <div class="field">
                                  <div class="ui radio checkbox">
                                      <input name="extended_submission_form" @if( $conference->extended_submission_form == 'N') {{ 'checked' }} @endif value="N" tabindex="0" class="hidden" type="radio">
                                      <label>{{ $CONFIG['N'] }}</label>
                                  </div>
                              </div>
                          </div>
                      </div>
                      
                      
                      <div class="field required">
                        <label for="file_type">{{ $CONFIG['F_TYPE'] }}</label>
                        <input type="text" name="file_type" id="file_type" value="{{$conference->file_type}}">
                      </div>
                  </div>


           
                </div>
          </div>
  
            
            <div class="panel">
              <div class="header">
                <h3>{{ $CONFIG['H-SUB_OPEN'] }}</h3>
              </div>
              <div class="body">
          
                <div class="three fields">
                    
                    <div class="field required">
                        <label for="is_submission_open">{{ $CONFIG['PAPERS'] }}</label>
                        <div class="inline fields">
                            <div class="field">
                                <div class="ui radio checkbox">
                                    <input name="is_submission_open" @if($conference->is_submission_open == 'Y') {{ 'checked' }} @endif value="Y" tabindex="0" class="hidden" type="radio">
                                    <label>{{ $CONFIG['Y'] }}</label>
                                </div>
                            </div>
                            <div class="field">
                                <div class="ui radio checkbox">
                                    <input name="is_submission_open" @if($conference->is_submission_open == 'N') {{ 'checked' }} @endif value="N" tabindex="0" class="hidden" type="radio">
                                    <label>{{ $CONFIG['N'] }}</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="field required">
                        <label for="camReady">{{ $CONFIG['CAM_R_ACTIVE'] }}</label>
                        <div class="inline fields">
                            <div class="field">
                                <div class="ui radio checkbox">
                                    <input name="camReady" @if($conference->camReady == 'Y') {{ 'checked' }} @endif value="Y" tabindex="0" class="hidden" type="radio">
                                    <label>{{ $CONFIG['Y'] }}</label>
                                </div>
                            </div>
                            <div class="field">
                                <div class="ui radio checkbox">
                                    <input name="camReady" @if($conference->camReady == 'N') {{ 'checked' }} @endif value="N" tabindex="0" class="hidden" type="radio">
                                    <label>{{ $CONFIG['N'] }}</label>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="field required">
                        <label for="is_cam_ready_open">{{ $CONFIG['CAM_R'] }}</label>
                        <div class="inline fields">
                            <div class="field">
                                <div class="ui radio checkbox">
                                    <input name="is_cam_ready_open" @if($conference->is_cam_ready_open == 'Y') {{ 'checked' }} @endif value="Y" tabindex="0" class="hidden" type="radio">
                                    <label>{{ $CONFIG['Y'] }}</label>
                                </div>
                            </div>
                            <div class="field">
                                <div class="ui radio checkbox">
                                    <input name="is_cam_ready_open" @if($conference->is_cam_ready_open == 'N') {{ 'checked' }} @endif value="N" tabindex="0" class="hidden" type="radio">
                                    <label>{{ $CONFIG['N'] }}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

              </div>  
            </div>

            <div class="panel">  
              <div class="header">
                <h3>{{ $CONFIG['H-REV'] }}</h3>
              </div>
              <div class="body">
                    <div class="three fields">
                        <div class="field required">
                            <label for="blind_review">{{ $CONFIG['BLIND'] }}</label>
                            <div class="inline fields">
                                <div class="field">
                                    <div class="ui radio checkbox">
                                        <input name="blind_review" @if($conference->blind_review == 'Y') {{ 'checked' }} @endif value="Y" tabindex="0" class="hidden" type="radio">
                                        <label>{{ $CONFIG['Y'] }}</label>
                                    </div>
                                </div>
                                <div class="field">
                                    <div class="ui radio checkbox">
                                        <input name="blind_review" @if($conference->blind_review == 'N') {{ 'checked' }} @endif value="N" tabindex="0" class="hidden" type="radio">
                                        <label>{{ $CONFIG['N'] }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="field required">
                            <label for="discussion_mode">{{ $CONFIG['DISCU'] }}</label>
                            <div class="inline fields">
                                <div class="field">
                                    <div class="ui radio checkbox">
                                        <input name="discussion_mode" @if($conference->discussion_mode == '1') {{ 'checked' }} @endif value="1" tabindex="0" class="hidden" type="radio">
                                        <label>{{ $CONFIG['NON'] }}</label>
                                    </div>
                                </div>
                                <div class="field">
                                    <div class="ui radio checkbox">
                                        <input name="discussion_mode" @if($conference->discussion_mode == '2') {{ 'checked' }} @endif value="2" tabindex="0" class="hidden" type="radio">
                                        <label>{{ $CONFIG['LOCAL'] }}</label>
                                    </div>
                                </div>
                                <div class="field">
                                    <div class="ui radio checkbox">
                                        <input name="discussion_mode" @if($conference->discussion_mode == '3') {{ 'checked' }} @endif value="3" tabindex="0" class="hidden" type="radio">
                                        <label>{{ $CONFIG['GLOBAL'] }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="field required">
                            <label for="ballot_mode">{{ $CONFIG['BALLOT'] }}</label>
                            <div class="inline fields">
                                <div class="field">
                                    <div class="ui radio checkbox">
                                        <input name="ballot_mode" @if($conference->ballot_mode == '1') {{ 'checked' }} @endif value="1" tabindex="0" class="hidden" type="radio">
                                        <label>{{ $CONFIG['TOPIC'] }}</label>
                                    </div>
                                </div>
                                <div class="field">
                                    <div class="ui radio checkbox">
                                        <input name="ballot_mode" @if($conference->ballot_mode == '2') {{ 'checked' }} @endif value="2" tabindex="0" class="hidden" type="radio">
                                        <label>{{ $CONFIG['GENERAL'] }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="field required">
                            <label for="nb_reviewer_per_item">{{ $CONFIG['NBR_REV'] }}</label>
                            <input type="number" name="nb_reviewer_per_item" id="nb_reviewer_per_item" value="{{$conference->nb_reviewer_per_item}}">
                      </div>
                    </div>
                  </div>
                  </div>

                  <div class="panel">  
              <div class="header">
                <h3>{{ $CONFIG['H-MAIL'] }}</h3>
              </div>
              <div class="body">
                    <div class="three fields">
                        
                        <div class="field required">
                            <label for="mail_on_upload">{{ $CONFIG['MAIL_UPLOAD'] }}</label>
                            <div class="inline fields">
                                <div class="field">
                                    <div class="ui radio checkbox">
                                        <input name="mail_on_upload" @if($conference->mail_on_upload == 1) {{ 'checked' }} @endif value="1" tabindex="0" class="hidden" type="radio">
                                        <label>{{ $CONFIG['Y'] }}</label>
                                    </div>
                                </div>
                                <div class="field">
                                    <div class="ui radio checkbox">
                                        <input name="mail_on_upload" @if($conference->mail_on_upload == 2) {{ 'checked' }} @endif value="2" tabindex="0" class="hidden" type="radio">
                                        <label>{{ $CONFIG['ADMIN'] }}</label>
                                    </div>
                                </div>
                                <div class="field">
                                    <div class="ui radio checkbox">
                                        <input name="mail_on_upload" @if($conference->mail_on_upload == 3) {{ 'checked' }} @endif value="3" tabindex="0" class="hidden" type="radio">
                                        <label>{{ $CONFIG['AUTHOR'] }}</label>
                                    </div>
                                </div>
                                <div class="field">
                                    <div class="ui radio checkbox">
                                        <input name="mail_on_upload" @if($conference->mail_on_upload == 4) {{ 'checked' }} @endif value="4" tabindex="0" class="hidden" type="radio">
                                        <label>{{ $CONFIG['N'] }}</label>
                                    </div>
                                </div>

                                
                            </div>
                        </div>
                        <div class="field required">
                            <label for="mail_on_review">{{ $CONFIG['MAIL_REVIEW'] }}</label>
                            <div class="inline fields">
                                <div class="field">
                                    <div class="ui radio checkbox">
                                        <input name="mail_on_review" @if($conference->mail_on_review == 1) {{ 'checked' }} @endif value="1" tabindex="0" class="hidden" type="radio">
                                        <label>{{ $CONFIG['Y'] }}</label>
                                    </div>
                                </div>
                                <div class="field">
                                    <div class="ui radio checkbox">
                                        <input name="mail_on_review" @if($conference->mail_on_review == 2) {{ 'checked' }} @endif value="2" tabindex="0" class="hidden" type="radio">
                                        <label>{{ $CONFIG['ADMIN'] }}</label>
                                    </div>
                                </div>
                                <div class="field">
                                    <div class="ui radio checkbox">
                                        <input name="mail_on_review" @if($conference->mail_on_review == 3) {{ 'checked' }} @endif value="3" tabindex="0" class="hidden" type="radio">
                                        <label>{{ $CONFIG['REV'] }}</label>
                                    </div>
                                </div>
                                <div class="field">
                                    <div class="ui radio checkbox">
                                        <input name="mail_on_review" @if($conference->mail_on_review == 4) {{ 'checked' }} @endif value="4" tabindex="0" class="hidden" type="radio">
                                        <label>{{ $CONFIG['N'] }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                  </div>
                  </div>
                    
                
                    
                    <div class="field text-center" style="margin-bottom:30px">
                        <button class="ui button primary" type="submit">{{ $CONFIG['BTN_EDIT'] }}</button>
                        <button class="ui button " type="reset">{{ $CONFIG['BTN_RESET'] }}</button>
                    </div>
    </form> 
    </div>

    @endsection