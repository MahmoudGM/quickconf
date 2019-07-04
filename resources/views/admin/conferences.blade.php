@extends('layouts.app')

@push('style')
<style>
    .action i{
        font-size: 27px;
        margin-top: 7px;
        margin-left: 1px;
    }
    .action{
        border:none;
        border-radius:5px;
        color: #eee;
        margin-bottom:10px;
        cursor:pointer
    }
    .approve{
        background-color:#16ab39;
    }
    .delete{
        background-color:#db2828;
        margin-left:5px
    }
    input[type="search"]{
        margin-bottom:15px !important
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
</style>

@endpush

@section('content')
 <div class="container ui">
    <h3 class="header dividing ui">List of conferences</h3>

    <form id="form" class="ui form" method="POST" >
    {{csrf_field()}}
        @foreach($errors->all() as $error)
            <span class="help-block">
                <strong>{{ $error }}</strong>
            </span>
        @endforeach
        @if (session('success'))
            <span class="help-block">
                <strong>{{ session('success') }}</strong>
            </span>
        @endif

         <div class="filters">

        <span class="tt"></span>

        <div class="fields">
        <select id="filterApp" >
            <option value>Filter By approved</option>
            <option value="Yes">Yes</option>
            <option value="No">No</option>
        </select>

        <select id="filterDl" >
            <option value>Filter By deleted</option>
            <option value="Yes">Yes</option>
            <option value="No">No</option>
        </select>
        </div>
        </div>
        

        <div data-tooltip="Delete" data-inverted="" data-position="bottom center" onclick="submitForm('{{ route('admin.conferences.delete' ) }}')" class="pull-right action  delete"><i class="icon trash"></i></div>
        <div data-tooltip="Approve" data-inverted="" data-position="bottom center" onclick="submitForm('{{ route('admin.conferences.delete' ) }}')" class="pull-right action  approve"><i class="icon check circle outline"></i></div>
        
    <table id="datatable" class="ui celled table">
    <thead>
        <tr>
        <th>#id</th>
        <th>Name</th>
        <th>Acronym</th>
        <th>Edition</th>
        <th>Start date</th>
        <th>End date</th>
        <th>Approved</th>
        <th>Deleted</th>
        <th>
        
            <div class="inline field">
                    <div class="ui toggle checkbox all">
                    <input tabindex="0" class="hidden" type="checkbox">
                    <label>All</label>
                    </div>
            </div>
        </th>
        </tr>
    </thead>
    <tbody>
    
    @foreach($conferences as $c)
        @if($c->is_activated == 0)
            <tr class="negative">
        @else
            <tr class="positive">
        @endif
                <td>{{$c->id}}</td>
                
                <td><a href="conferences/{{$c->confAcronym}}/{{$c->confEdition}}">{{$c->confName}}</a></td>
                <td>{{$c->confAcronym}}</td>
                <td>{{$c->confEdition}}</td>
                <td>{{$c->start_date}}</td>
                <td>{{$c->end_date}}</td>
                <td>
                    @if ($c->is_activated == 0) No @else Yes @endif
                </td>
                <td>
                    @if ($c->is_deleted == 0) No @else Yes @endif
                </td>
                <td>
                    <div class="inline field">
                        <div class="ui toggle checkbox one">
                        <input name="confs[]" value="{{$c->id}}" tabindex="0" class="hidden" type="checkbox">
                        
                        </div>
                    </div>
                </td>

            </tr>
    @endforeach
    
    </tbody>
    
    </table>
    </form>
</div>

@endsection

@push('script')
<script>
function submitForm(action)
{
    
        document.getElementById('form').action = action;
        document.getElementById('form').submit();
   
}
$(document).ready(function(){

    $('.all').change(function(){
        if($(this).find('input').is(':checked')){
            $('.ui.checkbox.one').checkbox('check');
        }
        else{
            $('.ui.checkbox.one').checkbox('uncheck');
        }
   
    });



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
                {name: 'name'},
                {name: 'acronym'},
                {name: 'edition'},
                {name: 'start date'},
                {name: 'end date'},
                {name: 'approved'},
                {name: 'deleted'},
                {name: 'action'},
            ],


            dom: 'Bfrtip',

            columnDefs: [
                {
                    targets: 1,
                    className: 'noVis'
                },

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



            order: [[0, 'desc']]

    });

    $("#filterApp").change(function(){         
        var val = $.fn.dataTable.util.escapeRegex($(this).val());      
          dt.columns(6)
            .search( val ? '^'+val+'$' : '', true, false )
            .draw();
    });

    $("#filterDl").change(function(){         
        var val = $.fn.dataTable.util.escapeRegex($(this).val());      
          dt.columns(7)
            .search( val ? '^'+val+'$' : '', true, false )
            .draw();
    });


});


</script>

@endpush