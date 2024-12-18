<?php

/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Auth;
use Illuminate\Foundation\Http\FormRequest;

class FuelRequest extends FormRequest {
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		return Auth::user();
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		return [
			'vehicle_id' => 'required',
			'start_meter' => 'required',
			'cost_per_unit' => 'required|numeric|gte:1|digits_between:1,10',
			'date' => 'required|date|date_format:Y-m-d',
			'qty' => 'required|numeric|gt:0',
			'vendor_name' => 'required_if:fuel_from,Vendor',
			'image' => 'nullable|mimes:jpg,jpeg,png|max:5120',
		];
	}
	public function messages() {
		return [
			'qty.gt' => 'Quantity cannot be Zero or less than 0',
			'cost_per_unit.gte' => 'Cost per unit should greater than 0',
		];
	}
}
