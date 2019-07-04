@extends('layouts.app')
<?php
$lang = Session::get('lang');
$P = parse_ini_file(base_path('language/'.$lang.'/PAPERS.ini'));
?>

<title>{{$P['LIST']}}</title>
@section('content')
<script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
<script src="{{ asset('js/ckeditor/config.js') }}"></script>
@push('style')
<style>
    #datatable td, #datatable th  {
        text-align: center
    }

    input[type="search"]{
        margin-bottom:20px !important;
    }

    .filters{
        background-color: #f7f7f7;
        margin-bottom: 22px;
        padding: 14px;
        border-radius:5px;
        border: 1px solid #ddd;
        text-align:center
    }

    .filters select{
        border-radius:5px;
        margin:7px 7px 7px 7px;
        background-color: #fff;

    }

    tfoot{
        display: table-header-group;
        position:absolute;
        top:-110px;
        left:-34px;
    }
    tfoot th{
        border:none !important;
        background-color: transparent !important;
    }
    select > .placeholder {
            display: none;
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
        width:24%;
        
    }

    .buttons button:hover{
        background-image: linear-gradient(to bottom, #fff 0%, #e9e9e9 100%) !important;
    }

</style>
@endpush
<div class="container ui">
  {!! Breadcrumbs::render('sub_papers',$conference) !!}
<h2 class="ui dividing header">{{$P['LIST']}} <a class="ui pull-right button teal" style="margin-top:-10px" href="{{ route('conferences.papers.create', [$conference->confAcronym, $conference->confEdition] ) }}">{{$P['SUBMIT']}}</a></h2>
    <div class="filters">

        <span class="tt"></span>

        <select id="filterCr" >
            <option value>Filter By Cam Ready</option>
            <option value="3">camReady uploaded</option>
            <option value="2">camReady not uploaded</option>
            <option value="1">no camReady required</option>
            <option value="0">non reviewed</option>
        </select>

        <select id="filterSt" >
            <option value>Filter By status</option>
            @foreach($paperStatus as $ps)
                <option value="{{$ps->label}}">{{$ps->label}}</option>
            @endforeach
        </select>
    </div>
    <div class="buttons">
        <button class="zip button gray ui tiny" style="margin-bottom:30px">{{$P['DOWN']}}</button>
        <button class="button gray ui show-modall tiny" style="margin-bottom:30px"></i>Send mails</button>
        <button class="button gray ui  tiny" style="margin-bottom:30px"></i>Download excel</button>
        <button class="button gray ui resetFilter tiny" style="margin-bottom:30px"></i>Reset filter</button>
    </div>
     

    <table id="datatable" class="ui selectable table display paperTab" cellspacing="0" width="100%">

   


    
    <thead>

        <tr>

             <th>{{$P['ID']}}</th>
            <th>{{$P['TITLE']}}</th>
            <th class="select-filter">{{$P['M_AUTHOR']}}</th>
            <th class="select-filter">{{$P['COUNTRY']}}</th>
            <th class="select-filter">{{$P['TOPIC']}}</th>
            @if( ($conference->camReady == 'Y')and($conference->is_cam_ready_open == 'Y') )
                <th>camReady</th>
                <th>camReadyRq</th>
            @endif
            <th>Reviewers</th>
            <th>{{$P['STATUS']}}</th>
            <th>{{$P['ACTIONS']}}</th>
        </tr>
    </thead>

    <tbody>
        
        @foreach($papers as $paper)
            <tr>
                <td>{{$paper->id}}</td>
                <td>{{$paper->title}}</td>
                <td>{{$paper->first_name}} {{$paper->last_name}}</td>
                <td>{{$paper->country}}</td>
                <td>{{$paper->acronym}}</td>
                
                <td>
                    @if ((\File::exists('papers/CR_'.strtoupper($paper->psLabel))) and ($conference->is_cam_ready_open == 'Y') )
                               <a class="button ui primary mini" href="/conferences/{{$conference->confAcronym}}/{{$conference->confEdition}}/papers/{{$paper->id}}/download/1" data-tooltip="{{$P['DOWN_CR_BTN']}}">  <i class="icon download cloud"></i></a> 
                    @elseif((\File::exists('papers/CR'.$paper->psLabel)) and ($conference->is_cam_ready_open == 'N') )
                             <a class="button ui primary" href="/conferences/{{$conference->confAcronym}}/{{$conference->confEdition}}/papers/{{$paper->id}}/download/CR/1" data-tooltip="{{$P['DOWN_CR_BTN']}}">  <i class="icon download cloud"></i></a> 
                           
                    @endif
                </td>
                @if( ($conference->camReady == 'Y')and($conference->is_cam_ready_open == 'Y') )
                <td>
                    @if ((\File::exists('papers/CR_'.strtoupper($paper->psLabel))) and ($paper->camReadyRq == 1) ) 
                            3
                    @elseif (!(\File::exists('papers/CR_'.strtoupper($paper->psLabel))) and ($paper->camReadyRq == 1) ) 
                            2
                    @elseif ($paper->camReadyRq === 0)
                            1
                    @else
                            0
                    @endif

                </td>
                @endif
                <td>
                    <?php $reviewers = \DB::table('paper_user')->where('paper_id',$paper->id)
                                                                ->join('users','users.id','=','paper_user.user_id')
                                                                ->select('users.*')
                                                                ->get();

                    ?>
                    @foreach($reviewers as $rev)
                        <?php $nbP = \DB::table('paper_user')->where('user_id',$rev->id)->count() ?>
                        {{$rev->first_name}} {{$rev->last_name}} ({{$nbP}}) <br>
                    @endforeach

                </td>
                <td>{{$paper->psLabel}}</td>
                <td>
                    <form id="formDeletePaper" action="/conferences/{{$conference->confAcronym}}/{{$conference->confEdition}}/papers/{{$paper->id}}/delete" method = "POST">
                            <a style="margin-bottom:6px"  class="button ui primary mini" href="/conferences/{{$conference->confAcronym}}/{{$conference->confEdition}}/papers/{{$paper->id}}/download/0" data-tooltip="{{$P['DOWN_BTN']}}">  <i class="icon download cloud"></i></a> </h2> 
                            <a style="margin-bottom:6px" href="/conferences/{{$conference->confAcronym}}/{{$conference->confEdition}}/papers/{{$paper->id}}" class="button ui green mini" data-tooltip="{{$P['SHOW_BTN']}}" ><i class="eye icon"></i></a>
                            <a href="/conferences/{{$conference->confAcronym}}/{{$conference->confEdition}}/papers/assign/{{$paper->id}}" class="button ui teal mini" data-tooltip="{{$P['ASSIGN_BTN']}}"><i class="add user icon"></i></a>
                            <a  class="button ui gray mini notifyAuthor" data-tooltip="Notify author"><i class="mail icon"></i></a> 
                            <button id="btnDeletePaper" type="submit" class="button ui red mini" data-tooltip="{{$P['DELETE_BTN']}}"><i class="delete icon"></i></button>
                            <input type="hidden" value="{{$paper->id}}" class="zipInput" id="{{$paper->id}}" name="idForZip[]"> 
                            <input type="hidden" value="{{$paper->email}}" class="mailInput" name="idForMail[]"> 

                        </form>
                </td>
            </tr>

        @endforeach
    </tbody>


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
                <option class="placeholder" value="" disabled selected >Select</option>
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

<script class="init">

function format ( d ) {
    var b = d.body.split("\n");

    //var c = "" ;
    /*for (var i=0;i<b.length;i++)
        c = c + b[i];*/


        //console.log(c);
    return b;


}

 $(document).ready(function() {
   var lang = "{{ $lang }}"
if( lang === "fr")
    var url = "http://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/French.json";
else
    var url = "http://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/English.json";
var  dt = $('#datatable').DataTable( {

            
            initComplete: function () {
            var i=1;
            this.api().columns('.select-filter').every( function () {
                var column = this;
                var columns = this.settings().init().columns;
                
                i++;
                var select = $('<select><option value="">Filter by '+columns[i].name+'</option></select>')
                    .appendTo( ".tt" )
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

            columns: [
                {name: 'id'},
                {name: 'title'},
                {name: 'name'},
                {name: 'country'},
                {name: 'topic'},
                {name: 'reviwers'},
                {name: 'status'},
                {name: 'camReady'},
                {name: 'camReadyRq'},
                {name: 'actions'},
            ],


            dom: 'Bfrtip',

            columnDefs: [
                {
                    targets: 1,
                    className: 'noVis'
                },
                {
                    "targets": [ 6 ],
                    "visible": false,
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
            serverSide: false,
            //ajax: '{{ route('conferences.papers.indexall', [$conference->confAcronym, $conference->confEdition] ) }}',


           /* columns: [


                {data: 'id', name: 'id'},
                { "data": "title" },
                { "data": "email" },
                { "data": "country"},
                { "data": "label" },
                @if( ($conference->camReady == 'Y')and($conference->is_cam_ready_open == 'Y') )
                { "data": "camReady" },
                { data: "camReadyRq", visible: false },
                @endif
                {data: 'action', name: 'action', orderable: false, searchable: false},

            ],*/
            order: [[0, 'desc']]

    } );

     $("#filterCr").change(function(){         
        var val = $.fn.dataTable.util.escapeRegex($(this).val());      
          dt.columns(6)
            .search( val ? '^'+val+'$' : '', true, false )
            .draw();
    });

     $("#filterAuth").change(function(){   
          var val = $.fn.dataTable.util.escapeRegex($(this).val());      
          dt.columns(2)
            .search( val ? '^'+val+'$' : '', true, false )
            .draw();
    });

    $("#filterSt").change(function(){   
          var val = $.fn.dataTable.util.escapeRegex($(this).val());      
          dt.columns(8)
            .search( val ? '^'+val+'$' : '', true, false )
            .draw();
    });



    $('.zip').click(function () {
        var arrayId = [];
        //console.log(dt.data().count());
        $('#datatable tbody tr td .zipInput').each(function(){
            arrayId.push(this.id);
        });
        console.log(arrayId);
        window.location.href = "/download/zip?inputZip=["+arrayId+"]";
    });




    $('.paperTab').on("submit", "#formDeletePaper" , function(){
             return confirm("{{$P['CONFIRM']}}");
        });

 
    $('.show-modall').click(function () {
        var arrayMail = [];
        //console.log(dt.data().count());
        $('#datatable tbody tr td .mailInput').each(function(){
            arrayMail.push(this.value);
        });
        //console.log(arrayId);
        var arrC="";
        for(i=0;i<arrayMail.length;i++ ){
            if(i != arrayMail.length-1)
                arrC=arrC+arrayMail[i]+';';
            else
                arrC=arrC+arrayMail[i];
        }
        console.log(arrC);
        $('#emails').val(arrC)
        if(arrayMail.length!=0){
            $('.ui.modal').modal('show');
        }else{
            alert('No matching records found');
        }
    });


    $('.notifyAuthor').click(function () {
      //console.log(arrayId);

        $('#emails').val($(this).siblings('.mailInput').val());

            $('.ui.modal').modal('show');

  
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
            $('.filters select').val('');
            var table = $('#datatable').dataTable();
            table.fnFilterClear();

        });
    
   





 } );

</script>

@endpush
@endsection
