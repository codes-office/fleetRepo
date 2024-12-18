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

class IncRequest extends FormRequest {
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		if (Auth::user()->user_type == "S" || Auth::user()->user_type == "O" || Auth::user()->user_type == "D") {
			return true;
		} else {
			abort(404);
		}
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		return [
			'income_type' => 'required',
			'revenue' => 'required|numeric|gt:0',
			'vehicle_id' => 'required',
			'mileage' => 'required|numeric|gt:0',
			'date' => 'required|date|date_format:Y-m-d',
		];
	}
	public function messages() {
		return [
			'revenue.gt' => 'Amount cannot be Zero or less than 0',
			'mileage.gt' => 'Mileage cannot be Zero or less than 0',
		];
	}
}
