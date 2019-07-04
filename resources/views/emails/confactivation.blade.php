Welcome, {{ $first_name }} {{ $last_name }}


@component('mail::message')

Thanks so much for using our application This your conference informations <br>
You must attend max 4 hours until your conference verified and accepted by our system

@component('mail::panel', ['url' => ''])
<strong>Conference information :</strong> <br>
Conference acronym : {{ $confAcronym }} <br>
Conference edition : {{ $confEdition}} <br>
Conference name : {{ $confName }}
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent