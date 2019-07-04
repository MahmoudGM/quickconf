@extends('layouts.app')
<?php
$lang = Session::get('lang');
$P = parse_ini_file(base_path('language/'.$lang.'/PAPERS.ini'));
?>

<title>{{$P['LIST']}}</title>
@section('content')

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
</style>
@endpush
<div class="container ui">
  {!! Breadcrumbs::render('myPapersRev',$conference) !!}
<h2 class="ui dividing header">{{$P['LIST']}} </h2>
    <button class="zip button teal ui" style="margin-bottom:30px"><i class="icon download cloud"></i>{{$P['DOWN']}}</button>
    <table id="datatable" class="ui selectable table display paperTab" cellspacing="0" width="100%">

    <tfoot>
        <th></th>
        <th></th>
        <th>{{$P['COUNTRY']}}</th>
        <th>{{$P['TOPIC']}}</th>
        <th></th>

    </tfoot>
    <thead>

        <tr>

             <th>{{$P['ID']}}</th>
            <th>{{$P['TITLE']}}</th>
            <th class="select-filter">{{$P['COUNTRY']}}</th>
            <th class="select-filter">{{$P['TOPIC']}}</th>
            <th>{{$P['ACTIONS']}}</th>
        </tr>
    </thead>


    </table>





</div>

@push('script')

<script class="init">


 $(document).ready(function() {
   var lang = "{{ $lang }}"
if( lang === "fr")
    var url = "http://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/French.json";
else
    var url = "http://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/English.json";
var  dt = $('#datatable').DataTable( {

            initComplete: function () {
            this.api().columns('.select-filter').every( function () {
                var column = this;
                var select = $('<select><option value=""></option></select>')
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
                'copy', 'csv', 'excel', 'pdf', 'print','pageLength',
                {
                    extend: 'colvis',
                    columns: ':not(.noVis)'
                }
            ],
            language: {
                "url": url
            },
            processing: true,
            serverSide: true,
            ajax: '{{ route('conferences.comite.mypapers.indexall', [$conference->confAcronym, $conference->confEdition] ) }}',


            columns: [


                {data: 'id', name: 'id'},
                { "data": "title" },
                { "data": "country"},
                { "data": "label" },
                {data: 'action', name: 'action', orderable: false, searchable: false},

            ],
            order: [[0, 'desc']]

    } );



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
