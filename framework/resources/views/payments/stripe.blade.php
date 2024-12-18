<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
	<script src="https://js.stripe.com/v3/"></script>
	<script type="text/javascript">
		var stripe = Stripe("{{ Hyvikk::payment('stripe_publishable_key') }}");
		stripe.redirectToCheckout({
		  // Make the id field from the Checkout Session creation API response
		  // available to this file, so you can provide it as parameter here
		  // instead of the  placeholder.
		  sessionId: '{{ $session_id }}'
		}).then(function (result) {
			alert(result.error.message);
		  // If `redirectToCheckout` fails due to a browser or network
		  // error, display the localized error message to your customer
		  // using `result.error.message`.
		});
	</script>
</body>
</html>