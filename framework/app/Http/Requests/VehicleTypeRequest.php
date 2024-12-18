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

class VehicleTypeRequest extends FormRequest {
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		if (Auth::user()->user_type == "S" || Auth::user()->user_type == "O") {
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
			'vehicletype' => 'required|unique:vehicle_types,vehicletype,' . \Request::get("id") . ',id,deleted_at,NULL',
			'displayname' => 'required',
			'icon' => 'nullable|mimes:jpg,png,jpeg|max:5120',
		];
	}

	public function messages() {
		return [
			'vehicletype.unique' => 'Vehicle type already exist',
		];
	}
}
