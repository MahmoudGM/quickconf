@extends('layouts.app')
<?php
    $lang = Session::get('lang');
    $P = parse_ini_file(base_path('language/'.$lang.'/PROGRAM.ini'));
?>

<title>{{$P['TITLE']}}</title>

@section('content')
@push('style')

<style>
*{
    outline:none
}



.slot{
    border:none !important;
    padding:8px !important;
}

.slot-table tr td {
    width: 200px !important;
}



.slot-box{
    
}

.slot-box .slotName{
    display: block;
    position: relative;
    bottom: 31px;
    left: -6px;
    text-align: right;
    z-index: 5;
    color: #fff;
}

.slot-box .addButton{
    position: relative;
    bottom: 52px;
    font-size:18px;
    left: 7px;
    z-index:5;
    color:#fff;
    
}
.slot-box .addButton i{
    margin-right: -3px;
    cursor:pointer;
}


.slot-box .session{
    background-color: #eee;
    margin:7px;
    display:block;
    padding:5px;
    text-align:center;
    cursor:move
}

.slot-div {
    color:#fff;
    padding:35px;
    position:absolute;
    width:100%;
    height:auto;
    min-height:200px;
    margin-top:45px;
    position:relative;
    transition:background-color .5s ease
  }

.connectedSortable:hover{
    background-color:#10afb9;
}

select > .placeholder {
            display: none;
        }


</style>

@endpush

<div class="ui container">
{!! Breadcrumbs::render('program',$conference) !!}
@if(count($slots) == 0)
    <a href="#" class="button ui blue create-slot"> {{$P['CR_SLOT_BTN']}}</a>
@else

@include('layouts.errors')

<h2 class="header dividing ui">{{$P['TITLE']}}</h2>

<form id="main-form" action="{{route('conferences.slots.commit',[$conference->confAcronym,$conference->confEdition])}}" method="POST">
{{csrf_field()}}
<meta name="csrf-token" content="{{ csrf_token() }}">
<a href="#" class="button ui blue create-slot"> {{$P['CR_SLOT_BTN']}}</a>
<button type="submit" class="ui button">{{$P['SAVE']}}</button>
<a href="{{route('conferences.slots.docProgram',[$conference->confAcronym,$conference->confEdition])}}" class="black button ui  pull-right"> {{$P['GEN']}}</a>
<a target="_blank" href="{{route('conferences.slots.preview',[$conference->confAcronym,$conference->confEdition])}}" class="orange button ui  pull-right">{{$P['PREV']}}</a>
<table class="ui selectable table display slot-table" style="margin-bottom:100px"  >

{{--<thead>
    <tr>
    @for($i=1;$i<=count($nbrDate);$i++)
        <th>day {{$i}}</th>
        
    @endfor
    </tr>  
</thead> --}}

