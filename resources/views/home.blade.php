@extends('layouts.app')

<?php
$lang = Session::get('lang');
$H = parse_ini_file(base_path('language/'.$lang.'/HOME.ini'));
?>

<title>{{$H['TITLE']}}</title>
@section('content')

@push('style')
<style>
    .menu .item{
        cursor:pointer !important;
    }

    .tab{
        padding-top: 15px !important
    }

</style>
@endpush
<div class="container ui">

  {!! Breadcrumbs::render('home') !!}

  @include('layouts.errors')

    @if( (count($requestChair) != 0) or (count($requestRev) != 0) )
        <h3 class="ui dividing header">
            {{$H['REQS']}}
        </h3>
        @if(count($requestChair) != 0)
            <h4 class="ui dividing header">
                {{$H['REQS_CH']}}
            </h4>
            @foreach($requestChair as $req)
             <div class="ui icon message">
                <i class="inbox icon"></i>
                    <div class="content">
                        <div class="header">
                            {{ $req->confName }} <br>
                            <a href="">{{$H['S_DETAILS']}}</a>
                            <form style="display:inline" class="ui form pull-right " action="{{route('conferences.comite.accept',[$req->confAcronym,$req->confEdition,$req->role])}}" method="post">
                                {{csrf_field()}}
                                <button type="submit" class="button ui green">{{$H['ACCEPT']}}</button>
                            </form>
                            <form style="display:inline" class="ui form pull-right" action="{{route('conferences.comite.decline',[$req->confAcronym,$req->confEdition,$req->role])}}" method="post">
                                {{csrf_field()}}
                                <button type="submit"  class="button ui red">{{$H['DECLINE']}}</button>
                            </form>
                        </div>
                    </div>
            </div>
            @endforeach
        @endif

        @if(count($requestRev) != 0)
            <h4 class="ui dividing header">
                {{$H['REQS_REV']}}
            </h4>
            @foreach($requestRev as $req)
            <div class="ui icon message">
                <i class="inbox icon"></i>
                    <div class="content">
                        <div class="header">
                            {{ $req->confName }} <br>
                            <a href="">{{$H['S_DETAILS']}}</a>
                            <form style="display:inline" class="ui form pull-right" action="{{route('conferences.comite.accept',[$req->confAcronym,$req->confEdition,$req->role])}}" method="post">
                                {{csrf_field()}}
                                <button type="submit" class="button ui green">{{$H['ACCEPT']}}</button>
                            </form>
                            <form style="display:inline" class="ui form pull-right" action="{{route('conferences.comite.decline',[$req->confAcronym,$req->confEdition,$req->role])}}" method="post">
                                {{csrf_field()}}
                                <button type="submit"  class="button ui red">{{$H['DECLINE']}}</button>
                            </form>
                        </div>
                    </div>
            </div>
            @endforeach
        @endif

    @endif

    <div class="ui pointing secondary menu">
        <div class="item active " data-tab="tab-name">{{$H['ADMIN']}}</div>
        @if(count($confsAuthor) != 0)
            <div class="item" data-tab="tab-name2">{{$H['AUTH']}}</div>
        @endif
            @if(count($confsChair) != 0)
            <div class="item" data-tab="tab-name3">{{$H['CHAIR']}}</div>
        @endif
        @if(count($confsRev) != 0)
            <div class="item" data-tab="tab-name4">{{$H['REV']}}</div>
        @endif
    </div>

    <div class="ui tab active" data-tab="tab-name">

    @if (count($confsAdmin) == 0)
    <div class="ui message">
    <div class="header">
        {{ $H['WELCOME'] }}
    </div>
        <p>{{ $H['WELCOME_TXT'] }}</p>

                    <a href="{{ route('conferences.create' ) }}">
                        <div class="ui floated small primary labeled icon button">
                        <i class="icon add square"></i> {{$H['BTN_CREATE']}}
                        </div>
                    </a>
            </div>
    @else

    <h3 class="ui dividing header">
        {{$H['LIST']}} {{$H['AS']}} {{$H['ADMIN']}}
    </h3>
    <table id="confTab" class="ui celled table">
        <thead>
            <tr>
            <th>{{$H['NAME']}}</th>
            <th>{{$H['ACRONYM']}}</th>
            <th>{{$H['EDITION']}}</th>
            <th>{{$H['START_DATE']}}</th>
            <th>{{$H['END_DATE']}}</th>
            <th>{{$H['ACTIONS']}}</th>
            </tr>
        </thead>
        <tbody>
        @foreach($confsAdmin as $c)
            @if($c->is_activated == 0)
                <tr>
                    <td>{{$c->confName}}</td>
                    <td>{{$c->confAcronym}}</td>
                    <td>{{$c->confEdition}}</td>
                    <td colspan='3'>{{$H['WAIT']}}</td>
                </tr>

            @elseif($c->is_deleted == 1)
                <tr>
                    <td>{{$c->confName}}</td>
                    <td>{{$c->confAcronym}}</td>
                    <td>{{$c->confEdition}}</td>
                    <td colspan='3'>{{$H['DELETE']}}</td>
                </tr>
            @else
            <tr>
                <td><a href="{{ route('conferences.show', [$c->confAcronym, $c->confEdition] ) }}">{{$c->confName}}</a></td>
                <td>{{$c->confAcronym}}</td>
                <td class="positive">
                    {{$c->confEdition}}
                </td>
                <td>
                    {{$c->start_date}}
                </td>
                <td>
                    {{$c->end_date}}
                </td>
                <td>
                    <button id="delete" onclick="submitForm('formDeleteConf','/conferences/{{$c->id}}/delete')" name="{{$c->id}}" class="button ui red">{{$H['BTN_DELETE']}}</button>



                    <button id="create" onclick="submitForm('createForm','/conferences/create/{{$c->id}}/')" class="button ui teal" name="{{$c->id}}" >{{$H['BTN_CREATE_FROM']}}</button>

                </td>
            </tr>
            @endif

        @endforeach
        </tbody>
            <tfoot class="full-width">
                <tr>

                <th colspan="6">
                    <a href="{{ route('conferences.create' ) }}">
                        <div class="ui right floated small primary labeled icon button">
                        <i class="icon add square"></i> {{$H['BTN_CREATE']}}
                        </div>
                    </a>

                </th>
                </tr>
            </tfoot>
        </table>

        <div id="modal-create" class="ui modal">
            <i class="close icon"></i>
            <div class="header">
              <th>{{$H['CREATE_NEW']}}</th>
            </div>
            <div class="content">

                <form class="form ui" id="createForm" style="display:inline-block" method="post" action="">
                  <div class="description">
                    {{ csrf_field() }}

                    <div class="fields">
                      <div class="field required">
                      <label for="start_date">{{$H['START_DATE']}}</label>
                          <div class="ui calendar" id="date_start">
                            <div class="ui input left icon">
                              <i class="calendar icon"></i>
                              <input name="start_date" id="start_date" type="text" required>
                            </div>
                          </div>
                      </div>

                      <div class="field required">
                      <label for="end_date">{{$H['END_DATE']}}</label>
                          <div class="ui calendar" id="date_end">
                            <div class="ui input left icon">
                              <i class="calendar icon"></i>
                              <input name="end_date" id="end_date" type="text" required>
                            </div>
                          </div>
                      </div>
                    </div>

                    <div class="fields">

                        <div class="field required">
                        <label for="submission_deadline">{{$H['SUBM_DEAD']}}</label>
                            <div class="ui calendar" id="deadline_sub">
                              <div class="ui input left icon">
                                <i class="calendar icon"></i>
                                <input name="submission_deadline" id="submission_deadline" type="text" required>
                              </div>
                            </div>
                        </div>

                        <div class="field required">
                        <label for="review_deadline">{{$H['REV_DEAD']}}</label>
                            <div class="ui calendar" id="deadline_rev">
                              <div class="ui input left icon">
                                <i class="calendar icon"></i>
                                <input name="review_deadline" id="review_deadline" type="text" required>
                              </div>
                            </div>
                        </div>

                        <div class="field required">
                        <label for="cam_ready_deadline">{{$H['CAM_DEAD']}}</label>
                            <div class="ui calendar" id="deadline_cam">
                              <div class="ui input left icon">
                                <i class="calendar icon"></i>
                                <input name="cam_ready_deadline" id="cam_ready_deadline" type="text" required>
                              </div>
                            </div>
                        </div>
                        </div>

                        <br>
                            <strong>Choose data that will be exported to the new conference:</strong><br><br>
                            <div class="inline fields">
                                <div class="field">
                                    <div class="ui checkbox">
                                        <input type="checkbox" tabindex="0" class="hidden">
                                        <label>Committe</label>
                                    </div>
                                </div>
                                <div class="field">
                                    <div class="ui checkbox">
                                        <input type="checkbox" tabindex="0" class="hidden">
                                        <label>Messages templates</label>
                                    </div>
                                </div>
                                <div class="field">
                                    <div class="ui checkbox">
                                        <input type="checkbox" tabindex="0" class="hidden">
                                        <label>Topics</label>
                                    </div>
                                </div>
                                </div>
                                <div class="inline fields">
                                <div class="field">
                                    <div class="ui checkbox">
                                        <input type="checkbox" tabindex="0" class="hidden">
                                        <label>Paper questions</label>
                                    </div>
                                </div>
                                <div class="field">
                                    <div class="ui checkbox">
                                        <input type="checkbox" tabindex="0" class="hidden">
                                        <label>Review questions</label>
                                    </div>
                                </div>
                                <div class="field">
                                    <div class="ui checkbox">
                                        <input type="checkbox" tabindex="0" class="hidden">
                                        <label>Criterias</label>
                                    </div>
                                </div>
                                <div class="field">
                                    <div class="ui checkbox">
                                        <input type="checkbox" tabindex="0" class="hidden">
                                        <label>Paper status</label>
                                    </div>
                                </div>
                        </div>
                    <button id="submit-form" style="display:none" type="submit" class="button ui primary" >{{$H['BTN_CREATE_FROM']}}</button>
                    </div>
                </form>

            </div>
            <div class="actions">
              <div class="ui black deny button">
                {{$H['CANCEL_BTN']}}
              </div>
              <div class="ui green right icon button" id="submitCreateForm">
                {{$H['CR_BTN']}}
                <i class="checkmark icon"></i>
              </div>
            </div>
          </div>

         <div id="modal" class="ui delete modal">
                     <input class="type" type="hidden">
                    <div class="ui icon header">
                        <i class="trash icon"></i>
                        {{$H['CONFIRM_TITLE']}}
                    </div>
                    <div class="content">
                        <p>{{$H['CONFIRM']}}</p>
                    </div>
                    <div class="actions">
                        <div class="ui red cancel button">
                        <i class="remove icon"></i>
                        {{$H['BTN_NO']}}
                        </div>
                        <form id="formDeleteConf" style="display:inline-block" method="post" action="">
                                {{ csrf_field() }}
                            <button type="submit" id="create" class="ui green ok  button" ><i class="remove icon"></i>{{$H['BTN_YES']}}</button>
                        </form>
                    </div>
                </div>


        @endif
