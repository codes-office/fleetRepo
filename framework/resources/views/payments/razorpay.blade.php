<!DOCTYPE html>
<html>
	<head>
		<title></title>
	</head>
	<body>
		<form method="POST" name="checkout" action="https://api.razorpay.com/v1/checkout/embedded">
			<input type="hidden" class="form-control" name="key_id" value="{{Hyvikk::payment('razorpay_key')}}">
			<input type="hidden" name="name" value="{{ Hyvikk::get('app_name') }}">
			<input type="hidden" name="amount" value="{{ $amount }}">
			<input type="hidden" name="order_id" value="{{$order_id}}">
			<input type="hidden" name="prefill[name]" value="{{ $name }}">
			<input type="hidden" name="prefill[contact]" value="{{ $contact }}">
			<input type="hidden" name="prefill[email]" value="{{ $email }}">
			<!-- if you want to add note or description related to payment then add here
			  <input type="hidden" name="notes[desc]" value="">
			 -->
			<input type="hidden" name="callback_url" value="{{ url('razorpay-success?booking_id='.$booking_id) }}">
			<input type="hidden" name="cancel_url" value="{{ url('razorpay-failed') }}">
			<input type="submit" name="submit" value="submit">
		</form>
	</body>
</html>