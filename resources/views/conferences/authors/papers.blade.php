@extends('layouts.app')
<?php
$lang = Session::get('lang');
$A = parse_ini_file(base_path('language/'.$lang.'/AUTHORS.ini'));
?>

<title>{{$A['PAPERS_OF']}} {{$author->first_name}} {{$author->last_name}} ({{count($papers)}}) </title>
@section('content')


<div class="ui container">
  {!! Breadcrumbs::render('papersOf',$conference,$author) !!}
    <h2 class="ui header dividing">{{$A['PAPERS_OF']}}  {{$author->first_name}} {{$author->last_name}} ({{count($papers)}}) </h2>

    <div class="ui relaxed divided list" style="margin:10px 0 40px 0">
        @foreach($papers as $p)

          <div class="ui icon message">
            <i class="large book aligned icon"></i>
            <div class="content">
              <div class="header">
                <a style="display:inline"  href="{{route('conferences.papers.show',[$conference->confAcronym,$conference->confEdition,$p->id ])}}" class="header">{{$p->title}}</a>
                <a style="display:inline"  href="{{route('conferences.papers.download',[$conference->confAcronym,$conference->confEdition,$p->id])}}">  <i class="icon download cloud"></i></a>
                <br>status
              </div>
              <span style="margin-top:5px" class="ui label tiny">Submitted {{ Carbon\Carbon::parse($p->created_at)->diffForHumans() }}</span>
              <br><label class="label ui pull-right" style="margin:12px 2px 4px 0">Topic: {{$p->label}}</label>
              @foreach ($authors as $auth)
                @if( ($auth->paper_id == $p->id) and ($auth->email != $author->email) )
                  <label class="label ui grey" style="margin:12px 5px 4px 0"><i class="user icon"></i>{{$auth->first_name}} {{$auth->last_name}}</label>
                @endif
              @endforeach
            </div>
          </div>


        @endforeach
   </div>

</div>


@endsection
