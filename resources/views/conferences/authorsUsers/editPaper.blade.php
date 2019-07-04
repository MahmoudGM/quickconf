@extends('layouts.app')
<?php
$lang = Session::get('lang');
$P = parse_ini_file(base_path('language/'.$lang.'/PAPERS.ini'));
?>
<title>{{ $P['SUBMIT'] }}</title>

@section('content')
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
        <h2 class="ui dividing header">{{ $P['SUBMIT'] }}</h2> @include('layouts.errors')
        <form class="ui form" method="POST" action="{{ route('conferences.papers.store', [$conference->confAcronym, $conference->confEdition] ) }}" enctype="multipart/form-data" >
            <div class="ui small form">

            <div class="panel">
                <div class="header">
                    <h3>Title and abstract</h3>
                </div>
                <div class="body">
                    <div class="field required">
                        <label for="title">{{ $P['TITLE'] }}</label>
                        <input name="title" id="title" required type="text" autofocus value="{{ $paper->title }}"> 
                    </div>

                    <div class="field required">
                        <label for="editor_create">{{ $P['ABSTRACT'] }}</label>
                        <textarea  name="abstract" id="editor_create" cols="30" rows="10" required>{{ $paper->abstract }}</textarea>
                        @ckeditor('editor_create',[
                            'height' => 300,
                            'removeButtons' => 'Source',
                            ])
                    </div>
                    <div class="field required">
                        <label for="input-tags">{{ $P['KEY'] }}</label>
                        <input name="keywords" id="input-tags" required type="text" value="{{ $paper->keywords }}"> 
                    </div>
                    
                </div>
            </div>
                
             {{ csrf_field() }}
                
                <div class="panel">
                    <div class="header">
                        <h3>{{ $P['H-AUTHORS'] }}</h3> </div>
                    <div class="body">
                        <div id="auths">
                        <?php $i=1 ?>
                            @foreach($authors as $author)
                            <div class="author">
                                <h4 class="ui dividing header">{{ $P['AUTHOR'] }} 1</h4> <span style="margin-bottom:10px" class="button ui primary mini" id="attribute">{{ $P['INSERT_C'] }}</span>
                                <br>
                                <div class="two fields">
                                    <div class="field required">
                                        <label>{{ $P['FIRST'] }}</label>
                                        <input value="{{ $author->first_name }}" id="main_first" name="first[]" type="text" required> 
                                    </div>
                                    <div class="field required">
                                        <label>{{ $P['LAST'] }}</label>
                                        <input value="{{ $author->last_name }}"  id="main_last" name="last[]" type="text" required> 
                                    </div>
                                </div>
                                <div class="three fields">
                                    <div class="field required">
                                        <label>{{ $P['AFF'] }}</label>
                                        <input value="{{ $author->affilation }}"  id="main_aff" name="aff[]" type="text" required> 
                                    </div>
                                    <div class="field required ">
                                        <label>{{ $P['EMAIL'] }}</label>
                                        <input value="{{ $author->email }}"  id="main_email" name="email[]" type="email" required>
                                    </div>
                                    <div class="field required ">
                                        <label>{{ $P['COUNTRY'] }}</label>
                                        <select required id="country{{$author->id}}" name ="country[]"></select>
                                    </div>
                                </div>
                                {{--
                                <input type="hidden" name="is_correspond[]" value="0" id="is_correspond1" value="0"> 
                                <div class="ui checkbox" style="margin-bottom:15px">
                                    <input id="1" name="correspond[]" tabindex="0" class="hidden corr" type="checkbox" value="1">
                                    <label>{{ $P['CORR_AUT'] }}</label>      
                                </div> --}}
                                <input type="hidden" class="is_corr"  name="is_correspond[]" value="0" id="is_correspond{{$i}}" value="0"> 
                                <div class="radio checkbox" style="margin-top:15px">
                                    <input id="{{$i}}" name="correspond[]" value="1" tabindex="0" class="hidden corr" type="radio" required>
                                    <label for="{{$i}}">{{ $P['CORR_AUT'] }}</label>
                                </div>
                            </div> @if (Session::get('errorAut1') !== null) <span class="help-block error">
                                <strong>{{ (string)Session::get('errorAut1') }}</strong>
                            </span> @endif @if (Session::get('errorAut2') !== null) <span class="help-block error">
                                <strong>{{ (string)Session::get('errorAut2') }}</strong>
                            </span> @endif 
                            @endforeach
                            </div> 
                            @if($conference->extended_submission_form == 'Y')
                        <!--<button class="add_field_button button ui green"><i class="add icon"></i>Add Authors</button> --><span class="button ui green" id="append">{{ $P['BTN_MORE'] }}</span> @endif </div>
                </div>

                <div class="panel">
                    <div class="header">
                        <h3>{{ $P['H-QU'] }}</h3> 
                    </div>
                    <div class="body">
                @foreach($questions as $question)
                <h5 class="ui block header">{{$question->question}}</h5>
                <div class="inline fields"> 
                    <input type="hidden" name="question[]" value="{{$question->id}}">
      
                    <div class="field">
                        <div class="ui radio checkbox">
                            <input name="choice{{$question->id}}" value="{{$question->id}}" tabindex="0" class="hidden" type="radio" required>
                            <label>{{$question->choice}}</label>
                        </div>
                    </div> 


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
                
                    @foreach($conference->topics as $topic)
                    <div class="ui checkbox" id="topic{{$topic->id}}">
                        <input @if($topic->paper_id == $paper->id  ) checked @endif name="topics[]" tabindex="0" class="hidden" type="checkbox" value="{{$topic->id}}">
                        <label>{{$topic->label}}</label>
                    </div> 
                    @endforeach
                </div>   
                @else
                <div class="field">
                    {{ $P['N-TOPIC'] }}
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
                            <?php $f = explode(',', $conference->file_type); ?>
                            <label>{{ $P['FILE'] }} {{$conference->file_type}}</label>
                            <div class="field">
                            @for ($i = 0; $i < count($f); $i++)
                                <div class="ui radio checkbox">
                                    <?php $t = str_replace("'", "" , $f[$i]); ?>
                                    <input name="file_type" value="{{$t}}" tabindex="0" class="hidden" type="radio" required>
                                    <label>{{$t}}</label>
                                </div>
                            @endfor
                            </div>
                            </div>
                            @if($lang == 'en')
                                <input class="dropify" data-height="75" id="file" name="file" type="file"  required> 
                            @elseif($lang == 'fr')
                                <input class="dropify-fr" data-height="75" id="file" name="file" type="file" required> 
                            @endif                                

                        
                    </div>
                </div>
                
                <div class="inline field required">
                            <center></center>
                            <label>{{ $P['CAPTCHA'] }}</label>
                </div>
                
                <input type="text" id="captcha" name="captcha" required>
                <div class="field text-center" style="margin:20px 0 40px 0">
                    <button class="ui button primary" type="submit">{{ $P['BTN_SUBMIT'] }}</button>
                    <button class="ui button " type="reset">{{ $P['BTN_RESET'] }}</button>
                </div>
            </div>
        </form>
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
            var i = {{$i}};
            $('#append').click(function () {
                i++;
                $('#auths').append('<div class="author" id="author' + i + '"><h4 class="ui dividing header">{{ $P['AUTHOR'] }} ' + i + '' 
                                + '<span id="remove" class="remove pull-right"><i class="icon remove"></i></span></h4>' 
                                + '<div class="two fields">' 
                                + ' <div class="field required">' 
                                + '<label>First name</label>' 
                                + '<input name="first[]" id="authors"   type="text">' 
                                + '</div>' + ' <div class="field required">' 
                                + '     <label>Last name</label>' 
                                + '     <input name="last[]" id="authors"   type="text">' 
                                + ' </div>' + '</div>' 
                                + '<div class="three fields">' 
                                + '<div class="field required">' 
                                + '<label>Affilation</label>' 
                                + '<input name="aff[]" id="authors"   type="text">' 
                                + '</div>' + '<div class="field required">' 
                                + '<label>Email</label>' 
                                + '<input name="email[]" id="authors"   type="email">' 
                                + '</div>' 
                                + '<div class="field ">'
                                +'        <label>{{ $P['COUNTRY'] }}</label>'
                                +'         <select required  id="countryy'+i+'" name ="country[]"></select>'
                                +'  </div>'
                                + '</div>' 
                                +'         <input type="hidden" class="is_corr" name="is_correspond[]" value="0" id="is_correspond'+i+'">         '                                
                                /*+ '<div  class="ui checkbox check" style="margin-bottom:15px">' 
                                + '        <input id="'+i+'" name="correspond[]" tabindex="0" class="hidden corr" type="checkbox" value="1">' 
                                + '        <label>{{ $P['CORR_AUT'] }}</label>' 
                                + '</div>'*/
                                +'<div class="radio checkbox check" style="margin-top:15px">'
                                +'    <input id="'+i+'" name="correspond[]" value="1" tabindex="0" class="hidden corr" type="radio" required>'
                                +'   <label for="'+i+'">{{ $P['CORR_AUT'] }}</label>'
                                +'</div> '
                                + '</div>');
                                populateCountries("countryy"+i);
            });

            @foreach($authors as $author)
                populateCountries("country"+{{$author->id}});
                $('#country'+{{$author->id}}).val('{{$author->country}}');
            @endforeach

            $('#auths').on("click", "#remove", function () {
                //console.log(this);
                $(this).parent().parent().remove();
                i--;
            });
            $('#attribute').click(function () {
                $('#main_first').val('{{ Auth::user()->first_name }}');
                $('#main_last').val('{{ Auth::user()->last_name }}');
                $('#main_aff').val('{{ Auth::user()->affilation }}');
                $('#main_email').val('{{ Auth::user()->email }}');
                $('#country').val('{{ Auth::user()->country }}');
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
        
    </script>

    @endpush
    @endsection