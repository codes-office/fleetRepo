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
use App\Rules\UniqueContractNumber;
use App\Rules\UniqueEId;
use App\Rules\UniqueLicenceNumber;
use Illuminate\Foundation\Http\FormRequest;

class DriverRequest extends FormRequest {

	public function authorize() {
		if (Auth::user()->user_type == "S" || Auth::user()->user_type == "O") {
			return true;
		} else {
			abort(404);
		}
	}

	public function rules() {
		if ($this->request->has("edit")) {
			return [

				'first_name' => 'required',
				'last_name' => 'required',
				'address' => 'required',
				'phone' => 'required|numeric|digits_between:7,15',
				'email' => 'required|email|unique:users,email,' . \Request::get("id"),
				'start_date' => 'date|date_format:Y-m-d',
				'issue_date' => 'date|date_format:Y-m-d',
				'end_date' => 'nullable|date|date_format:Y-m-d',
				'exp_date' => 'required|date|date_format:Y-m-d',
				'driver_image' => 'nullable|mimes:jpg,png,jpeg|max:5120',
				'license_image' => 'nullable|mimes:jpg,png,jpeg|max:5120',
				'documents.*' => 'nullable|mimes:jpg,png,jpeg,pdf,doc,docx|max:5120',
				'driver_commision_type' => 'required',
				'driver_commision' => 'required|numeric',
			];
		} else {
			return [
				'emp_id' => ['required', new UniqueEId],
				'license_number' => ['required', new UniqueLicenceNumber],
				'contract_number' => ['required', new UniqueContractNumber],
				'first_name' => 'required',
				'last_name' => 'required',
				'address' => 'required',
				'phone' => 'required|numeric|digits_between:7,15',
				'email' => 'required|email|unique:users,email,' . \Request::get("id"),
				'exp_date' => 'required|date|date_format:Y-m-d|after:tomorrow',
				'start_date' => 'date|date_format:Y-m-d',
				'issue_date' => 'date|date_format:Y-m-d',
				'end_date' => 'nullable|date|date_format:Y-m-d',
				'driver_image' => 'nullable|mimes:jpg,png,jpeg|max:5120',
				'license_image' => 'nullable|mimes:jpg,png,jpeg|max:5120',
				'documents.*' => 'nullable|mimes:jpg,png,jpeg,pdf,doc,docx|max:5120',
				'driver_commision_type' => 'required',
				'driver_commision' => 'required|numeric',
			];
		}
	}
}
