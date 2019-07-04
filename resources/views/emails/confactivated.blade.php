Welcome, {{ $first_name }} {{ $last_name }} <br>

Your Conferenceis approved successfully, you can manage it.<br>

<strong>Conference information :</strong><br>
Conference acronym : {{ $confAcronym }}<br>
Conference edition : {{ $confEdition}}<br>
Conference name : {{ $confName }}<br>

@component('mail::message')
# Introduction

Thanks so much for using our application 

@component('mail::button', ['url' => '{{ route("conferences.show", [$confAcronym, $confEdition] ) }}' ])
Your conference link
@endcomponent

@component('mail::panel', ['url' => ''])
Some quotes goes here
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent