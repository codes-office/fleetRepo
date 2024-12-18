<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
<form method="post" action="{{ url('frontend/redirect-payment') }}">
{!! csrf_field() !!}
<input type="text" name="booking_id" placeholder="booking id">
<br>
<input type="radio" name="method" value="cash">cash
<input type="radio" name="method" value="stripe"> stripe
<input type="radio" name="method" value="razorpay"> razorpay
<br>
<input type="submit" name="submit" value="submit">
</form>
</body>
</html>