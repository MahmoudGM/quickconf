@extends('layouts.app')

<?php
    $lang = Session::get('lang');
    $Q = parse_ini_file(base_path('language/'.$lang.'/RQUESTIONS.ini'));
?>

<title>{{$Q['LIST']}}</title>
@section('content')

@push('style')
<style>
    #datatable td, #datatable th  {
        text-align: center
    }

    .choices input{
        margin:0 0 10px 0 !important;
    }
</style>
@endpush
<div class="container ui">
{!! Breadcrumbs::render('rquestions',$conference) !!}
<h2 class="ui dividing header">{{$Q['LIST']}}</h2>
 <a href="{{ route('conferences.rquestions.create', [$conference->confAcronym, $conference->confEdition] ) }}" class="ui primary button show-modal"  ><i class="add square icon"></i> {{$Q['BTN_ADD']}} </a> <hr>

    <table id="datatable" class="ui selectable table display pqTab" cellspacing="0" width="100%">

    <thead>
        <tr>
            <th></th>
            <th>{{$Q['ID']}}</th>
            <th>{{$Q['QUESTION']}}</th>
            <th>{{$Q['PUBLIC']}}</th>
            <th>{{$Q['ACTIONS']}} </th>
        </tr>
    </thead>

    </table>




@push('script')
<script class="init">

function format ( d ) {
    var b = d.choices.split("\n");

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
            dom: 'Bfrtip',
            columnDefs: [
                {
                    targets: 1,
                    className: 'noVis'
                }
            ],
            buttons: [
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
            ajax: '{{ route('conferences.rquestions.indexall', [$conference->confAcronym, $conference->confEdition] ) }}',


            columns: [
                {
                    "className":      'details-control',
                    "orderable":      false,
                    "searchable":     false,
                    "data":           null,
                    "defaultContent": '<i class="unhide icon"></i>'
                },

                {data: 'id', name: 'id'},
                { "data": "question" },
                { "data": "public" },
                {data: 'action', name: 'action', orderable: false, searchable: false},

            ],
            order: [[1, 'asc']]


    } );

    // Array to track the ids of the details displayed rows
    var detailRows = [];

    $('#datatable tbody').on( 'click', 'tr td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = dt.row( tr );
        var idx = $.inArray( tr.attr('id'), detailRows );

        if ( row.child.isShown() ) {
            tr.removeClass( 'details' );
            row.child.hide();

            // Remove from the 'open' array
            detailRows.splice( idx, 1 );
        }
        else {
            tr.addClass( 'details' );
            row.child( format( row.data() ) ).show();

            //console.log(row);

            // Add to the 'open' array
            if ( idx === -1 ) {
                detailRows.push( tr.attr('id') );
            }
        }
    } );

    // On each draw, loop over the `detailRows` array and show any child rows
    dt.on( 'draw', function () {
        $.each( detailRows, function ( i, id ) {
            $('#'+id+' td.details-control').trigger( 'click' );
        } );

    } );

    $('.pqTab').on("submit", "#deletePqForm" , function(){
             return confirm("{{$Q['CONFIRM']}}");
        });

   


 } );


</script>
@endpush
@endsection