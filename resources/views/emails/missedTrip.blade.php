Attention {{ $e_contact->name }},

This is an automated alert from AdventureAlert.

Our system has detected that {{ $user->first_name }} has not checked back in from their trip titled {{ $trip->title }}. 
DO NOT PANIC! Occasionally users forget to check back in from a trip or are in a remote location and unable to aquire a data signal for their mobile device.

@if($e_contacts->count() > 1)
{{ $user->first_name }} elected you as one of their emergency contacts should something like this happen. Additionally they elected:

@foreach($e_contacts as $contact)
@if($e_contact->name != $contact->name)
{{ $contact->name }}
{{ $contact->email }}
{{ phone_format($contact->phone, 'CA') }}

@endif
@endforeach
@endif
Our first recommendation is that you attempt to get ahold of {{ $user->first_name }} by phone. If they do not answer, we suggest contacting their other Emergency Contacts to see if anyone else has heard from {{ $user->first_name }}. If no one has had any contact, please contact Search and Rescue at your discretion. Below are some helpful points of contact.

## HELPFUL SAR RESOURCES ##

According to our records, {{ $user->first_name }} checked in to their trip from the following location: http://maps.google.com/maps?z=12&t=h&q=loc:{{ $trip->checkIn_lat }}+{{ $trip->checkIn_long }}

Thank you,

The AdventureAlert Team