<tbody>
       
        {{--@foreach($slots as $slot)
        
            <tr>
             @foreach($nbrDate as $nb)
                @if($nb->date == $slot->date)
                    <td>{{$slot->name}}</td>
                @else
                    <td></td>
                @endif
            @endforeach
            </tr>
 
        @endforeach--}}

         @foreach($nbrDate as $nb)
         <tr> 
            <td style="border:none;text-align:center !important">{{Carbon\Carbon::parse($nb->date)->toFormattedDateString() }} <a data-tooltip="{{$P['CR_SLOT_BTN']}}" href="#"><i data-date="{{$nb->date}}" class="icon square plus show-slot-date-modal"></i></a></td>
            @foreach($slots as $slot)
                @if($nb->date == $slot->date)
                    <td class="slot" >
                        <div class="slot-box" >
                            <?php
                            $sessions = \App\Session::where('slot_id',$slot->id)->orderBy('position')->get();
                            ?>
                            <span>
                                    <div id="{{$slot->id}}" @if($slot->type == 1) class="connectedSortable slot-div" @endif class="slot-div" style="background-color:{{$slot->color}}">
                                        @if($slot->type == 2) 
                                            <h1 style="left:90px;top:80px;position:absolute">{{$P['BREAK']}}</h1>
                                        @endif
                                        @foreach($sessions as $session)
                                                
                                                <a href="#" style="text-align:center" class="session" id="1" >
                                                    <input type="hidden" style="width:17px" class="sess"  name="slot{{$slot->id}}[]" value="{{$session->id}}">
                                                   <?php $sessPapers = \App\Paper::where('conference_id',$conference->id)
                                                                                ->where('session_id',$session->id)
                                                                                ->count(); ?>
                                                    {{$session->name}}
                                                    @if($sessPapers<$session->capacity)
                                                        <label for="" class="ui label blue mini">{{$sessPapers-$session->capacity}}</label>
                                                    @elseif($sessPapers>$session->capacity)
                                                        <label for="" class="ui label red mini">+{{$sessPapers-$session->capacity}}</label>
                                                    @else
                                                        <label for="" class="ui label green mini"><i class="icon up thumbs"></i></label>
                                                    @endif
                                                    <br>
                                                    <div style="background-color:#ddd;width:91px;display:inline-block;margin-top:5px">
                                                        <span class="not" data-tooltip="{{$P['ASSIGN']}}"><i class="icon  newspaper show-papers-modal green"  data-id="{{$session->id}}"></i></span>
                                                        <span class="not"  data-tooltip="{{$P['EDIT']}}"><i class="icon  edit show-session-edit-modal blue" data-id="{{$session->id}}"></i></span>
                                                        <span class="not"  data-tooltip="{{$P['SHOW']}}"><i class="icon  eye show-session-modal black" data-id="{{$session->id}}"></i></span>
                                                        <span class="not"  data-tooltip="{{$P['DELETE']}}"><i class="icon  trash red delete-slot-btn" data-url="/conferences/{{$conference->confAcronym}}/{{$conference->confEdition}}/sessions/{{$session->id}}/delete"></i></span>
                                                    </div>
                                                </a>
                                        @endforeach
                                    </div>
                            <?php $begin = date('G:i', strtotime($slot->begin)) ?>
                            <?php $end = date('G:i', strtotime($slot->end)) ?>
                            
                            @if($slot->type == 1 )
                            <span class="slotName">
                            <label for="" class="ui label">{{$slot->name}}: {{$begin}} - {{$end}}</label>
                            </span>
                            @else

                             <span class="slotName">
                            <label for="" class="ui label">{{$begin}} - {{$end}}</label>
                            </span>

                            @endif
                            <span class="addButton"  >
                                <span data-tooltip="{{$P['CR_SESS_BTN']}}"><i class="icon square plus show-sess-modal " data-id="{{$slot->id}}"></i></span>
                                <span data-tooltip="{{$P['EDIT']}}"><i class="icon  edit show-slot-edit-modal" data-id="{{$slot->id}}"></i></span>
                                <span class="not"  data-tooltip="{{$P['SHOW']}}"><i class="icon  eye show-slot-modal basic" data-id="{{$slot->id}}"></i></span>
                                <span data-tooltip="{{$P['DELETE']}}"><i class="icon  trash red delete-slot-btn" data-url="/conferences/{{$conference->confAcronym}}/{{$conference->confEdition}}/slots/{{$slot->id}}/delete"></i></span>
                            </span>
                            </span>
                        </div>
                    </td>
                    
           
                @endif
                    
            @endforeach

         </tr>
         @endforeach


    
</tbody>

</table>

</form>
@endif

