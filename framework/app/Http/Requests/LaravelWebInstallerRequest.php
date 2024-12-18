<?php

/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LaravelWebInstallerRequest extends FormRequest {

	public function authorize() {
		return true;
	}

	public function rules() {
		return [
			'purchase_code' => 'required',
			'hostname' => 'required',
			'username' => 'required',
			'database' => 'required',
		];
	}
}
