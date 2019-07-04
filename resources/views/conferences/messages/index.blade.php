@extends('layouts.app')
<?php
    $lang = Session::get('lang');
    $MSG = parse_ini_file(base_path('language/'.$lang.'/MESSAGE_TEMP.ini'));
?>

<title>{{$MSG['LIST']}}</title>
@section('content')

<style>
    #datatable td, #datatable th  {
        text-align: center
    }
</style>

<div class="container ui">
{!! Breadcrumbs::render('msgTemp',$conference) !!}
<h2 class="ui dividing header">{{$MSG['LIST']}}</h2>
 <a href="{{ route('conferences.messages.create', [$conference->confAcronym, $conference->confEdition] ) }}" class="ui primary button"  ><i class="add square icon"></i>{{$MSG['BTN_ADD']}}</a> <hr>

    <table id="datatable" class="ui selectable table display" cellspacing="0" width="100%">

    <thead>
        <tr>
            <th>{{$MSG['ID']}}</th>
            <th>{{$MSG['NAME']}}</th>
            <th>{{$MSG['TITLE']}}</th>
            <th>{{$MSG['ACTIONS']}}</th>
        </tr>
    </thead>

    </table>

    <div class="ui modal">
        <i class="close icon"></i>
        <div class="header title-msg">   </div>
        <div class="content" style="padding-bottom:20px;height:350px;overflow: scroll;overflow-x: hidden;"></div>


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
            language: {
                        "url": url
                    },
            processing: true,
            serverSide: true,
            ajax: '{{ route('conferences.messages.indexall', [$conference->confAcronym, $conference->confEdition] ) }}',


            columns: [
                
                {data: 'id', name: 'id'},
                { "data": "name" },
                { "data": "title" },
                {data: 'action', name: 'action', orderable: false, searchable: false},

            ]


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

    $('#datatable tbody').on( 'click', '.show', function () {
        console.log($(this).attr("title"));
        var id=this.id;
        $.ajax({
  
            type:"GET",
            url : '/conferences/{{$conference->confAcronym}}/{{$conference->confEdition}}/messages/'+id+'/showbody',
            async: true,
            success : function(response) {
                var data = response;
                $('.content').empty();
                $('.title-msg').empty();
                $('.title-msg').append('{{$MSG['NAME']}}:'+ data.name);
                $('.content').append('<fieldset>'
                                   + '<legend>'+data.title+':</legend>'
                                   +data.body
                                   +'</fieldset>');
                return response;
            },
            error: function() {
                alert('Error occured');
            }
      
        });
        $('.ui.modal').modal('show');
    });

    $('#datatable').on("submit", "#formDeleteMsg" , function(){
             return confirm("{{$MSG['CONFIRM']}}");
        });
 } );

</script>

@endpush
@endsection
