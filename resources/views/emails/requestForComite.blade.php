
@component('mail::message')

Welcome, <br> <br>

@if($role == 'C')
You are invited to be a chair member of this conference  <br>
@else
You are invited to be a reviewer in this conference  <br>
@endif
@component('mail::panel', ['url' => ''])
<strong>Conference information :</strong> <br>
Conference acronym : {{ $confAcronym }} <br>
Conference edition : {{ $confEdition}} <br>
Conference name : {{ $confName }}
@endcomponent

Please respond this request, If you have an account in our application, <br> 
just login or create an account it's free. 

Regards,<br>
{{ config('app.name') }}.
@endcomponent