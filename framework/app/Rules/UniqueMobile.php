<?php
/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */
namespace App\Rules;

use App\Model\User;
use Illuminate\Contracts\Validation\Rule;

class UniqueMobile implements Rule {

	public function passes($attribute, $value) {
		// dd(Input::get("edit"));
		if (\Request::get("edit") == "1") {
			$mobno = User::meta()
				->where(function ($query) use ($attribute, $value) {
					$query->where('users_meta.key', '=', $attribute)
						->where('users_meta.value', '=', $value)
						->where('users_meta.user_id', '!=', \Request::get('user_id'))
						->where('users_meta.deleted_at', '=', null);

				})->exists();
			if (!$mobno) {
				return true;
			} else {
				return false;
			}
		} else {
			$mobno = User::meta()
				->where(function ($query) use ($attribute, $value) {
					$query->where('users_meta.key', '=', $attribute)
						->where('users_meta.value', '=', $value)
						->where('users_meta.deleted_at', '=', null);
				})->exists();
			if (!$mobno) {
				return true;
			} else {
				return false;
			}
		}
	}

	/**
	 * Get the validation error message.
	 *
	 * @return string
	 */
	public function message() {
		return 'The :attribute must be unique.';
	}
}