<div class="ui modal modal-create-session"  >
    <i class="close icon"></i>
    <div class="header">
        {{$P['CR_SESS_BTN']}}
    </div>

    <div class="content" style="padding-bottom:20px;height:600px;overflow: scroll;overflow-x: hidden;">
        
    <form class="ui form" id="formAddSession" method="post" action="{{route('conferences.sessions.store',[$conference->confAcronym,$conference->confEdition])}}">
        {{csrf_field()}}
        <input type="hidden" name="slotId" class="slotId">
        <div class="field required">
            <label for="name">{{$P['NAME']}}</label>
            <input type="text" id="name" name="name" required>
        </div>

        <div class="field required">
            <label for="room">{{$P['ROOM']}}</label>
            <input type="text" id="room" name="room" required>
        </div>

        <div class="field required">
            <label for="comment">{{$P['COMMENT']}}</label>
            <textarea name="comment" id="comment" cols="30" rows="10"></textarea>
        </div>

        <div class="field required">
            <label for="capacity">{{$P['CAP']}}</label>
            <input type="number" id="capacity" name="capacity" required>
        </div>

        <div class="field required">
            <label for="chair">{{$P['CHAIR']}}</label>
            <select name="chair" id="chair" required>
                <option value>{{$P['SELECT_P']}}</option>
                @foreach($chairs as $chair)
                    <option value="{{$chair->id}}">{{$chair->first_name}} {{$chair->last_name}}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" id="submitCrSess" style="display:none"></button>

    </form>
       
    </div>

    <div class="actions">
        <div class="ui button deny">{{$P['BTN_CANC']}}</div>
        <button class="button ui green" id="clickCrSess" >{{$P['BTN_ADD']}}</button>
    </div>
    </div>



    <div class="ui modal modal-create-slot"  >
    <i class="close icon"></i>
    <div class="header header-create-slot"></div>

    <div class="content" >
        
    <form class="ui form" id="formAddSlot" method="post" action="{{route('conferences.slots.store',[$conference->confAcronym,$conference->confEdition])}}">
        {{csrf_field()}}
        <div class="field required">
            <label for="nameSlot">{{$P['NAME']}}</label>
            <input type="text" id="nameSlot" name="name" required>
        </div>

        <div class="field required">
            <label for="colorSlot">{{$P['COLOR']}}</label>
            <input id="colorSlot" class="jscolor" name="color" required>
        </div>

        <div class="field required">
            <label for="typeSlot">{{$P['TYPE']}}</label>
            <select name="type" id="typeSlot" required>
                <option value class="placeholder" >{{$P['SELECT_P']}}</option>
                <option value="1">{{$P['WORK']}}</option>
                <option value="2">{{$P['BREAK']}}</option>
            </select>
        </div>


        <div class="field required">
            <label for="dateSlotCrVal">{{$P['DATE']}}</label>
                <div class="ui calendar" id="dateSlotCr">
                <div class="ui input left icon">
                    <i class="calendar icon"></i>
                    <input name="date" id="dateSlotCrVal" type="text" required>
                </div>
                </div>
            </div>
       

        <div class="field required">
            <label for="begin">{{$P['BEGIN']}}</label>
                <div class="ui calendar" id="begin">
                <div class="ui input left icon">
                    <i class="calendar icon"></i>
                    <input name="begin" id="begin" type="text" required>
                </div>
                </div>
            </div>

        <div class="field required">
            <label for="end">{{$P['END']}}</label>
                <div class="ui calendar" id="end">
                <div class="ui input left icon">
                    <i class="calendar icon"></i>
                    <input name="end" id="end" type="text" required>
                </div>
                </div>
            </div>


       

        <button type="submit" id="submitCrSlot" style="display:none"></button>

    </form>
       
    </div>

    <div class="actions">
        <div class="ui button deny">{{$P['BTN_CANC']}}</div>
        <button class="button ui green" id="clickCrSlot" >{{$P['BTN_ADD']}}</button>
    </div>
    </div>



    <div class="ui modal modal-create-slot-date"  >
    <i class="close icon"></i>
    <div class="header header-slot-date"></div>

    <div class="content" >
        
    <form class="ui form" id="formAddSlotDate" method="post" action="{{route('conferences.slots.store',[$conference->confAcronym,$conference->confEdition])}}">
        {{csrf_field()}}
        <input type="hidden" name="dateSlot" class="date_slot">
        <div class="field required">
            <label for="nameSlot">{{$P['NAME']}}</label>
            <input type="text" id="nameSlot" name="name" required>
        </div>


        <div class="field required">
            <label for="colorSlot">{{$P['COLOR']}}</label>
            <input id="colorSlot" class="jscolor" name="color" required>
        </div>

        <div class="field required">
            <label for="typeSlot">{{$P['TYPE']}}</label>
            <select name="type" id="typeSlot" required>
                <option value class="placeholder" >{{$P['SELECT_P']}}</option>
                <option value="1">{{$P['WORK']}}</option>
                <option value="2">{{$P['BREAK']}}</option>
            </select>
        </div>

       

        <div class="field required">
            <label for="begin">{{$P['BEGIN']}}</label>
                <div class="ui calendar" id="begin">
                <div class="ui input left icon">
                    <i class="calendar icon"></i>
                    <input name="begin" id="begin" type="text" required>
                </div>
                </div>
            </div>

        <div class="field required">
            <label for="end">{{$P['END']}}</label>
                <div class="ui calendar" id="end">
                <div class="ui input left icon">
                    <i class="calendar icon"></i>
                    <input name="end" id="end" type="text" required>
                </div>
                </div>
            </div>


       

        <button type="submit" id="submitCrSlotDate" style="display:none"></button>

    </form>
       
    </div>

    <div class="actions">
        <div class="ui button deny">{{$P['BTN_CANC']}}</div>
        <button class="button ui green" id="clickCrSlotDate" >{{$P['BTN_ADD']}}</button>
    </div>
    </div>


    <div class="ui modal modal-edit-slot"  >
    <i class="close icon"></i>
    <div class="header header-slot-edit"></div>

    <div class="content" >
        
    <form class="ui form" id="formEditSlot" method="post" action="{{route('conferences.slots.update',[$conference->confAcronym,$conference->confEdition])}}">
        {{csrf_field()}}
        <input type="text" name="slotId" id="slotId"class="id_slot">
        <div class="field required">
            <label for="nameSlotEdit">{{$P['NAME']}}</label>
            <input type="text" id="nameSlotEdit" name="name" required>
        </div>

        <div class="field required">
            <label for="colorSlotdit">{{$P['COLOR']}}</label>
            <input id="colorSlotEdit" class="jscolor" name="color" required>
        </div>

        <div class="field required">
            <label for="typeSlotEdit">{{$P['TYPE']}}</label>
            <select name="type" id="typeSlotEdit" required>
                <option value class="placeholder" >{{$P['SELECT_P']}}</option>
                <option value="1">{{$P['WORK']}}</option>
                <option value="2">{{$P['BREAK']}}</option>
            </select>
        </div>

        <div class="field required">
            <label for="dateSlotEditVal">{{$P['DATE']}}</label>
                <div class="ui calendar" id="dateSlotEdit">
                <div class="ui input left icon">
                    <i class="calendar icon"></i>
                    <input name="date" id="dateSlotEditVal" type="text" required>
                </div>
                </div>
            </div>


        <div class="field required">
            <label for="beginEdit">{{$P['BEGIN']}}</label>
                <div class="ui calendar" id="begin">
                <div class="ui input left icon">
                    <i class="calendar icon"></i>
                    <input name="begin" id="beginEdit" type="text" required>
                </div>
                </div>
            </div>

        <div class="field required">
            <label for="endEdit">{{$P['END']}}</label>
                <div class="ui calendar" id="end">
                <div class="ui input left icon">
                    <i class="calendar icon"></i>
                    <input name="end" id="endEdit" type="text" required>
                </div>
                </div>
            </div>


       

        <button type="submit" id="submitEditSlot" style="display:none"></button>

    </form>
       
    </div>

    <div class="actions">
        <div class="ui button deny">{{$P['BTN_CANC']}}</div>
        <button class="button ui green" id="clickEditSlot" >{{$P['EDIT']}}</button>
    </div>
    </div>


    <div class="ui modal modal-edit-session"  >
    <i class="close icon"></i>
        <div class="header header-session-edit"></div>
        <div class="content" style="padding-bottom:20px;height:600px;overflow: scroll;overflow-x: hidden;">
        
    <form class="ui form" id="formEditSession" method="post" action="{{route('conferences.sessions.update',[$conference->confAcronym,$conference->confEdition])}}">
        {{csrf_field()}}
        <input type="hidden" name="sessionId" class="sessionId" id="sessionId">
        <div class="field required">
            <label for="nameSessEdit">{{$P['NAME']}}</label>
            <input type="text" id="nameSessEdit" name="name" required>
        </div>

        <div class="field required">
            <label for="roomEdit">{{$P['ROOM']}}</label>
            <input type="text" id="roomEdit" name="room" required>
        </div>

        <div class="field required">
            <label for="commentEdit">{{$P['COMMENT']}}</label>
            <textarea name="comment" id="commentEdit" cols="30" rows="10"></textarea>
        </div>

        <div class="field required">
            <label for="capacityEdit">{{$P['CAP']}}</label>
            <input type="number" id="capacityEdit" name="capacity" required>
        </div>

        <div class="field required">
            <label for="chairEdit">{{$P['CHAIR']}}</label>
            <select name="chair" id="chairEdit" required>
                <option value>Select</option>
                @foreach($chairs as $chair)
                    <option value="{{$chair->id}}">{{$chair->first_name}} {{$chair->last_name}}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" id="submitEditSess" style="display:none"></button>

    </form>
       
    </div>

    <div class="actions">
        <div class="ui button deny">{{$P['BTN_CANC']}}</div>
        <button class="button ui green" id="clickEditSess" >{{$P['EDIT']}}</button>
    </div>
    </div>


    <div class="ui modal modal-show"  >
    <i class="close icon"></i>
    <div class="header header-show">
    </div>
    <div class="content" style="padding-bottom:20px;height:500px;overflow: scroll;overflow-x: hidden;"></div>
    <div class="actions btn-papers">
    </div>
    </div>

    <div class="ui modal modal-show-papers"  >
    <i class="close icon"></i>
    <div class="header header-papers">
    </div>
    <div class="content" style="padding-bottom:20px;height:700px;overflow: scroll;overflow-x: hidden;"></div>
    <div class="actions btn-papers">
    </div>
    </div>


