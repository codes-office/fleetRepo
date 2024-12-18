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

class UniqueEId implements Rule {

	public function passes($attribute, $value) {
		if (\Request::get("edit") == "1") {
			$emp_id = User::meta()
				->where(function ($query) use ($value) {
					$query->where('users_meta.key', '=', 'emp_id')
						->where('users_meta.value', '=', $value)
						->where('users_meta.user_id', '!=', \Request::get('id'));
				})->exists();

			if (!$emp_id) {
				return true;
			} else {
				return false;
			}
		} else {
			$emp_id = User::meta()
				->where(function ($query) use ($value) {
					$query->where('users_meta.key', '=', 'emp_id')
						->where('users_meta.value', '=', $value);
				})->exists();

			if (!$emp_id) {
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
