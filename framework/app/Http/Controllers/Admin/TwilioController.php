<?php
/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\Bookings;
use App\Model\TwilioSettings;
use Hyvikk;
use Illuminate\Http\Request;

class TwilioController extends Controller {
	public function __construct() {
		$this->middleware('permission:Settings list');
	}

	public function test() {

		$booking_id = 64;
		$booking = Bookings::find($booking_id);

		$customer_name = $booking->customer->name;
		$customer_contact = $booking->customer->mobno;
		$driver_name = $booking->driver->name;
		$driver_contact = $booking->driver->phone;
		$pickup_address = $booking->pickup_addr;
		$destination_address = $booking->dest_addr;

		$pickup_datetime = date(Hyvikk::get('date_format') . " g:i A", strtotime($booking->pickup));
		$passengers = $booking->travellers;

		$search = ['$customer_name', '$customer_contact', '$driver_name', '$driver_contact', '$pickup_address', '$pickup_datetime', '$passengers', '$destination_address'];
		$replace = [$customer_name, $customer_contact, $driver_name, $driver_contact, $pickup_address, $pickup_datetime, $passengers, $destination_address];

		$id = Hyvikk::twilio('sid');
		$token = Hyvikk::twilio('token');

		$url = "https://api.twilio.com/2010-04-01/Accounts/$id/SMS/Messages";
		$from = Hyvikk::twilio('from');
		// customer sms notification
		$to = $booking->customer->mobno; // twilio trial verified number
		$body = str_replace($search, $replace, Hyvikk::twilio("customer_message"));

		$new_body = str_split($body, 120);
		$test2 = explode("\n", wordwrap($body, 120));
		// dd($test2);

		// foreach ($new_body as $row) {
		//     $data = array(
		//         'From' => $from,
		//         'To' => $to,
		//         'Body' => $row,
		//     );
		//     $post = http_build_query($data);
		//     $x = curl_init($url);
		//     curl_setopt($x, CURLOPT_POST, true);
		//     curl_setopt($x, CURLOPT_RETURNTRANSFER, true);
		//     curl_setopt($x, CURLOPT_SSL_VERIFYPEER, false);
		//     curl_setopt($x, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		//     curl_setopt($x, CURLOPT_USERPWD, "$id:$token");
		//     curl_setopt($x, CURLOPT_POSTFIELDS, $post);
		//     $y = curl_exec($x);
		//     curl_close($x);
		// }

		// driver sms notification
		$to_driver = $booking->driver->phone_code . $booking->driver->phone; // twilio trial verified number
		$msg_body = str_replace($search, $replace, Hyvikk::twilio("driver_message"));

		$new_msg_body = str_split($msg_body, 120);
		foreach ($new_msg_body as $row) {
			$data = array(
				'From' => "+447401280531",
				'To' => "+918320205588",
				'Body' => $row,
			);
			$post = http_build_query($data);
			$x = curl_init($url);
			curl_setopt($x, CURLOPT_POST, true);
			curl_setopt($x, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($x, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($x, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($x, CURLOPT_USERPWD, "$id:$token");
			curl_setopt($x, CURLOPT_POSTFIELDS, $post);
			$y = curl_exec($x);
			curl_close($x);

		}
		dd($y);
	}

	public function index() {
		return view('twilio.index');
	}

	public function update(Request $request) {
		TwilioSettings::where('name', 'sid')->update(['value' => $request->sid]);
		TwilioSettings::where('name', 'token')->update(['value' => $request->token]);
		TwilioSettings::where('name', 'from')->update(['value' => $request->from]);
		TwilioSettings::where('name', 'customer_message')->update(['value' => $request->customer_message]);
		TwilioSettings::where('name', 'driver_message')->update(['value' => $request->driver_message]);

		return back()->with(['msg' => 'Twilio settings updated successfully!']);
	}

}