</div>

    @if(count($confsAuthor) != 0)
    <div class="ui tab" data-tab="tab-name2">
        <h3 class="ui dividing header">
    {{$H['LIST']}} {{$H['AS']}} {{$H['AUTH']}}
    </h3>
    <table id="confTab" class="ui celled table">
        <thead>
            <tr>
            <th>{{$H['NAME']}}</th>
            <th>{{$H['ACRONYM']}}</th>
            <th>{{$H['EDITION']}}</th>
            <th>{{$H['START_DATE']}}</th>
            <th>{{$H['END_DATE']}}</th>
            </tr>
        </thead>
        <tbody>
        @foreach($confsAuthor as $cAut)

            <tr>
                <td><a href="{{ route('conferences.authors.mypapers', [$cAut->confAcronym, $cAut->confEdition] ) }}">{{$cAut->confName}}</a></td>
                <td>{{$cAut->confAcronym}}</td>
                <td class="positive">
                    {{$cAut->confEdition}}
                </td>
                <td>
                    {{$cAut->start_date}}
                </td>
                <td>
                    {{$cAut->end_date}}
                </td>

            </tr>

        @endforeach
        </table>
 </div>
 @endif

  @if(count($confsChair) != 0)
    <div class="ui tab" data-tab="tab-name3">
        <h3 class="ui dividing header">
    {{$H['LIST']}} {{$H['AS']}} {{$H['CHAIR']}}
    </h3>
    <table id="confTab" class="ui celled table">
        <thead>
            <tr>
            <th>{{$H['NAME']}}</th>
            <th>{{$H['ACRONYM']}}</th>
            <th>{{$H['EDITION']}}</th>
            <th>{{$H['START_DATE']}}</th>
            <th>{{$H['END_DATE']}}</th>
            </tr>
        </thead>
        <tbody>
        @foreach($confsChair as $c)

            <tr>
                <td><a href="{{ route('conferences.papers.index', [$c->confAcronym, $c->confEdition] ) }}">{{$c->confName}}</a></td>
                <td>{{$c->confAcronym}}</td>
                <td class="positive">
                    {{$c->confEdition}}
                </td>
                <td>
                    {{$c->start_date}}
                </td>
                <td>
                    {{$c->end_date}}
                </td>

            </tr>

        @endforeach
        </table>
 </div>
 @endif

 @if( count($confsRev) != 0)
    <div class="ui tab" data-tab="tab-name4">
        <h3 class="ui dividing header">
    {{$H['LIST']}} {{$H['AS']}} {{$H['REV']}}
    </h3>
    
    <table id="confTab" class="ui celled table">
        <thead>
            <tr>
            <th>{{$H['NAME']}}</th>
            <th>{{$H['ACRONYM']}}</th>
            <th>{{$H['EDITION']}}</th>
            <th>{{$H['START_DATE']}}</th>
            <th>{{$H['END_DATE']}}</th>
            <th>{{$H['ACTIONS']}}</th>
            </tr>
        </thead>
        <tbody>
        @foreach($confsRev as $c)

            <tr>
                <td><a href="{{ route('conferences.comite.mypapers', [$c->confAcronym, $c->confEdition] ) }}">{{$c->confName}}</a></td>
                <td>{{$c->confAcronym}}</td>
                <td class="positive">
                    {{$c->confEdition}}
                </td>
                <td>
                    {{$c->start_date}}
                </td>
                <td>
                    {{$c->end_date}}
                </td>

                <td>
                    @if($topics == 0)
                        <a href="{{route('conferences.comite.choose',[$c->confAcronym,$c->confEdition])}}" class="button ui primary">{{$H['CHOOSE']}}</a>
                    @else
                        <a href="{{route('conferences.comite.choose',[$c->confAcronym,$c->confEdition])}}" class="button ui primary">{{$H['EDIT_CHOOSE']}}</a>
                    @endif
                </td>

            </tr>

        @endforeach
        </table>
 </div>
 @endif



</div>


@push('script')
<script>
function submitForm(formId,action)
{
    document.getElementById(formId).action = action;
    
}


$(document).ready(function() {
    $('.pointing.menu .item').tab();

    $('#submitCreateForm').click(function() {
      $('#submit-form').click();
      
      document.getElementById('createForm').submit();
    });



});
</script>
@endpush
@endsection
