@extends('layouts.app')

<?php
$lang = Session::get('lang');
$P = parse_ini_file(base_path('language/'.$lang.'/PAPERS.ini'));
?>

<title>{{$P['MY_SUB']}} </title>

@section('content')

@push('style')
<style>
    #datatable td, #datatable th  {
        text-align: center 
    }

    input[type="search"]{
        margin-bottom:20px !important
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
  {!! Breadcrumbs::render('myPapersAut',$conference) !!}
<h2 class="ui dividing header">{{$P['MY_SUB']}} </h2>
    <div class="filters">
 
    </div>
    <div class="buttons">
        <button class="zip button gray ui tiny" style="margin-bottom:30px">{{$P['DOWN']}}</button>
        <button class="button gray ui  tiny" style="margin-bottom:30px"></i>Download excel</button>
        <button class="button gray ui resetFilter tiny" style="margin-bottom:30px"></i>Reset filter</button>
    </div>
    <table id="datatable" class="ui selectable table display paperTab" cellspacing="0" width="100%">
    
    <tfoot>
        <th></th>
        <th></th>
        <th>{{$P['TOPIC']}}</th>
         <th>
                <select id="engines" >
                    <option value>Filter By Cam Ready</option>
                    <option value="1">yes</option>
                    <option value="0">no</option>
                </select>
            </th>
        
    </tfoot>
    <thead>
    
        <tr>
            
             <th>{{$P['ID']}}</th>
            <th>{{$P['TITLE']}}</th>
            <th class="select-filter">{{$P['TOPIC']}}</th>
            @if( ($conference->camReady == 'Y')and($conference->is_cam_ready_open == 'Y') )
                <th>camReady</th>
                <th>camReadyRq</th>
            @endif
            <th>{{$P['ACTIONS']}}</th>
        </tr>
    </thead> 
    

    </table>





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
            ajax: '{{ route('conferences.authors.papers.indexall', [$conference->confAcronym, $conference->confEdition] ) }}',


            columns: [
                

                {data: 'id', name: 'id'},
                { "data": "title" },
                { "data": "label" },
                @if( ($conference->camReady == 'Y')and($conference->is_cam_ready_open == 'Y') )
                { "data": "camReady" },
                { data: "camReadyRq", visible: false },
                @endif
                {data: 'action', name: 'action', orderable: false, searchable: false},

            ],
            order: [[0, 'desc']],

    } );

     $('.resetFilter').click(function(){
            $('#datatable select').val('');
            var table = $('#datatable').dataTable();
            table.fnFilterClear();

        });
    
    $("#engines").change(function(){         
        dt
            .columns(4)
            .search(this.value)
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




  
 } );

</script>

@endpush


@endsection