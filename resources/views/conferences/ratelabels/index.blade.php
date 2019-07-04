@extends('layouts.app')
<?php
    $lang = Session::get('lang');
    $RL = parse_ini_file(base_path('language/'.$lang.'/RATELABELS.ini'));
?>

<title>{{$RL['TITLE']}}</title>
@section('content')

@push('style')
<style>

    span.button.red {line-height: .8;}
    span i{
        margin-left: 4px !important
    }
    .choices input,.added input{
        margin:0 0 10px 0 !important;
    }

    #datatable td, #datatable th  {
        text-align: center
    }
    .modal .content {

        overflow: hidden;
    }
</style>
@endpush


<div id="container" class="container ui">
<h2 class="ui dividing header">{{$RL['TITLE']}}</h2>
 <a href="{{ route('conferences.ratelabels.create', [$conference->confAcronym, $conference->confEdition] ) }}" class="ui primary button show-modal-long"  ><i class="add square icon"></i>{{$RL['BTN_ADD']}}</a> <hr>

    <table id="datatable" class="ui selectable table display topicTab" cellspacing="0" width="100%">

    <thead>
        <tr>

            <th>{{ $RL['ID']}}</th>
            <th>{{ $RL['LABEL']}}</th>
            <th>{{ $RL['ACTIONS']}}</th>
        </tr>
    </thead>

    </table>



  


</div>

</div>


</div>


@push('script')


<script src=""></script>

<!--
<script src="https://cdn.datatables.net/buttons/1.2.4/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/select/1.2.1/js/dataTables.select.min.js"></script>
<script src="{{ asset('js/dataTables.editor.min.js') }}"></script>
-->
<script class="init">

function printDiv(divName) {
     var printContents = document.getElementById(divName).innerHTML;
     var originalContents = document.body.innerHTML;

     document.body.innerHTML = printContents;

     window.print();

     document.body.innerHTML = originalContents;
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
            /*buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],*/
            language: {
                "url": url
            },
            processing: true,
            serverSide: true,
            ajax: '{{ route('conferences.ratelabels.indexall', [$conference->confAcronym, $conference->confEdition] ) }}',


            columns: [

                {data: 'id', name: 'id'},
                { "data": "label" },
                {data: 'action', name: 'action', orderable: false, searchable: false},

            ],



    } );


    $('.topicTab').on("submit", "#deleteTopicForm" , function(){
             return confirm("{{$RL['CONFIRM']}}");
        });



});

</script>
@endpush
@endsection