</div>

@push('script')
<script>
function submitForm(formId,action)
{
    document.getElementById(formId).action = action;
    document.getElementById(formId).submit();

}
<?php $array=[] ?>
@foreach($slots as $slot)
    <?php $array[]='#'.$slot->id ;?>
@endforeach

<?php $ids = implode(',',$array); ?>
  $( function() {
    $( '{{$ids}}' ).sortable({
      connectWith: ".connectedSortable",
      scroll: 'true',
      //revert: "invalid",
      refreshPositions: true,
      cursor:'move',
      opacity:0.8,
      /*stop: function(event, ui) {
        position = ui.item.index()+1;
        console.log("New position: " + position);
        $(this).find('.sessPos').val(position);
    },*/
      
      /*start: function() {
        var th=$(this).find('.session');
        $(document).mousemove(function (e) {
            th.offset({ top: e.pageY-50, left: e.pageX-50 });
        }).click(function () {
            $(this).unbind("mousemove");
        });
    },*/
    cursorAt: { left: 85, top: 20 },
      helper: function(event, ui){
        var $clone =  $(ui).clone();
        $clone .css('position','absolute');
        return $clone.get(0);
    },
    /*stop: function(event, ui) {
            $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type:"POST",
            url : '{{route('conferences.slots.store',[$conference->confAcronym,$conference->confEdition])}}',
            async: true,
            success : function(response) {
                var data = response;
                return response;
            },
            error: function() {
                alert('Error occured');
            }
    
        });
        } ,*/

        
    }).disableSelection();
    $('{{$ids}}').sortable({ cancel: '.not' });

    
  } );
  </script>
