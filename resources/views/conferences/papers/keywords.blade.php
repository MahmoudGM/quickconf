@extends('layouts.app')
<?php
$lang = Session::get('lang');
$P = parse_ini_file(base_path('language/'.$lang.'/PAPERS.ini'));
?>

<title>{{$keys}}</title>
@section('content')

<div class="ui container">
  {!! Breadcrumbs::render('paperKeys',$conference,$keys[0]) !!}
    @if(count($papers) == 0)
        <h2 style="margin-top:10 0 40px 0"><i class="frown icon"></i>{{$P['HAVE-NOT']}} <a class="ui teal label">#{{$keys}}</a> </h2>
    @else
    <h2 class="ui dividing header">
        {{$P['HAVE-T']}}
        <a class="ui teal label">#{{$keys}}</a>
    </h2>

    <div class="ui relaxed divided list" style="margin-top:10px 0 40px 0">
        @foreach($papers as $p)

        <div class="ui icon message">
          <i class="large book aligned icon"></i>
          <div class="content">
            <div class="header">
              <a style="display:inline"  href="{{route('conferences.papers.show',[$conference->confAcronym,$conference->confEdition,$p->id ])}}" class="header">{{$p->title}}</a>
              <a style="display:inline"  href="{{route('conferences.papers.download',[$conference->confAcronym,$conference->confEdition,$p->id])}}">  <i class="icon download cloud"></i></a>
            </div>
              <br>status <br>
                {{$P['BY']}}
              <a href="{{route('conferences.authors.papers',[$conference->confAcronym,$conference->confEdition,$p->authorId ])}}">{{ $p->email }}</a>
              <br>
            <span style="margin-top:5px" class="ui label tiny">Submitted {{ Carbon\Carbon::parse($p->created_at)->diffForHumans() }}</span>
            <br><label class="label ui pull-right">Topic: {{$p->label}}</label>
            </div>

        </div>

        @endforeach
   </div>
   @endif

</div>


@endsection
