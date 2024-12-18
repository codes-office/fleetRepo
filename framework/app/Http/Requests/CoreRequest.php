<?php
/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */
namespace app\Http\Request;

use app\Helpers\Reply;
use Illuminate\Foundation\Http\FormRequest;

class CoreRequest extends FormRequest {

	protected function formatErrors(\Illuminate\Contracts\Validation\Validator $validator) {
		return Reply::formErrors($validator);
	}

}
