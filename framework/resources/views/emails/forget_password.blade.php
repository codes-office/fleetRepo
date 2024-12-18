@component('mail::message')

<h1><div style="color: #1a8aa1;">@lang('passwords.Hello')!</div></h1>
<p>@lang('passwords.You are receiving this email because we received a password reset request for your account.')</p>

@component('mail::button', ['url' =>url('reset-password/' . $token . '?email=' .$email), 'color' => 'primary'])@lang('passwords.Reset Password') @endcomponent

<p>@lang('passwords.This password reset link will expire in :count minutes.', ['count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire')])</p>
<p>@lang('passwords.If you did not request a password reset, no further action is required.')</p>

@lang('passwords.thank_you').<br>
<span style="color: #185869;"><strong>{{ config('app.name') }}</strong></span>
@endcomponent
