@extends('layouts.app')
<?php
$lang = Session::get('lang');
$P = parse_ini_file(base_path('language/'.$lang.'/PAPERS.ini'));
?>
<title>{{ $P['PAPER'] }} {{$paper->id}}</title>
@section('content')

<div class="container ui">
@if($conference->pivot->role == 'A')
  {!! Breadcrumbs::render('paper',$conference,$paper) !!}
@endif
    <h2 class="header dividing ui">{{ $P['PAPER'] }} {{$paper->id}} <a href="{{route('conferences.papers.download',[$conference->confAcronym,$conference->confEdition,$paper->id])}}">  <i class="icon download cloud"></i></a> </h2>
    <div class="panel">
        <div class="header">
            <h4>{{ $P['TITLE'] }}</h4>
        </div>
        <div class="body">
            {{$paper->title}}
        </div>
    </div>

    <div class="panel">
        <div class="header">
            <h4>{{ $P['KEY'] }}</h4>
            <?php
                //$keys = str_replace(' ',', ',$paper->keywords);
                $keys = str_replace(', ',',',$paper->keywords);
                $keys = explode(',',$keys);
                

             ?>
        </div>
        <div class="body">
            @for($i=0;$i<count($keys);$i++)
                @if($keys != '')
                    @if($conference->pivot->role == 'A')
                        <a href="{{route('conferences.papers.keys',[$conference->confAcronym,$conference->confEdition,$keys[$i] ])}}"class="ui teal tag label">#{{$keys[$i]}}</a>
                    @else
                        <a class="ui teal tag label">#{{$keys[$i]}}</a>
                    @endif
                @endif
            @endfor
        </div>
    </div>

    <div class="panel">
        <div class="header">
            <h4>{{ $P['H-TOPIC'] }}</h4>
        </div>
        <div class="body">
            <div class="ui bulleted list">
                @foreach($topics as $t)
                    <div class="item">
                        {{$t->label}}
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="panel">
        <div class="header">
            <h4>{{ $P['ABSTRACT'] }}</h4>
        </div>
        <div class="body">
            {!! $paper->abstract !!}
        </div>
    </div>

    <div class="panel">
        <div class="header">
            @if($conference->pivot->role == 'A')
                <h4>{{ $P['AUTHORS'] }} &nbsp &nbsp
                    <span class="ui tiny grey label ">
                        <i  class="icon user ui" ></i> {{ $P['USER_APP'] }}
                    </span>
                    <span class="ui tiny grey label ">
                        <i  class="icon mail ui" ></i> {{ $P['CONTACT'] }}
                    </span>
                </h4>
            @else
                 <h4>{{ $P['AUTHORS'] }} with you</h4>
            @endif
        </div>
        <div class="body">

            <div class="ui bulleted list">
            @if($conference->pivot->role == 'A')
                @for($i=0;$i<count($authors);$i++)
                    <?php $v=0 ?>
                    @for($j=0;$j<count($users);$j++)
                        @if($authors[$i]->email == $users[$j]->email)
                            <?php $v=1 ?>
                        @endif
                    @endfor
                    @if($v == 1)
                        <div class="item">
                            <a href="{{route('conferences.authors.papers',[$conference->confAcronym,$conference->confEdition,$authors[$i]->id ])}}" class="author-popup">{{ $authors[$i]->email }}
                            </a>
                            <span style="color:#888" data-tooltip="{{ $P['USER'] }}"><i class="icon user ui" ></i></span>
                            @if($authors[$i]->is_corresponding == 1)
                                <span style="color:#888" data-tooltip="{{ $P['CORR_AUT'] }}"><i class="icon mail ui" ></i></span>
                            @endif

                            <div class="ui flowing popup top left transition hidden">
                                <div class="ui list">
                                    <div class="item">{{ $P['FIRST'] }} : {{ $authors[$i]->first_name }}</div>
                                    <div class="item">{{ $P['LAST'] }} : {{ $authors[$i]->last_name }}</div>
                                    <div class="item">{{ $P['AFF'] }} : {{ $authors[$i]->affilation }}</div>
                                     <div class="item">{{ $P['COUNTRY'] }} : {{ $authors[$i]->country }}</div>
                                </div>

                            </div>
                        </div>
                    @else
                        <div class="item">
                            <a href="{{route('conferences.authors.papers',[$conference->confAcronym,$conference->confEdition,$authors[$i]->id ])}}">{{ $authors[$i]->email }}
                            </a>
                            @if($authors[$i]->is_corresponding == 1)
                                <span style="color:#888" data-tooltip="{{ $P['CORR_AUT'] }}"><i class="icon mail ui" ></i></span>
                            @endif

                        </div>
                    @endif
                @endfor

                @else
                    @for($i=0;$i<count($authors);$i++)
                        @if($authors[$i]->email != auth()->user()->email)
                            <div class="item">
                                    <span>
                                        <span class="label ui">{{$P['FIRST']}}: {{ $authors[$i]->first_name }} </span>
                                        <span class="label ui">{{$P['LAST']}}: {{ $authors[$i]->last_name }} </span>
                                        <span class="label ui">{{$P['EMAIL']}}: {{ $authors[$i]->email }} </span>

                                    </span>
                                </div>
                            @endif

                    @endfor
                @endif
            </div>

        </div>
    </div>

    <div class="panel">
        <div class="header">
            <h4>{{ $P['H-QU'] }}</h4>
        </div>
        <div class="body">
            <div class="ui bulleted list">
                @foreach($pquestions as $pq)
                    <div class="item">
                        {{$pq->question}}:
                        {{$pq->choice}}
                    </div>
                @endforeach
            </div>
        </div>
    </div>

</div>

@push('script')

<script>
    $('.author-popup').popup({
        popup : $('.flowing.popup'),
        on    : 'hover'
    });

</script>

@endpush

@endsection
