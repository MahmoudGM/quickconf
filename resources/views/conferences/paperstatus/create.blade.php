@extends('layouts.app')
<?php
    $lang = Session::get('lang');
    $PS = parse_ini_file(base_path('language/'.$lang.'/P_STATUS.ini'));
?>

<title>{{ $PS['BTN_ADD']}}</title>
@section('content')
<script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
<script src="{{ asset('js/ckeditor/config.js') }}"></script>
@push('style')
<style>

    span.button.red {line-height: .8;}
    span i{
        margin-left: 4px !important
    }
    .choices input,.added input{
        margin:0 0 10px 0 !important;
    }

    select > .placeholder {
            display: none;
        }

</style>
@endpush

<div class="container ui">
{!! Breadcrumbs::render('crPs',$conference) !!}
<div class="header ui dividing">{{ $PS['BTN_ADD']}}</div>
@include('layouts.errors')

<div class="content">

<form class="ui form" method="POST" action="{{ route('conferences.paperstatus.store', [$conference->confAcronym, $conference->confEdition] ) }}">
  {{ csrf_field() }}
  <input type="hidden" name="conference_id" value="{{$conference->id}}">

<div id="pstatuses">

    <div class="two fields">
        <div class="field required eleven wide">
            <label for="label">{{ $PS['LABEL']}}</label>
            <input type="text" name="label" id="label" required>
        </div>
        <div class="field required three wide">
            <label for="camReady">{{ $PS['CAMREADY']}}</label>
            <select name="camReady" id="camReady" required>
                <option class="placeholder" value="" disabled selected >{{$PS['SELECT']}}</option>
                <option value="1">{{ $PS['Y']}}</option>
                <option value="0">{{ $PS['N']}}</option>
            </select>
        </div>

        <div class="field required three wide">
            <label for="camReady">{{ $PS['ACC']}}</label>
            <select name="accepted" id="accepted" required>
                <option class="placeholder" value="" disabled selected >{{$PS['SELECT']}}</option>
                <option value="1">{{ $PS['Y']}}</option>
                <option value="0">{{ $PS['N']}}</option>
            </select>
        </div>

    </div>

    <div class="field required ">
        <label for="msgTemplate">{{$PS['MSG']}}</label>
        <textarea required name="msgTemplate" id="msgTemplate" cols="30" rows="10"></textarea>
        <script>
            CKEDITOR.replace('msgTemplate');
        </script>
    </div>

</div>
    


</div>
    <div class="field text-center" style="margin-bottom:20px">
        <button class="ui button primary" type="submit">{{ $PS['BTN_SAVE']}}</button>
        <button class="ui button " type="reset">{{ $PS['BTN_RESET']}}</button>
    </div>
    </form>

</div>


</div>

@push('script')
<script>
$(document).ready(function(){
 $('#append').click(function(){

            $('#pstatuses').append( '<div class="two fields">'
                                +'<div class="field required eleven wide">'
                                +'<input type="text" name="label[]" id="label" required>'
                                +'</div>'
                                +'<div class="field required two wide">'
                                +'<select name="camReady[]" id="camReady" required>'
                                +'    <option class="placeholder" value="" disabled selected >{{$PS['SELECT']}}</option>'
                                +'    <option value="1">{{ $PS['Y']}}</option>'
                                +'    <option value="0">{{ $PS['N']}}</option>'
                                +'</select>'
                                +'</div>'
                                +'<div class="field required two wide">'
                                +'<select name="accepted[]" id="accepted" required>'
                                +'    <option class="placeholder" value="" disabled selected >{{$PS['SELECT']}}</option>'
                                +'    <option value="1">{{ $PS['Y']}}</option>'
                                +'    <option value="0">{{ $PS['N']}}</option>'
                                +'</select>'
                                +'</div>'
                                +'<div class=" one wide field">'
                                +'<span id="remove" class="button ui red"> <i class="icon delete"></i></span>'
                                +'</div>'
                                +'</div>'


                );
     });

     $('#pstatuses').on("click", "#remove" , function(){
              //console.log(this);
                  $(this).parent().parent().remove();
          });

});

</script>
@endpush
@endsection