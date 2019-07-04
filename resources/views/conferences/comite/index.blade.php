@extends('layouts.app')

<?php
$lang = Session::get('lang');
$C = parse_ini_file(base_path('language/'.$lang.'/COMITE.ini'));
?>

<title>{{$C['TITLE']}}</title>
@section('content')
<script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
<script src="{{ asset('js/ckeditor/config.js') }}"></script>

@push('style')
<style>
    #datatable td, #datatable th  {
        text-align: center
    }

    input[type="search"]{
        margin-bottom:20px !important
    }
    tfoot{
        display: table-header-group;
    }
    select > .placeholder {
            display: none;
        }
    .filters{
        background-color: #f7f7f7;
        margin-bottom: 22px;
        padding: 14px;
        height: 50px;
        border-radius:5px;
        border: 1px solid #ddd
    }

    tfoot{
        display: table-header-group;
        position:absolute;
        top:-110px;
        left:105px;
    }
    tfoot th{
        border:none !important;
        background-color: transparent !important;
    }

    .mymodal{
        position:absolute;
        top:10px;
        left: 10px;
        background-color:#fff;
        width:85%;
        z-index:25
    }


    .ui.hidden.transition {
        display: none !important;
        }

    .dt-buttons{
        text-align:center !important;
        border-bottom:2px solid #ddd;
        padding-bottom:20px;
        margin-bottom:20px;
        float:none !important
    }

    .btn:hover{
        border:none !important;
        background-color:#eee !important;
        color:#555 !important
    }

    .btn{
        text-align:center !important;
        font-weight: bold !important;
        margin-left: 3px !important;
        width:24% !important;

    }
    .buttons{
        text-align:center;
        margin-bottom:-20px
    }
    .buttons button{
        width:32.2%;
        
    }

    .buttons button:hover{
        background-image: linear-gradient(to bottom, #fff 0%, #e9e9e9 100%) !important;
    }

    
  

</style>
@endpush
<div class="container ui">
{!! Breadcrumbs::render('comite',$conference) !!}
  @include('layouts.errors')
<h3 class="ui dividing header">{{$C['SEND']}}
    <span>
        <i class="icon add square green add-invitation" style="font-size:20px;margin-bottom:4px;cursor:pointer"></i>
        <i class="icon minus square green min-invitation" style="font-size:20px;margin-bottom:4px;cursor:pointer;display:none"></i>
    </span>
    
</h3>

<form id="invit-form" style="display:none" action="{{route('conferences.comite.send',[$conference->confAcronym,$conference->confEdition])}}" method="POST" class="form ui">
    {{ csrf_field() }}
    <div class="two fields">
        <div class="wide six field required">
            <label for="email">{{$C['EMAIL']}}</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="wide two field required">
            <label for="role">{{$C['ROLE']}}</label>
            <select class="" id="role" name="role" required>
                <option class="placeholder" value="" disabled selected >{{$C['SELECT']}}</option>
                <option value="C">{{$C['CHAIR']}}</option>
                <option value="R">{{$C['REV']}}</option>
            </select>
        </div>
         <div class="wide one field">
           <button type="submit" style="margin-top:23px" class="ui teal button">{{$C['BTN_SEND']}}</button>
        </div>
    </div>

</form>

<h3 class="ui dividing header">{{$C['ADD_MAN']}} 
    <span>
        <i class="icon add square green add-manuel" style="font-size:20px;margin-bottom:4px;cursor:pointer"></i>
        <i class="icon minus square green min-manuel" style="font-size:20px;margin-bottom:4px;cursor:pointer;display:none"></i>
    </span>
</h3>

<form id="manuel-form" style="display:none" action="{{route('conferences.comite.add',[$conference->confAcronym,$conference->confEdition])}}" method="POST" class="form ui">
    {{ csrf_field() }}

    <div class="three fields">
        <div class="field required">
            <label for="first">{{ $C['FIRST'] }}</label>
            <input id="first" name="first" type="text" required>
        </div>
        <div class="field required">
            <label for="last">{{ $C['LAST'] }}</label>
            <input id="last" name="last" type="text" required>
        </div>
        <div class="field required ">
                <label for="email">{{ $C['EMAIL'] }}</label>
                <input id="email" name="email" type="email" required>
        </div>
    </div>
        <div class="three fields">
            <div class="field required">
                <label for="aff">{{ $C['AFF'] }}</label>
                <input id="aff" name="aff" type="text" required>
            </div>

            <div class="field required">
                
                <label for="grade">{{ $C['GRADE'] }}</label>
                <select name="grade" id="grade" required>
                    <option class="placeholder" value="" disabled selected >{{$C['SELECT']}}</option>
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
                <label for="country">{{ $C['COUNTRY'] }}</label>
                <select required id="country" name ="country"></select>
            </div>
        </div>


    <div class="two fields">


        <div class="field required">
            <label for="role">{{$C['ROLE']}}</label>
            <select name="role" required id="topic_select">
                <option class="placeholder" value="" disabled selected >{{$C['SELECT']}}</option>
                <option value="C">{{$C['CHAIR']}}</option>
                <option value="R">{{$C['REV']}}</option>
            </select>
          </div>
            <div class="field" id="topics">

            </div>
    </div>
        <div class="field text-center" style="margin:20px 0 40px 0">
            <button class="ui button primary" type="submit">{{ $C['BTN_ADD'] }}</button>
            <button id="reset" class="ui button " type="reset">{{ $C['BTN_RESET'] }}</button>
        </div>

</form>




<h3 class="ui dividing header">{{$C['LIST']}} </h3>
    <div class="filters">
                  
    </div>
    <div class="buttons">
        <button class="button gray ui show-modall tiny" style="margin-bottom:30px"></i>Send mails</button>
        <button class="button gray ui  tiny" style="margin-bottom:30px"></i>Download excel</button>
        <button class="button gray ui resetFilter tiny" style="margin-bottom:30px"></i>Reset filter</button>
    </div>
    <table id="datatable" class="ui selectable table display paperTab" cellspacing="0" width="100%">

    <tfoot>
        <th></th>
        <th></th>
        <th></th>
        <th>{{$C['ROLE']}}</th>
        <th>{{$C['COUNTRY']}}</th>
        <th>{{$C['AFF']}}</th>
        <th>
                <select id="engines" >
                    <option  value="" >Filter by grade</option>
                    <option value="Teaching Assistant">Teaching Assistant</option>
                    <option value="PhD Candidate">PhD Candidate</option>
                    <option value="Dr.">Dr.</option>
                    <option value="Master student">Master student</option>
                    <option value="Assoc. Prof. Dr.">Assoc. Prof. Dr.</option>
                    <option value="Prof.">Prof.</option>
                    <option value="Professional">Professional</option>
                </select>
        </th>
        <th></th>
        <th></th>

    </tfoot>
    <thead>

        <tr>
            <th>{{$C['FIRST']}}</th>
            <th>{{$C['LAST']}}</th>
            <th>{{$C['EMAIL']}}</th>
            <th class="select-filter">{{$C['ROLE']}}</th>
            <th class="select-filter">{{$C['COUNTRY']}}</th>
            <th class="select-filter">{{$C['AFF']}}</th>
            <th>{{$C['GRADE']}}</th>
            <th>topics</th>
            <th>nbr_papers</th>
            <th>{{$C['ACTIONS']}}</th>
        </tr>
    </thead>


    </table>

    
    <div class="ui modal"  >
    <i class="close icon"></i>
    <div class="header">
        Send emails
    </div>

    <div class="content" style="padding-bottom:20px;height:450px;overflow: scroll;overflow-x: hidden;">
        
    <form class="ui form" id="formSend" method="get" action="/conferences/{{$conference->confAcronym}}/{{$conference->confEdition}}/send/mail">
        <div class="form ui">
        <div class="field">
        <input type="hidden" id="emails" name="emails">
            <label for="model">model</label>
            <select name="selectMsg" id="selectMsg">
                <option class="placeholder" value="" disabled selected >{{$C['SELECT']}}</option>
                <option value="free">Free message</option>
                @foreach($conference->messages as $msg)
                    <option value="{{$msg->id}}">{{$msg->name}}</option>
                @endforeach
            </select>
        </div>
        <div class="body"></div>
        </div>
    </form>
       
    </div>

    <div class="actions">
        <div class="ui button deny">Cancel</div>
        <button class="button ui green" onclick="event.preventDefault();document.getElementById('formSend').submit();">Send</button>
    </div>
    </div>





</div>

@push('script')
<script>var scountry = '{{ $C['SELECT_COUNTRY'] }}';</script>
<script src="{{ asset('js/countries.js') }}"></script>
<script class="init">

populateCountries("country");

 $(document).ready(function() {

   if($('#topic_select option').val() == 'C'){
     $('#topic_select option').remove();
   }





   $('#topic_select').on('change', function () {
       $('#topic_select option').each(function () {
           if (this.selected) {
               $('#topic' + this.id).hide().siblings().show();
               console.log(this.value);
               if(this.value == 'C'){
                 $('#topics #topic').remove();
               }
               else{

                 /*$('#topics').append('<div id="topic">'
                                    +'<label for="">Topics</label><br><br>'
                                    +'@foreach($conference->topics as $topic)'
                                    +'<div class="ui checkbox  check" >'
                                    +'<input id="tp{{$topic->id}}" name="topics[]" tabindex="0" class="hidden" type="checkbox" value="{{$topic->id}}">'
                                    +'<label for="tp{{$topic->id}}">{{$topic->label}}</label>'
                                    +'</div>'
                                    +'@endforeach'
                                    +'</div>');*/
                  $('#topics').append('<div id="topic">'
                                    +'<div class="field">'
                                    +'<label>{{$C['TOPICS']}}</label>'
                                    +'<select multiple="" class="ui fluid dropdown tps search" name="topics[]">'
                                    +'@foreach($conference->topics as $topic)'
                                    +    '<option value="{{$topic->id}}">{{$topic->label}}</option>'
                                    +  '@endforeach'
                                    +'</select>'
                                    +'</div>'
                                    +'</div>');

          
                                       $('#topics .tps').dropdown({
                                            allowCategorySelection: true,
                                            forceSelection:false,
                                        });



               }
           }
       });
   });




var lang = "{{ $lang }}"
if( lang === "fr")
    var url = "http://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/French.json";
else
    var url = "http://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/English.json";
var  dt = $('#datatable').DataTable( {

            initComplete: function () {
            var i=2;
            this.api().columns('.select-filter').every( function () {
                var column = this;
                var columns = this.settings().init().columns;
                i++;
                var select = $('<select><option value="">Filter by '+columns[i].data+'</option></select>')
                    .appendTo( $(column.footer()).empty() )
                    .on( 'change', function () {
                        var val = $.fn.dataTable.util.escapeRegex(
                            $(this).val()
                        );

                        column
                            .search( val ? '^'+val+'$' : '', true, false )
                            .draw();
                    } );

                column.data().unique().sort().each( function ( d, j ) {
                    select.append( '<option value="'+d+'">'+d+'</option>' )
                } );
            } );
        },


            dom: 'Bfrtip',

            columnDefs: [
                {
                    targets: 1,
                    className: 'noVis'
                }
            ],
            lengthMenu: [
                [ 10, 25, 50, -1 ],
                [ '10 rows', '25 rows', '50 rows', 'Show all' ]
            ],
            buttons: [
                {
                    extend:'copy',
                    className: 'button ui btn gray tiny' ,
                },
                {
                    extend:'pageLength',
                    className: 'button ui btn gray tiny',
                },
                {
                    extend:'print',
                    className: 'button ui btn gray tiny',
                },
                {
                    extend: 'colvis',
                    className: 'button ui btn gray tiny',
                    columns: ':not(.noVis)'
                }
            ],
            language: {
                "url": url
            },
            processing: true,
            serverSide: true,
            ajax: '{{ route('conferences.comite.indexall', [$conference->confAcronym, $conference->confEdition] ) }}',


            columns: [


                //{data: 'id', name: 'id'},
                { "data": "first_name" },
                { "data": "last_name" },
                { "data": "email"},
                { "data": "role" },
                { "data": "country" },
                { "data": "affilation"},
                { "data": "grade"},
                { "data": "topics"},
                { "data": "nbr_papers"},
                {data: 'action', name: 'action', orderable: false, searchable: false},

            ],
            order: [[0, 'desc']]

    } );

    $("#engines").change(function(){         
        dt
            .columns(6)
            .search(this.value)
            .draw();
    });

 


    $('.show-modall').click(function () {
        var arrayId = [];
        //console.log(dt.data().count());
        $('#datatable tbody tr td .mailInput').each(function(){
            arrayId.push(this.value);
        });
        //console.log(arrayId);
        var arrC="";
        for(i=0;i<arrayId.length;i++ ){
            if(i != arrayId.length-1)
                arrC=arrC+arrayId[i]+';';
            else
                arrC=arrC+arrayId[i];
        }
        console.log(arrayId.length);
        $('#emails').val(arrC)
        if(arrayId.length!=0){
            $('.ui.modal').modal('show');
        }else{
            alert('No matching records found');
        }
    });




    $('.paperTab').on("submit", "#formDeletePaper" , function(){
             return confirm("{{$C['CONFIRM']}}");
        });

    $('.add-invitation').click(function(){
        $(this).hide();
        $('.min-invitation').show();
        $('#invit-form').fadeIn(200);
    });

    $('.min-invitation').click(function(){
        $(this).hide();
        $('.add-invitation').show();
        $('#invit-form').fadeOut(200);
    });

    $('.add-manuel').click(function(){
        $(this).hide();
        $('.min-manuel').show();
        $('#manuel-form').fadeIn(200);
    });

    $('.min-manuel').click(function(){
        $(this).hide();
        $('.add-manuel').show();
        $('#manuel-form').fadeOut(200);
    });

    $("#reset").click(function(){
        $('.tps').dropdown('restore defaults');
    });

    $('#selectMsg').change(function() {
        var data = "";
        console.log(this.value);
        var id=this.value;

        if(id!='free'){

        $.ajax({
            //dataType:"json",
            type:"GET",
            url : '/conferences/{{$conference->confAcronym}}/{{$conference->confEdition}}/messages/'+id+'/showbody',
            async: true,
            success : function(response) {
                var data = response;
                $('.body').empty();
                $('.body').append('<fieldset>'
                                   + '<legend>'+data.title+'</legend>'
                                   +data.body
                                   +'</fieldset>');
                return response;
            },
            error: function() {
                alert('Error occured');
            }

                
        });
        }else{
            $('.body').empty();
            $('.body').append('<div class="ui message">'
                            +'To be able to integrate the data in the messages sent by email please use the names of the field of the database as follows: <br> <br>'
                            +'{$first_name}: First name <br>'
                            +'{$last_name}: Last name <br>'
                            +'</div>'
                            +'<div class="field required">'
                            +'<label for="subject">Subject</label>'
                            +'<input type="text" name="subject" id="subject">'
                            +'</div>'
                            +'<div class="field required">'
                            +'<textarea id="bodyField" name="body">'
                            +'</textarea>'
                            +'</div>');
            CKEDITOR.replace('bodyField');
        }
        

    });

    $('.resetFilter').click(function(){
            $('#datatable select').val('');
            var table = $('#datatable').dataTable();
            table.fnFilterClear();

        });
        

 } );

</script>

@endpush
@endsection
