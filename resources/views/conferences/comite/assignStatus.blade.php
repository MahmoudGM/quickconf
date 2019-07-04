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

    .ui.message {
        margin-bottom: 10px !important;
    }

    .filters{
        background-color: #f7f7f7;
        margin-bottom: 22px;
        padding: 14px;
        border-radius:5px;
        border: 1px solid #ddd;
        text-align:center
    }

    tfoot{
        display: table-header-group;
        position:absolute;
        top:-110px;
        left:116px;
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
    .buttons span, .buttons button{
        width:30% !important;   
        
    }

    .buttons button:hover{
        background-image: linear-gradient(to bottom, #fff 0%, #e9e9e9 100%) !important;
    }

    .filters select{
        border-radius:5px;
        margin:7px 7px 7px 7px;
        background-color: #fff;

    }


</style>
@endpush
<div class="container ui">
{!! Breadcrumbs::render('assignStatus',$conference) !!}

<form style="margin-top:30px" id="form" method="post">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <h2 class="ui dividing header">{{$P['LIST']}} </h2>
    <button class="button ui primary" type="submit" onclick="submitForm('{{route('conferences.comite.storeAssignStatus',[$conference->confAcronym,$conference->confEdition])}}')">Commit</button>
    <button class="button ui" type="reset">Reset</button>
    {{csrf_field()}}
    @include('layouts.errors')
    <div class="ui message">
        <p>Double Click on the status to change it</p>
    </div>
     

    <div class="filters">
        <span class="tt"></span>
        <select id="engines" >
            <option value>Filter by status</option>
                <option value="0">Rejected</option>
                <option value="1">Accepted</option>
                <option value="-1">null</option>
        </select>

        <select id="enginesRevSt" >
            <option value>Filter by reviewers_status</option>
                <option value="0">without reviewers</option>
                <option value="1">not complete</option>
                <option value="2">complete</option>
        </select>
         <input style="width:75px;display:inline-block;border-radius:5px;outline:none;padding:2px" placeholder="min score" type="text" id="min" name="min">
        <input style="width:75px;display:inline-block;border-radius:5px;outline:none;padding:2px" placeholder="max score" type="text" id="max" name="max">
            
    </div>
    <div class="buttons">
        <span class="button gray ui tiny notifyAuthors" >Notify authors</span>
        <span class="button gray ui  tiny" style="margin-bottom:30px"></i>Download excel</span>
        <span class="button gray ui resetFilter tiny" style="margin-bottom:30px"></i>Reset filter</span>
    </div>

    <table id="datatable" class="ui selectable table display paperTab" cellspacing="0" width="100%">
        
        <thead>

            <tr>
                <th id="th1" >{{$P['ID']}}</th>
                <th id="th2" >{{$P['TITLE']}}</th>
                <th class="select-filter"  id="th3" >{{$P['M_AUTHOR']}}</th>
                <th class="select-filter" id="th4" >{{$P['COUNTRY']}}</th>
                
                <th  id="th6" >Status</th>
                <th id="th7">Reviewers</th>
                <th id="th7">Accepted</th>
                <th id="th7">nbrevs</th>
                <th id="th7">Score</th>
                <th id="th7">Camera-ready version</th>
                <th id="th8" >{{$P['ACTIONS']}}</th>
            </tr>
        </thead>

        <tbody>
            @foreach($papers as $paper)
                @if($paper->paperstatus_id == null)
                    <tr class="negative">
                @else
                    <tr class="positive">
                @endif
                    <td class="td1" >{{$paper->id}}</td>
                    <td class="td2" >{{$paper->title}}</td>
                    <td class="td3" >{{$paper->first_name}} {{$paper->last_name}}</td>
                    <td class="td4" >{{$paper->country}}</td>
   
                    <td class="td6" >
                       {{-- @if($paper->paperstatus_id == null)
                            <input type="hidden" class="papers" name="papers[]" value="{{$paper->id}}">
                            <select id="select{{$paper->id}}"  class="status" name="status[]" required>
                                <option  class="placeholder" value="0">Select</option>
                                @foreach($paperStatus as $ps)
                                    <option value="{{$ps->id}}">{{$ps->label}}</option>
                                @endforeach
                            </select>
                        @else --}}
                        
                            <div id="status{{$paper->id}}">
                                <span id="{{$paper->id}}">
                                    <input type="hidden" class="papers" name="papers[]" value="{{$paper->id}}">
                                        @foreach($paperStatus as $ps)
                                                @if($paper->paperstatus_id == $ps->id)
                                                    <input type="hidden" class="status" name="status[]" value="{{$ps->id}}">
                                                    <span>{{$ps->label}}</span>
                                                @endif
                                        @endforeach
                                        @if($paper->paperstatus_id == null)
                                        <select id="select{{$paper->id}}"  class="status" name="status[]" required>
                                            <option  class="placeholder" value="0">Select</option>
                                            @foreach($paperStatus as $ps)
                                                <option data-value="{{$ps->accepted}}" value="{{$ps->id}}">{{$ps->label}}</option>
                                            @endforeach
                                        </select>
                                        @endif
                                </span>

                                

                                </div>
                 
                    </td>
                    <td class="td7" >
                    <?php 
                        $reviews = \DB::table('reviews')
                                ->join('papers' , 'papers.id' ,'=', 'reviews.paper_id' )
                                ->join('users' , 'users.id' ,'=', 'reviews.user_id' )
                                ->where('papers.id',$paper->id)
                                ->select('users.*','reviews.*','papers.id as paperId','reviews.user_id as rId','reviews.id as revId')
                                ->get();
                            $i=1;
                     ?>
                        <div>

                            @foreach($reviews as $rv)
                                <label style="margin-bottom:5px" class="ui label">Reviewer {{$i}}: {{$rv->first_name}} {{$rv->last_name}} <a class="notifyRev" data-tooltip="notify reviewer"> <i class="mail icon"></i> </a> </label> 
                                <a id="{{$rv->revId}}" class="show-modall"><label data-tooltip="show review" style="cursor:pointer" class="ui label">  {{$rv->overall}} <i class="icon star"></i></label></a><br> <br>
                                
                                <?php $i++?>
                            @endforeach
                        </div>
                    </td>
                    <td  data-order="{{$paper->id}}" id="st{{$paper->id}}">
                    <span class="editable">
                        @if($paper->psLabel === 0) 
                            0 
                        @elseif ($paper->psLabel === 1) 
                            1 
                        @else
                            -1
                        @endif
                    </span>
                    </td>
                    <td>
                        <?php $nbRev = \DB::table('paper_user')->where('paper_id',$paper->id)->count(); ?>
                        @if($nbRev === 0) 
                            0
                        @elseif($nbRev != count($reviews))
                            1
                        @else
                            2
                        @endif
                    </td>
                    <td>
                    @if(count($reviews) != 0)
                        <?php $s=0; 
                            foreach($reviews as $rv){
                                $s = $s + $rv->overall;
                            }
                        ?>
                        {{$s/count($reviews)}}
                    @endif
                    </td>
                    <td>
                        @if ((\File::exists('papers/CR_'.strtoupper($paper->psLabel))) and ($conference->is_cam_ready_open == 'Y') )
                                <a class="button ui primary mini" href="/conferences/{{$conference->confAcronym}}/{{$conference->confEdition}}/papers/{{$paper->id}}/download/1" data-tooltip="{{$P['DOWN_CR_BTN']}}">  <i class="icon download cloud"></i></a> 
                        @elseif((\File::exists('papers/CR'.$paper->psLabel)) and ($conference->is_cam_ready_open == 'N') )
                                <a class="button ui primary mini" href="/conferences/{{$conference->confAcronym}}/{{$conference->confEdition}}/papers/{{$paper->id}}/download/CR/1" data-tooltip="{{$P['DOWN_CR_BTN']}}">  <i class="icon download cloud"></i></a> 
                            
                        @endif
                      
                    </td>
                    <td class="td8" >
                
                            <a style="margin-bottom:4px" class="mini button ui primary" href="/conferences/{{$conference->confAcronym}}/{{$conference->confEdition}}/papers/{{$paper->id}}/download/0" data-tooltip="Download">  <i class="icon download cloud"></i></a> </h2> 
                            <a style="margin-bottom:4px" href="/conferences/{{$conference->confAcronym}}/{{$conference->confEdition}}/papers/{{$paper->id}}" class="button ui green mini" data-tooltip="Show"><i class="eye icon"></i></a> 
                            <a href="/conferences/{{$conference->confAcronym}}/{{$conference->confEdition}}/committee/notifyAuthors?ids={{$paper->id}}" class="button ui teal mini" data-tooltip="Notify author"><i class="mail icon"></i></a> 
                            <input type="hidden" id="{{$paper->id}}" value="{{$paper->id}}" class="mailInput" name="idForMail[]"> 
 
                    </td>
                </tr>
                
            @endforeach
        </tbody>



    </table>
    </form>

    <div class="ui modal">
        <i class="close icon"></i>
        <div class="header title-rev">   </div>
        <div class="content" style="padding-bottom:20px;height:650px;overflow: scroll;overflow-x: hidden;">
            <div class="panel">
            <div class="header">
                Paper information 
                <i class="icon minus square large blue pull-right min-div" style="display:none;cursor:pointer"></i>
                <i class="icon plus square large blue pull-right max-div" style="cursor:pointer"></i>
            </div>
            <div class="body paperinfo-div" style="display:none">
                
            </div>
        </div>
            
            <div class="panel">
            <div class="header"> 
                Criterias <label class="ui label teal small">( Strong Reject, Reject, Weak Reject, Neutral, Weak Accept, Accept, Strong Accept )</label>
                <i class="icon minus square large blue pull-right min-div" style="cursor:pointer"></i>
                <i class="icon plus square large blue pull-right max-div" style="display:none;cursor:pointer"></i>
            </div>
                <div class="body criterias-div">
                </div>
            </div>

            <div class="panel">
            <div class="header">
                Review expertise <label class="ui label teal small">( Low,Meduim,High )</label>
                <i class="icon minus square large blue pull-right min-div" style="cursor:pointer"></i>
                <i class="icon plus square large blue pull-right max-div" style="display:none;cursor:pointer"></i>
            </div>
            <div class="body expertise-div">
                
            </div>
        </div>

        <div class="panel">
            <div class="header">
            <i class="icon minus square large blue pull-right min-div" style="cursor:pointer"></i>
            <i class="icon plus square large blue pull-right max-div" style="display:none;cursor:pointer"></i>
  
            <label for="details">Comments for Program Committee (not shown to the authors)	</label>
            </div>
            <div class="body details-div">
            </div>
        </div>

        <div class="panel">
            <div class="header">
            <i class="icon minus square large blue pull-right min-div" style="cursor:pointer"></i>
            <i class="icon plus square large blue pull-right max-div" style="display:none;cursor:pointer"></i>
  
            <label for="comments">Questions</label>
            </div>
            <div class="body questions-div">
            
            </div>
        </div>

        </div>
        <div class="actions">
            <div class="ui button deny">Cancel</div>
        </div>

    </div>




</div>

@push('script')

<script class="init">
/*function lenObj(a){
    var count = 0;
    var i;

    for (i in a) {
        if (a.hasOwnProperty(i)) {
            count++;
        }
    
    return count;
}
}*/
function submitForm(action)
{
    document.getElementById('form').action = action;
    document.getElementById('form').submit();
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
                console.log(columns);
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
                {name: 'status'},
                {name: 'reviewers'},
                {name: 'accepted'},
                {name: 'reviewers status'},
                {name: 'score'},
                {name: 'cam-ready-version'},
                {name: 'actions'},
            ],


            


            dom: 'Bfrtip',

            columnDefs: [
                {
                    targets: 1,
                    className: 'noVis'
                },
                {
                    "targets": [ 6,7 ],
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

            order: [[0, 'desc']],
            

    } );

    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
            return parseFloat(data[8]) >= parseFloat($('#min').val() || data[8]) 
                && parseFloat(data[8]) <= parseFloat($('#max').val() || data[8])
        });
    $('#min, #max').on('keyup', dt.draw);



    $("#engines").change(function(){ 

        var term = this.value;
        dt.columns(6).search(term ).draw();
        
    });

     $("#enginesRevSt").change(function(){ 

        var term = this.value;
        dt.columns(7).search(term ).draw();
        
    });

    $('.resetFilter').click(function(){
            $('#datatable select').val('');
            $('#min').val('');
            $('#max').val('');
            var table = $('#datatable').dataTable();
            table.fnFilterClear();

        });


    




    $('.paperTab').on("submit", "#formDeletePaper" , function(){
             return confirm("{{$P['CONFIRM']}}");
        });


    $('#btn-vis').click(function(){
        $('.column-vis').fadeToggle(300);
        $('.op').fadeToggle(300);
        event.stopPropagation();
    });

    $('.column-vis').click(function(){
        event.stopPropagation();
    });

    $('.column-vis ul label').click(function(){
        $(this).toggleClass("selected");
    });
    

    $(window).click(function(){
        $('.column-vis').fadeOut(300);
        $('.op').fadeOut(300);
    });

    $(document).on('change', '.column-vis input', function() {
    var checked = $(this).is(":checked");
    
        console.log(this.id);
        var index = $(this).parent().index();
        if(checked) {
            $('.td'+this.id).show();
            $('#th'+this.id).show();
        } else {
            $('.td'+this.id).hide();
            $('#th'+this.id).hide();
        }
    });

    $(document).keyup(function(e) {
        if (e.keyCode === 27) {
            $('.column-vis').fadeOut(300);  
            $('.op').fadeOut(300);  
        } 
    });

    var label="";
    var idSt="";
    @foreach($papers as $paper)



        $('#status{{$paper->id}}').on('change','#select{{$paper->id}}',function() {
            label = $(this).find('option:selected').text();
            idSt = this.value;
             /*@foreach($paperStatus as $ps)'
                @if($paper->paperstatus_id == $ps->id)
                    var status = '{{$ps->label}}' ;
                @endif
            @endforeach*/
            acc = $(this).find('option:selected');

            $('#status{{$paper->id}}').empty();
            $('#st{{$paper->id}}').empty();
            $('#st{{$paper->id}}').append(acc.data('value'));

            var $tableRow = $('#st{{$paper->id}}').parent();
            dt.row( $tableRow ).invalidate();

            $('#status{{$paper->id}}').append('<span id="{{$paper->id}}">'
                                    +'<input type="hidden" class="papers" name="papers[]" value="{{$paper->id}}">'
                                         +'           <input type="hidden" class="status" name="status[]" value="'+idSt+'">'
                                         +'         <span>'+label+'</span>'
                                +'</span>');

            var papers = {papers:[]};
            $('input[name^="papers"]').each(function() {
                item = $(this).val();
                papers.papers.push(item)
            });

            var status = {status:[]};
            $('input[name^="status"]').each(function() {
                item = $(this).val();
                status.status.push(item)
            });

            var json1 = JSON.stringify(papers);
            var json2 = JSON.stringify(status);

            var params = json1.concat(json2);

            console.log(status);

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type:"POST",
                url : '/conferences/{{$conference->confAcronym}}/{{$conference->confEdition}}/assignstatus',
                async: true,
                dataType: 'json',
                data: {status,papers},
                success : function(response) {
                    var data = response;
                    return response;
                    
                }
            });


            
         });


        $('#status{{$paper->id}}').on('dblclick','#{{$paper->id}}',function(){
            $('#status{{$paper->id}}').append('<input type="hidden" class="papers" name="papers[]" value="{{$paper->id}}">'
                                        +'<select id="select{{$paper->id}}" class="status" name="status[]" required>'
                                         +'<option class="placeholder" value="0" >Select</option>'
                                        +' @foreach($paperStatus as $ps)'
                                              +    '  <option data-value="{{$ps->accepted}}" value="{{$ps->id}}">{{$ps->label}}</option> ---}}'
                                            +'@endforeach'
                                       +' </select> ');

            //$('#select{{$paper->id}}').find('option[value="'+idSt+'"]').prop('selected', true); 
            $(this).remove();

        
        });

    

     
        
    @endforeach

    

    $('#datatable tbody tr td').on('click','.show-modall',function () {
        var id=this.id;

        

        $.ajax({
            type:"GET",
            url : '/conferences/{{$conference->confAcronym}}/{{$conference->confEdition}}/papers/review/'+id+'/json',
            async: true,
            success : function(response) {
                var data = response;
                $('.title-rev').empty();
                $('.title-rev').append('show review paper'
                                        +data.reviews.paper_id
                                        +' ('+data.reviews.overall +')');
                
                var criterias = data.rev_criterias.items;
                $('.paperinfo-div').empty();
                $('.paperinfo-div').append('title: '+data.paper.title
                                            +'<br>author: '+data.paper.first_name+' '+data.paper.last_name
                                            +'<br>topic: '+data.paper.label);
                $('.criterias-div').empty();
                for(i=0;i<_.size(criterias);i++){
                    $('.criterias-div').append('<div class="message ui">'
                                                +criterias[i].label+' <div data-rating="'+criterias[i].mark+'" class="ui star huge rating criteria "></div>'
                                                +'</div>'
                                                +criterias[i].explanation);
                }
                $('.expertise-div').empty();
                console.log(data.questions[0].choice);
                $('.expertise-div').append(' <div data-rating='+data.reviews.reviewExpertise+' class="ui star huge rating expertise " ></div>');

                $('.details-div').empty();
                $('.details-div').append(data.reviews.details);

                $('.questions-div').empty();
                for(i=0;i<_.size(data.questions);i++){
                $('.questions-div').append('<div class="field">'
                                        +'<label>'+data.questions[i].question+': </label>'
                                        +data.questions[i].choice
                                        +' </div>');
                }
                $('.ui.rating.expertise').rating({
                    initialRating: 0,
                    maxRating: 3
                });

                $('.ui.rating')
                .rating({
                    initialRating: 0,
                    maxRating: 7
                });

                $('.ui.rating')
                .rating('disable');
                return response;
            },
            error: function() {
                alert('Error occured');
            }
                
        });
        $('.ui.modal').modal('show');    

    });

    $('.modal').on('click','.min-div',function () {
        $(this).hide();
        $(this).parent().siblings('.body').slideToggle();
        $(this).siblings('.max-div').show();
    });

    $('.modal').on('click','.max-div',function () {
        $(this).hide();
        $(this).parent().siblings('.body').slideToggle();
        $(this).siblings('.min-div').show();
    });


    $('.notifyAuthors').click(function () {
        var arrayId = [];
        //console.log(dt.data().count());
        $('#datatable tbody tr td .mailInput').each(function(){
            arrayId.push(this.id);
        });

        if(arrayId.length!=0){
            window.location.href = "/conferences/{{$conference->confAcronym}}/{{$conference->confEdition}}/committee/notifyAuthors?ids=["+arrayId+"]";
        }else{
            alert('No matching records found');
        }
    });

    $('.zip').click(function () {
        var arrayId = [];
        //console.log(dt.data().count());
        $('#datatable tbody tr td .zipInput').each(function(){
            arrayId.push(this.id);
        });
        console.log(arrayId);
        if(arrayId.length!=0){
        window.location.href = "/download/zip?inputZip=["+arrayId+"]";
        }else{
            alert('No matching records found');
        }
    });

   
    



 } );

</script>

@endpush
@endsection
