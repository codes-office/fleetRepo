<!DOCTYPE html>
<html lang=en  @if(Hyvikk::frontend('language') == "ar") dir="rtl" @endif>
<head>
	<meta charset=utf-8>
	@if(Hyvikk::api('api_key'))
	<meta name=mapApi content="{{Hyvikk::api('api_key')}}">
	@endif
	<meta http-equiv=X-UA-Compatible content="IE=edge">
	<meta name=viewport content="width=device-width,initial-scale=1">
	<meta name=API content={{ env('APP_URL')."/frontend" }}>
	<title>Fleet Frontend</title>
	<link rel=stylesheet href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900">
	<link rel=stylesheet href=https://cdn.jsdelivr.net/npm/@mdi/font@latest/css/materialdesignicons.min.css>
	<meta name="lang" content="{{ Hyvikk::frontend('language') }}" />
	<link href={{ asset('/css/app.css') }} rel=preload as=style>
	<link href={{ asset('/css/chunk-vendors.css') }} rel=preload as=style>
	<link href={{ asset('/js/app.js') }} rel=preload as=script>
	<link href={{ asset('/js/chunk-vendors.js') }} rel=preload as=script>
	<link href={{ asset('/css/chunk-vendors.css') }} rel=stylesheet>
	<link href={{ asset('/css/app.css') }} rel=stylesheet>

</head>

<body @if(Hyvikk::frontend('language') == "ar") dir="rtl" @endif>
	<noscript>
		<strong>We're sorry but fleet-ui-1 doesn't work properly without JavaScript enabled. Please enable it to continue.</strong>
	</noscript>
	<div id=app></div>
	<script src={{ asset('/js/chunk-vendors.js') }}></script>
	<script src={{ asset('/js/app.js') }}></script>

</body>
</html>