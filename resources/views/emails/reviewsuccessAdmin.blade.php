Welcome, {{ $first }} {{ $last }}


@component('mail::message')

Your are the admin of the {{$confAcronym}}{{$confEdition}} conference, this reviewer: <br>
{{$first_name}} {{$last_name}} has reviewed the paper {{$idPaper}} in this conference <br>

@component('mail::panel', ['url' => ''])
<strong>Paper information :</strong> <br>
Paper title : {{ $title }} <br>
Paper abstract : {!! $abstract !!} <br>
@endcomponent


Thanks,<br>
{{ config('app.name') }}
@endcomponent
