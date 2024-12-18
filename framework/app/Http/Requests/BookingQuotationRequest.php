<?php

/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

namespace App\Http\Requests;

use Auth;
use Illuminate\Foundation\Http\FormRequest;

class BookingQuotationRequest extends FormRequest {

	public function authorize() {
		if (Auth::user()->user_type == "S" || Auth::user()->user_type == "O") {
			return true;
		} else {
			abort(404);
		}

	}

	public function rules() {
		return [
			'customer_id' => 'required',
			'vehicle_id' => 'required',
			'pickup_addr' => 'required',
			'pickup' => 'required',
			'dest_addr' => 'required|different:pickup_addr',
			'dropoff' => 'required|different:pickup',
		];
	}

	public function messages() {
		return [
			'dest_addr.different' => 'Pickup address and drop-off address must be different',
			'dropoff.different' => 'Dropoff time and Pickup Time must be different',

		];
	}
}
