Welcome, {{ $first_name }} {{ $last_name }}


@component('mail::message')

Your paper has uploaded successfully in this conference{{ $confAcronym }} {{ $confEdition}} <br>

<strong>Paper information :</strong> <br>
Paper title : {{ $title }} <br>
Paper abstract : {!! $abstract !!} <br>
@endcomponent


Thanks,<br>
{{ config('app.name') }}
@endcomponent
