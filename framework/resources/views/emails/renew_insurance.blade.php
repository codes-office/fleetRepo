@component('mail::message')
# Renew Insurance

Dear {{$user}},

{!! Hyvikk::email_msg('insurance') !!}
@php($date_format_setting=(Hyvikk::get('date_format'))?Hyvikk::get('date_format'):'d-m-Y')
	Vehicle: {{$vehicle}}
	Insurance Expiry date: {{date($date_format_setting,strtotime($ins_date))}}
	Remaining days: {{$diff_in_days}}





Thanks,<br>
{{ config('app.name') }}
@endcomponent
