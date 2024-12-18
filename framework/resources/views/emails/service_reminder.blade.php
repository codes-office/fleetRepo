@component('mail::message')
# Service Reminder

Dear {{$user}},

{!! Hyvikk::email_msg('service_reminder') !!}
@php($date_format_setting=(Hyvikk::get('date_format'))?Hyvikk::get('date_format'):'d-m-Y')

	Vehicle: {{$vehicle}}
	Service Item: {{$detail}}
	Next due date: {{date($date_format_setting,strtotime($date))}}
	Remaining days: {{$diff_in_days}}



Thanks,<br>
{{ config('app.name') }}
@endcomponent
