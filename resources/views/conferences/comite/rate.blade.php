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


    .column-vis{
        position:absolute;
        background-color:#ddd;
        width:145px;
        height: 196px;
        display: none;
        z-index:5;
        
    }

    .column-vis ul{
        list-style: none;
        position: relative;
        
        left: -40px;
        top:-13px;
        width:185px;
        cursor:pointer
    }

    .column-vis label{
        cursor:pointer
    }


    tfoot{
        display: table-header-group;
    }

</style>
@endpush
<div class="container ui">
  {!! Breadcrumbs::render('sub_papers',$conference) !!}
<h2 class="ui dividing header">{{$P['LIST']}} </h2>



<div class="op"></div>

<form id="form" method="post" action="{{route('conferences.comite.storeRate',[$conference->confAcronym,$conference->confEdition])}}">
    {{csrf_field()}}
    @include('layouts.errors')
    <div class="ui message">
        <p>Double Click on rate to change it</p>
    </div>
    <button class="button ui primary" type="submit" >Commit</button>
    <button class="button ui" type="reset">Reset</button> <br> <br>
    <table id="datatable" class="ui selectable table display paperTab" cellspacing="0" width="100%">
        <tfoot>
            <th></th>


            
            <th>
                <select id="engines" >
                    <option value></option>
                    @foreach($ratelabels as $rl)
                        <option value="{{$rl->label}}">{{$rl->label}}</option>
                    @endforeach
                </select>
            </th>


        </tfoot>
        <thead>

            <tr>
                <th id="th1" >{{$P['PAPER']}}</th>
                <th id="th2" >Rating</th>

                

            </tr>
        </thead>

        <tbody>
            @foreach($papers as $paper)
                @if($paper->ratelabel_id == null)
                    <tr class="negative">
                @else
                    <tr class="positive">
                @endif
                    <td><a href="{{route('conferences.papers.show',[$conference->confAcronym,$conference->confEdition,$paper->id])}}">paper {{$paper->id}}</a></td>
   
                    <td>
                        @if($paper->ratelabel_id == null)
                            <input type="hidden" name="papers[]" value="{{$paper->id}}">
                            <select name="rating[]" required>
                                <option value="0">Select</option>
                                @foreach($ratelabels as $rl)
                                    <option value="{{$rl->id}}">{{$rl->label}}</option>
                                @endforeach
                            </select>
                        @else
                        
                            <div id="rating{{$paper->id}}">
                                <span id="{{$paper->id}}">
                                    <input type="hidden" name="papers[]" value="{{$paper->id}}">
                                        @foreach($ratelabels as $rl)
                                                @if($paper->ratelabel_id == $rl->id)
                                                    <input type="hidden" name="rating[]" value="{{$rl->id}}">
                                                    <span>{{$rl->label}}</span>
                                                @endif
                                        @endforeach
                                </span>

                            </div>
                        @endif
                    </td>
                    

                </tr>
                
            @endforeach
        </tbody>



    </table>
    </form>




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
            serverSide: false,
     

     
            order: [[0, 'desc']]

    } );

    //$('#engines').change( function() { dt.fnFilter( $(this).val() ); } );


    $("#engines").change(function(){         
        dt
            .columns(1)
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


    @foreach($papers as $paper)
        $('#{{$paper->id}}').dblclick(function(){
            $('#rating{{$paper->id}}').append('<input type="hidden" name="papers[]" value="{{$paper->id}}">'
                                        +'<select name="rating[]" required>'
                                         +'<option value="0" >Select</option>'
                                        +' @foreach($ratelabels as $rl)'
                                        +    '@if($paper->ratelabel_id == $rl->id)'
                                        +         ' <option selected value="{{$rl->id}}">{{$rl->label}}</option>'
                                        +  ' @else'
                                              +    '  <option value="{{$rl->id}}">{{$rl->label}}</option> ---}}'
                                              +  '@endif'
                                            +'@endforeach'
                                       +' </select> ');
            $(this).remove();
        });
    @endforeach

 } );

</script>

@endpush
@endsection
