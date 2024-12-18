@component('mail::message')
# Renew Registration

Dear {{$user}},

	@php ($to = \Carbon\Carbon::now())
    @php ($from = \Carbon\Carbon::createFromFormat('Y-m-d', $reg_date))
    @php ($diff_in_days = $to->diffInDays($from))

{!! Hyvikk::email_msg('registration') !!}
@php($date_format_setting=(Hyvikk::get('date_format'))?Hyvikk::get('date_format'):'d-m-Y')

	Vehicle: {{$vehicle}}
	Registration Expiry date: {{date($date_format_setting,strtotime($reg_date))}}
	Remaining days: {{$diff_in_days}}



Thanks,<br>
{{ config('app.name') }}
@endcomponent
