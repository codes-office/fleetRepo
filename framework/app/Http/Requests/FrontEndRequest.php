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

class FrontEndRequest extends FormRequest {

	public function authorize() {
		if (Auth::user()->user_type == "S" || Auth::user()->user_type == "O") {
			return true;
		} else {
			return false;
		}
	}

	public function rules() {
		return [
			'about' => 'required|max:130',
			'customer_support' => 'required',
			'phone' => 'required|numeric',
			'email' => 'required',
			'about_description' => 'required',
			'about_title' => 'required',
			'faq_link' => 'nullable|url',
			'cancellation' => 'nullable|url',
			'terms' => 'nullable|url',
			'privacy_policy' => 'nullable|url',
		];
	}
}
