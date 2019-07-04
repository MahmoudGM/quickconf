@extends('layouts.app')
<?php
$lang = Session::get('lang');
$P = parse_ini_file(base_path('language/'.$lang.'/PAPERS.ini'));
?>
<title>{{ $P['SUBMIT'] }}</title>

@section('content')
<script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
<script src="{{ asset('js/ckeditor/config.js') }}"></script>
@push('style')
    <style>
        .author {
            background-color: #fff;
            padding: 14px;
            margin-bottom: 25px;
        }

        a.remove{
            background-color: #1d8eee !important
        }

        select > .placeholder {
            display: none;
        }

        .remove {
            cursor: pointer;
            width: 5px;
            color: #fff;
            padding-right: 19px;
            border-radius: 4px;
            padding-left: 2px;
            margin-top: 1px;
            height: 16px;
            background-color: red;
            line-height: .9;
        }






    </style>
    @endpush
    <div class="container ui">
        {!! Breadcrumbs::render('submitPaper',$conference) !!}
        @if( ($conference->is_submission_open == 'N')or( Carbon\Carbon::now()->format('Y-m-d') > $conference->submission_deadline) )
            <div class="ui negative message">
                <div class="header">
                   {{$P['NO_SUB']}}

                </div>
            </p></div>
        @else
        <h2 class="ui dividing header">{{ $P['SUBMIT'] }}</h2> @include('layouts.errors')
        <form class="ui form" method="POST" action="{{ route('conferences.papers.store', [$conference->confAcronym, $conference->confEdition] ) }}" enctype="multipart/form-data" >
            <div class="ui small form">

            <div class="panel">
                <div class="header">
                    <h3>{{$P['T-A']}}</h3>
                </div>
                <div class="body">
                    <div class="field required">
                        <label for="title">{{ $P['TITLE'] }}</label>
                        <input name="title" id="title" required type="text" autofocus>
                    </div>

                    <div class="field required">
                        <label for="abstract">{{ $P['ABSTRACT'] }}</label>
                        <textarea  name="abstract" id="abstract" cols="30" rows="10" required></textarea>
                        <script>
                            CKEDITOR.replace('abstract');
                        </script>
                    </div>
                    <div class="field required">
                        <label for="input-tags">{{ $P['KEY'] }}</label>
                        {{$P['KEY-INS']}}
                        <input name="keywords" id="input-tags" required type="text">
                    </div>

                </div>
            </div>

             {{ csrf_field() }}

                <div class="panel">
                    <div class="header">
                        <h3>{{ $P['H-AUTHORS'] }}</h3> </div>
                    <div class="body">
                        <div id="auths">
                            <div class="author">
                                <h4 class="ui dividing header">{{ $P['AUTHOR'] }} 1</h4> <span style="margin-bottom:10px" class="button ui primary mini" id="attribute">{{ $P['INSERT_C'] }}</span>
                                <br>
                                <div class="two fields">
                                    <div class="field required">
                                        <label>{{ $P['FIRST'] }}</label>
                                        <input class="first" id="main_first" name="first[]" type="text" required>
                                    </div>
                                    <div class="field required">
                                        <label>{{ $P['LAST'] }}</label>
                                        <input class="last" id="main_last" name="last[]" type="text" required>
                                    </div>
                                    <div class="field required">
                                        <label>{{ $P['AFF'] }}</label>
                                        <input class="aff"id="main_aff" name="aff[]" type="text" required>
                                    </div>
                                </div>
                                <div class="three fields">
                                    
                                    <div class="field required">
                                        <label for="grade">Grade</label>
                                        <select class="grade" name="grade[]"  required>
                                            <option class="placeholder" value="" disabled selected >Select</option>
                                            <option value="Teaching Assistant">Teaching Assistant</option>
                                            <option value="PhD Candidate">PhD Candidate</option>
                                            <option value="Dr.">Dr.</option>
                                            <option value="Master student">Master student</option>
                                            <option value="Assoc. Prof. Dr.">Assoc. Prof. Dr.</option>
                                            <option value="Prof.">Prof.</option>
                                            <option value="Professional">Professional</option>
                                        </select>
                                    </div>
                                    <div class="field required ">
                                        <label>{{ $P['EMAIL'] }}</label>
                                        <input class="email" id="main_email" name="email[]" type="email" required>
                                    </div>
                                    <div class="field required ">
                                        <label>{{ $P['COUNTRY'] }}</label>
                                        <select class="country" required id="country" name ="country[]"></select>
                                    </div>
                                </div>
                                <input type="hidden" class="is_corr"  name="is_correspond[]" value="0" id="is_correspond1" value="0">
                                <div class="radio checkbox" style="margin-top:15px">
                                    <input id="1" name="correspond[]" value="1" tabindex="0" class="hidden corr" type="radio" required>
                                    <label for="1">{{ $P['CORR_AUT'] }}</label>
                                </div>
                            </div> @if (Session::get('errorAut1') !== null) <span class="help-block error">
                                <strong>{{ (string)Session::get('errorAut1') }}</strong>
                            </span> @endif @if (Session::get('errorAut2') !== null) <span class="help-block error">
                                <strong>{{ (string)Session::get('errorAut2') }}</strong>
                            </span> @endif </div>
                            @if($conference->extended_submission_form == 'Y')
                        <span class="button ui green" id="append">{{ $P['BTN_MORE'] }}</span> @endif </div>
                </div>

                <div class="panel">
                    <div class="header">
                        <h3>{{ $P['H-QU'] }}</h3>
                    </div>
                    <div class="body">
                    @foreach ($questions as $question)
                        <div class="field required">
                         <label for="q{{$question->id}}">{{$question->question}}</label>
                            <select name="{{$question->id}}" id="q{{$question->id}}" class="ui fluid dropdown" required>
                                <option value="">Select</option>
                                @foreach ($pqchoice as $pq) 
                                    @if ($pq->paperquestion_id == $question->id)
                                        <option value="{{$pq->id}}">{{$pq->choice}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    @endforeach
                    </div>
                </div>

                <div class="panel">
                    <div class="header">
                        <h3>{{ $P['H-TOPIC'] }}</h3>
                    </div>
                    <div class="body">
                @if(count($conference->topics) != 0)
                <div class="inline field topics">
                <select id="choose" class="ui fluid dropdown search" multiple="" name="topics[]" required>
                        <option value="">{{$P['SELECT']}}</option>
                    @foreach($conference->topics as $topic)
                        <option value="{{$topic->id}}">{{$topic->label}}</option>
                    @endforeach 
                </select>
                </div>
                
                @endif
                </div>
                </div>

                <div class="panel">
                    <div class="header">
                        <h3>{{ $P['H-FILE'] }}</h3>
                    </div>
                    <div class="body">
                        <div class="field required">
                            <label>{{ $P['FILE'] }} {{$conference->file_type}}</label>
                           
                            </div>
                            @if($lang == 'en')
                                <input class="dropify" data-height="75" id="file" name="file" type="file"  required>
                            @elseif($lang == 'fr')
                                <input class="dropify-fr" data-height="75" id="file" name="file" type="file" required>
                            @endif


                    </div>
                </div>

                <div class="field required">
                            <img src="{!! Captcha::url() !!}" alt="">
                            <label>{{ $P['CAPTCHA'] }}</label>
                </div>

                <input type="text" id="captcha" name="captcha" required>
                <div class="field text-center" style="margin:20px 0 80px 0">
                    <button class="ui button primary" type="submit">{{ $P['BTN_SUBMIT'] }}</button>
                    <button id="reset" class="ui button " type="reset">{{ $P['BTN_RESET'] }}</button>
                </div>
            </div>
        </form>

        @endif
    </div>

    @push('script')

    <script src="{{ asset('js/plugin.js') }}"></script>
    <script src="{{ asset('js/selectize.js') }}"></script>
    <script src="{{ asset('js/dropify.min.js') }}"></script>



    <script>
        $(document).ready(function () {

            $('.dropify').dropify();

            $('.dropify-fr').dropify({
                messages: {
                    'default': 'Faites glisser et déposez un fichier ici ou cliquez',
                    'replace': 'Glisser-déposer ou cliquer pour remplacer',
                    'remove':  'Supprimer',
                    'error':   'Oups, quelque chose de mal s\'est passé.'
                }
            });

            var selected_option = $('#topic_select option:selected');
            $('checkbox').click(function () {
                $(this + 'input').addAttribute("checked");
            });
            //console.log(selected_option);
            var array = [];
            var topics = $('.topics div');
            //console.log(topics.length);
            for (var i = 0; i < topics.length; i++) {
                array.push(topics[i]);
            }
            //console.log(array);
            $('#topic_select').on('change', function () {
                $('#topic_select option').each(function () {
                    if (this.selected) {
                        //var topic = array[this.id];
                        $('#topic' + this.id).hide().siblings().show();
                        //$('.topics').append(topic);
                        //console.log(this.id);
                    }
                });
            });
            var i = 1;
            $('#append').click(function () {
                i++;
                $('#auths').append('<div class="author" id="author' + i + '"><h4 class="ui dividing header">{{ $P['AUTHOR'] }} ' + i + ''
                                + '<span id="remove" class="remove pull-right"><i class="icon remove"></i></span></h4>'
                                +'<span style="margin-bottom:10px" class="button ui primary mini" id="attribute">{{ $P['INSERT_C'] }}</span>'
                                + '<div class="two fields">'
                                + ' <div class="field required">'
                                + '<label>First name</label>'
                                + '<input class="first" name="first[]"    type="text">'
                                + '</div>' + ' <div class="field required">'
                                + '     <label>Last name</label>'
                                + '     <input  class="last" name="last[]"   type="text">'
                                + ' </div>' 
                                +'<div class="field required">'
                                + '<label>Affilation</label>'
                                + '<input  class="aff" name="aff[]"   type="text">'
                     
                                + '</div>' 
                                + '</div>'
                                + '<div class="three fields">'
                                + '<div class="field required">'
                                + '<label>Grade</label>'
                                + '<select  class="grade" name="grade[]" >'
                                +'   <option class="placeholder" value="" disabled selected >Select</option>'
                                +'  <option value="Teaching Assistant">Teaching Assistant</option>'
                                +'    <option value="PhD Candidate">PhD Candidate</option>'
                                +'    <option value="Dr.">Dr.</option>'
                                +'    <option value="Master student">Master student</option>'
                                +'    <option value="Assoc. Prof. Dr.">Assoc. Prof. Dr.</option>'
                                +'   <option value="Prof.">Prof.</option>'
                                +'    <option value="Professional">Professional</option>'
                                +'</select>'
                                + '</div>' 
                                + '<div class="field required">'
                                + '<label>Email</label>'
                                + '<input  class="email" name="email[]"   type="email">'
                                + '</div>'
                                + '<div class="field ">'
                                +'        <label>{{ $P['COUNTRY'] }}</label>'
                                +'         <select  class="country" required  id="country'+i+'" name ="country[]"></select>'
                                +'  </div>'
                                + '</div>'
                                +'         <input type="hidden" class="is_corr" name="is_correspond[]" value="0" id="is_correspond'+i+'">         '
                                +'<div class="radio checkbox check" style="margin-top:15px">'
                                +'    <input id="'+i+'" name="correspond[]" value="1" tabindex="0" class="hidden corr" type="radio" required>'
                                +'   <label for="'+i+'">{{ $P['CORR_AUT'] }}</label>'
                                +'</div> '
                                + '</div>');
                                populateCountries("country"+i);
            });
            $('#auths').on("click", "#remove", function () {
                //console.log(this);
                $(this).parent().parent().remove();
                i--;
            });
            $('#auths').on('click', '#attribute' , function () {
                $(this).siblings('div').find('.first').val('{{ Auth::user()->first_name }}');
                $(this).siblings('div').find('.last').val('{{ Auth::user()->last_name }}');
                $(this).siblings('div').find('.aff').val('{{ Auth::user()->affilation }}');
                $(this).siblings('div').find('.grade').val('{{ Auth::user()->grade }}');
                $(this).siblings('div').find('.email').val('{{ Auth::user()->email }}');
                $(this).siblings('div').find('.country').val('{{ Auth::user()->country }}');
            });
            $('#auths').on("click", ".check", function () {
                $(this).checkbox();
            });

            $('#auths').on("change", ".corr", function () {

                if($(this).is(':checked')){
                    console.log(this.id);
                    $('.is_corr').val('0');
                    $('#is_correspond'+this.id).val('1');

                }
                else{
                    $('#is_correspond'+this.id).val('0');
                }

            });

            //$('#input-tags').tokenfield();

            $('#input-tags').selectize({
                plugins: ['restore_on_backspace'],
                plugins: ['remove_button'],
                delimiter: ',',
                persist: false,
                create: function(input) {
                    return {
                        value: input,
                        text: input
                    }
                }
            });

            /*
            $('#input-tags').selectize({
                delimiter: ',',
                persist: false,
                create: function(input) {
                    return {
                        value: input,
                        text: input
                    }
                }
            });
*/
        });
    </script>

    <script>
    var scountry = '{{ $P['SELECT_COUNTRY'] }}';
    </script>
    
    <script src="{{ asset('js/countries.js') }}"></script>
    <script>
        populateCountries("country");

        

        $(document).ready(function() {

            $("#reset").click(function(){
                $('.search').dropdown('restore defaults');
            })
    
    });

    </script>

    @endpush
    @endsection
