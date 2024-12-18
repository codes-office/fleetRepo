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

class ImportRequest extends FormRequest {

	public function authorize() {
		return (Auth::user());
	}

	public function rules() {
		return [
			'excel' => 'required|mimes:xlsx,xls',
		];
	}

	public function messages() {
		return [
			'excel.mimes' => 'File type must be Excel.',
		];
	}
}