<script>
$(document).ready(function(){
    $("tablle").each(function() {
        var $this = $(this);
        var newrows = [];
        $this.find("tr").each(function(){
            var i = 0;
            $(this).find("td").each(function(){
                i++;
                if(newrows[i] === undefined) { newrows[i] = $("<tr></tr>"); }
                newrows[i].append($(this));
            });
        });
        $this.find("tr").remove();
        $.each(newrows, function(){
            $this.append(this);
        });
    });



    $('.connectedSortable').hover(function(){
            val=$(this).prop("id");
            $(this).find('input').attr('name','slot'+val+'[]');
    });

    $('.session').hover(function(){
            val=$(this).parent('div').prop("id");
            $(this).find('input').attr('name','slot'+val+'[]');
    });

    $('.slot').hover(function(){
            val=$(this).find('.connectedSortable').prop("id");
            $(this).find('input').attr('name','slot'+val+'[]');
    });
/*
    var isDragging = false;
        $(".session")
        .mousedown(function() {
            isDragging = false;
        })
        .mousemove(function() {
            isDragging = true;
        })
        .mouseup(function() {
            var wasDragging = isDragging;
            isDragging = false;
            if (wasDragging) {
                console.log('drag');
            }else{
                 console.log('n drag');
            }
        });
        */

        

        $('.show-slot-date-modal').click(function () {
            $('.modal-create-slot-date').modal('show');
            $('.date_slot').val($(this).data('date'));
            $('.header-slot-date').empty();
            $('.header-slot-date').append('Add slot for day '+$(this).data('date'));

            $('#begin,#end').calendar({
                type: 'time',
                ampm: false,
            });

        });

        $('.create-slot').click(function () {
            $('.modal-create-slot').modal('show');
            $('.date_slot').val($(this).data('date'));
            $('.header-create-slot').empty();
            $('.header-create-slot').append('Create new slot');

            $('#begin,#end').calendar({
                type: 'time',
                ampm: false,
            });

            $('#dateSlotCr').calendar({
            type: 'date',
            monthFirst: true,
            formatter: {
                date: function(date, settings) {
                    if (!date) return '';
                    var day = date.getDate();
                    var month = date.getMonth() + 1;
                    var year = date.getFullYear();
                    return year + '-' + month + '-' + day;
                }
            }
        });
        });

        $('.show-slot-edit-modal').click(function () {
            $('.modal-edit-slot').modal('show');
            $('.date_slot').val($(this).data('date'));
            $('.header-slot-edit').empty();
            $('.header-slot-edit').append('Edit slot '+$(this).data('id'));

            $('#begin,#end').calendar({
                type: 'time',
                ampm: false,
            });

            $('#dateSlotEdit').calendar({
            type: 'date',
            monthFirst: true,
            formatter: {
                date: function(date, settings) {
                    if (!date) return '';
                    var day = date.getDate();
                    var month = date.getMonth() + 1;
                    var year = date.getFullYear();
                    return year + '-' + month + '-' + day;
                }
            }
        });

            id = $(this).data('id');

            $.ajax({
                type:"GET",
                url : '/conferences/{{$conference->confAcronym}}/{{$conference->confEdition}}/slots/'+id,
                async: true,
                success : function(response) {
                    var data = response;
                    console.log(data);
                    $('#slotId').val(id);
                    $('#nameSlotEdit').val(data.name);
                    $('#colorSlotEdit').val(data.color);
                    $('#colorSlotEdit').css("background-color", data.color);
                    
                    $('#typeSlotEdit').val(data.type);
                    $('#dateSlotEditVal').val(data.date);
                    $('#beginEdit').val(data.begin.slice(0, -3));
                    $('#endEdit').val(data.end.slice(0, -3));
                    return response;
                },
                error: function() {
                    alert('Error occured');
                }
    
            });

        });

        $('.show-session-modal').click(function () {
            $('.modal-show').modal({
                onVisible: function () {
                $( '.modal-show .content .sortPapers' ).sortable({
                    scroll: 'true',
                    refreshPositions: true,
                    cursor:'move',
                    opacity:0.8,
                    helper: function(event, ui){
                        var $clone =  $(ui).clone();
                        $clone .css('position','absolute');
                        return $clone.get(0);
                    },

                }).disableSelection();



                },
            }).modal('show');
            $('.slotId').val($(this).data('id'));
            $('.header-show').empty();
            $('.header-show').append('Show session '+$(this).data('id'));
            $('.modal-show .content').empty();
            $('.btn-papers').empty();
            $('.btn-papers').append('<div class="ui button deny">{{$P['BTN_CANC']}}</div>');
            $('.btn-papers').append('<button class="button ui green" id="clickPosPapers" >{{$P['SAVE']}}</button>');
             $('.modal-show .content').append('<div class="ui top attached tabular menu">'
                                                +'<a class="item active" data-tab="first">{{$P['INFOS']}}</a>'
                                                +'<a class="item" data-tab="second">{{$P['PAPERS']}}</a>'
                                                +'</div>');
                                                
            $('.modal-show .content').append('<div class="ui bottom attached tab segment active infos" data-tab="first"></div>'
                                    +'<div class="ui bottom attached tab segment listpapers" data-tab="second"></div>');


            id = $(this).data('id');

            $.ajax({
                type:"GET",
                url : '/conferences/{{$conference->confAcronym}}/{{$conference->confEdition}}/sessions/'+id,
                async: true,
                success : function(response) {
                    var data = response;
                    console.log(data);
                    var nbr = Math.abs(data.session.capacity - _.size(data.papers));
                    if(_.size(data.papers) > data.session.capacity){
                        $('.modal-show .content').prepend('<div class="ui message negative">'
                                                        +'<i class="close icon"></i>'
                                                        +'<div class="header">'
                                                        +'{{$P['EXCC']}} '+nbr+' papers'
                                                        +'</div>'
                                                        +'</div> ');
                    }else if(_.size(data.papers) < data.session.capacity){
                        $('.modal-show .content').prepend('<div class="ui message info">'
                                                        +'<i class="close icon"></i>'
                                                        +'<div class="header">'
                                                        +nbr+' {{$P['AVAIBLE']}} '
                                                        +'</div>'
                                                        +'</div> ');
                    }else{
                        $('.modal-show .content').prepend('<div class="ui message positive">'
                                                        +'<i class="close icon"></i>'
                                                        +'<div class="header">'
                                                        +'{{$P['COMPLETE']}}'
                                                        +'</div>'
                                                        +'</div> ');
                    }

                   
                    $('.modal-show .content .listpapers ').append('<form id="assignPapers" class="ui form" action="/conferences/{{$conference->confAcronym}}/{{$conference->confEdition}}/sessions/'+data.session.id+'/papers/update" method="POST">'
                                                    +'{{csrf_field()}}'
                                                    +'<button type="submit" style="display:none" id="submitPosPapers"></button>'
                                                    +'</form>');
                    $('.modal-show .content .listpapers #assignPapers').append('<label class="ui label">{{$P['DRAG_P']}}</label>'
                                                                    +'<div class="sortPapers" style="margin-top:20px">'
                                                                    +'</div>')
                    for(i=0;i<_.size(data.papers);i++){
                        $('.modal-show .content .listpapers #assignPapers .sortPapers').append('<div class="ui message" id="p'+data.papers[i].id+'">Paper: '+data.papers[i].id
                                                                        +'<input type="hidden" name="paperPos[]" value="'+data.papers[i].id+'">'
                                                                        +'<a href="#" style="margin-top:-6px" class="button ui red tiny pull-right deletePaper" data-tooltip="{{$P['DELETE']}}"><i class="icon trash"></i></a>'
                                                                        +'<label style="margin:-5px 5px 0 0"  class="ui label grey pull-right">{{$P['AUTHOR']}}: '+data.papers[i].first_name+' '+data.papers[i].last_name+' </label>'
                                                                        +'<label style="margin:-5px 5px 0 0"  class="ui label grey pull-right">{{$P['TOPIC']}}: '+data.papers[i].acronym+' </label> </div>');
                        if(data.papers[i].psLabel != null)  {
                            $('.modal-show .content .listpapers #assignPapers .sortPapers .message').append('<label style="margin:-5px 5px 0 0"  class="ui label grey pull-right">{{$P['STATUS']}}: '+data.papers[i].psLabel+' </label>');
                        }                                     
                    }
                    $('.modal-show .content .infos').append('<div class="ui message">{{$P['NAME']}}: '+data.session.name+'</div>'
                                                    +'<div class="ui message">{{$P['ROOM']}}: '+data.session.room+'</div>'
                                                    +'<div class="ui message">{{$P['COMMENT']}}: '+data.session.comment+'</div>'
                                                    +'<div class="ui message">{{$P['CAP']}}: '+data.session.capacity+'</div>'
                                                    +'<div class="ui message">{{$P['CHAIR']}}: '+data.session.first_name+' '+data.session.last_name+'</div>');
            
                    
                    return response;
                },
                error: function() {
                    alert('Error occured');
                }
    
            });

            $('.modal-show .menu .item').tab();

            $('.modal-show ').on('click','.close', function() {
                $(this).parent().fadeOut(300);
            });

        });

        $('.show-slot-modal').click(function () {
            $('.modal-show').modal('show');
            $('.slotId').val($(this).data('id'));
            $('.header-show').empty();
            $('.header-show').append('Show slot '+$(this).data('id'));
            $('.modal-show .content').empty();
            $('.btn-papers').empty();
            $('.btn-papers').append('<div class="ui button deny">{{$P['BTN_CANC']}}</div>');

            id = $(this).data('id');

            $.ajax({
                type:"GET",
                url : '/conferences/{{$conference->confAcronym}}/{{$conference->confEdition}}/slots/'+id,
                async: true,
                success : function(response) {
                    var data = response;
                    console.log(data);
                    $('.modal-show .content').append('<div class="ui message">{{$P['NAME']}}: '+data.name+'</div>');
                    if(data.type == 1){
                        $('.modal-show .content').append('<div class="ui message">{{$P['TYPE']}}: {{$P['WORK']}}</div>');
                    }else if(data.type == 2){
                        $('.modal-show .content').append('<div class="ui message">{{$P['TYPE']}}: {{$P['BREAK']}}</div>');
                    }
                    $('.modal-show .content').append('<div class="ui message">{{$P['DATE']}}: '+data.date+'</div>'
                                                    +'<div class="ui message">{{$P['BEGIN']}}: '+data.end+'</div>'
                                                    +'<div class="ui message">{{$P['END']}}End: '+data.end+'</div>');
                    return response;
                },
                error: function() {
                    alert('Error occured');
                }
    
            });

        });

         $('.show-papers-modal').click(function () {
            $('.modal-show-papers').modal({
                onVisible: function () {
                $( '.modal-show-papers .content .sortPapers' ).sortable({
                    scroll: 'true',
                    refreshPositions: true,
                    cursor:'move',
                    opacity:0.8,
                    helper: function(event, ui){
                        var $clone =  $(ui).clone();
                        $clone .css('position','absolute');
                        return $clone.get(0);
                    },

                }).disableSelection();

                 $( '.modal-show-papers .content #papers' ).sortable({
                    scroll: 'true',
                    refreshPositions: true,
                    cursor:'move',
                    opacity:0.8,
                    helper: function(event, ui){
                        var $clone =  $(ui).clone();
                        $clone .css('position','absolute');
                        return $clone.get(0);
                    },

                }).disableSelection();
                console.log('visible');


                },
            }).modal('show');
            $('.slotId').val($(this).data('id'));
            $('.header-papers').empty();
            $('.header-papers').append('Assgin papers to session '+$(this).data('id'));
            
            $('.btn-papers').empty();
            $('.btn-papers').append('<div class="ui button deny">{{$P['BTN_CANC']}}</div>');
            $('.btn-papers').append('<button class="button ui green" id="clickAssignPapers" >{{$P['SAVE']}}</button>');
            id = $(this).data('id');

            $.ajax({
                type:"GET",
                url : '/conferences/{{$conference->confAcronym}}/{{$conference->confEdition}}/sessions/'+id,
                async: true,
                success : function(response) {
                    var data = response;
                    $('.modal-show-papers .content').empty();
                    $('.modal-show-papers .content').append('<form id="assignPapers" class="ui form" action="/conferences/{{$conference->confAcronym}}/{{$conference->confEdition}}/sessions/'+data.session.id+'/papers" method="POST">'
                                                    +'{{csrf_field()}}'
                                                    +'</form>');
                    @if(count($papers) != null)
                        $('.modal-show-papers .content #assignPapers').append('<h3 class="header ui dividing">{{$P['SELECT']}}</h3>'
                                                                        +'<div id="papers" style="margin-bottom:8px">'
                                                                        +'<div class="fields">'
                                                                        +'<div class="field wide one">'
                                                                        +'<label for="position"></label>'
                                                                        +'<i class="icon maximize large"></i>'
                                                                        +'</div>'
                                                                        +'<div class="field wide thirteen required">'
                                                                        +'<select name="papers[]" id="paper" required>'
                                                                        +'<option value>{{$P['SELECT_P']}}</option>'
                                                                        +'@foreach($papers as $paper)'
                                                                        +'<option value="{{$paper->id}}">paper {{$paper->id}}</option>'
                                                                        +'@endforeach </select>'
                                                                        +'</div>'
                                                                        +'</div>'

                                                                        +'</div>'
                                                                        +'<a class="ui button addPaper">{{$P['ADD']}}</a>'
                                                                        +'<button style="display:none" id="submitAssignPapers"></button>');
                    @else
                        $('.modal-show-papers .content #assignPapers').append('<div class="ui message negative">{{$P['NO_PAPERS']}}</div>')
                    @endif                                    
                                                    
                    return response;
                },
                error: function() {
                    alert('Error occured');
                }

                });

                

    
            


        });



        $('.show-session-edit-modal').click(function () {
            $('.modal-edit-session').modal('show');
            $('.slotId').val($(this).data('id'));
            $('.header-session-edit').empty();
            $('.header-session-edit').append('Edit session '+$(this).data('id'));

   

            id = $(this).data('id');

            $.ajax({
                type:"GET",
                url : '/conferences/{{$conference->confAcronym}}/{{$conference->confEdition}}/sessions/'+id,
                async: true,
                success : function(response) {
                    var data = response;
                    console.log(data);
                    $('#sessionId').val(id);
                    $('#nameSessEdit').val(data.session.name);
                    $('#roomEdit').val(data.session.room);
                    $('#capacityEdit').val(data.session.capacity);
                    $('#commentEdit').empty();
                    $('#commentEdit').append(data.session.comment);
                    $('#chairEdit').val(data.session.user_id);
                    return response;
                },
                error: function() {
                    alert('Error occured');
                }
    
            });

        });

        $('.show-sess-modal').click(function () {
            $('.modal-create-session').modal('show');
            $('.slotId').val($(this).data('id'));
        });
        

        $('#clickCrSess').click(function() {
            $('#submitCrSess').click();
        });

        $('#clickCrSlotDate').click(function() {
            $('#submitCrSlotDate').click();
        });

        $('#clickCrSlot').click(function() {
            $('#submitCrSlot').click();
        });

        $('#clickEditSlot').click(function() {
            $('#submitEditSlot').click();
        });

        $('#clickEditSess').click(function() {
            $('#submitEditSess').click();
        });

        $('.modal-show-papers').on('click','#clickAssignPapers',function () {
            $('#submitAssignPapers').click();
        });

        $('.modal-show').on('click','#clickPosPapers',function () {
            $('#submitPosPapers').click();
        });

        $('.modal-show-papers').on('click','.addPaper',function () {
            $('#papers').append('<div class="fields">'
                                +'<div class="field wide one">'
                                +'<label for="position"></label>'
                                +'<i class="icon maximize large"></i>'
                                +'</div>'
                                +'<div class="field wide thirteen required">'
                                +'<select name="papers[]" id="paper" required>'
                                +'<option value>select</option>'
                                +'@foreach($papers as $paper)'
                                +'<option value="{{$paper->id}}">paper {{$paper->id}}</option>'
                                +'@endforeach </select>'
                                +'</div>'
                                +'<div class="field wide one required">'
                                +'<a href="#" class="button ui red deletePaper"><i class="icon delete"></i></a>'
                                +'</div>'
                                +'</div>');
        });

        $('.modal-show-papers').on('click','.deletePaper',function () {
            $(this).parent().parent().remove();
        });

        $('.modal-show').on('click','.deletePaper',function () {
            $(this).parent().remove();
        });


        $('.delete-slot-btn').on("click", function(e){
            e.preventDefault();
            var answer = window.confirm("veuillez supprimer ce item");
            if(answer){
                submitForm('main-form',$(this).data('url'))
                }
        });

         $('.delete-session-btn').on("click", function(e){
            e.preventDefault();
            var answer = window.confirm("veuillez supprimer ce item");
            if(answer){
                submitForm('main-form',$(this).data('url'))
                }
        });

        
   


});


</script>

@endpush

@endsection