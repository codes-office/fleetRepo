<?php

/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Support\Facades\Auth as Login;

class Testcontroller extends Controller {

	public function fetch() {
		return Auth::user();
	}

	public function login() {
		Login::loginUsingId(1);
		$user = Login::user();
		return view('sample');
	}

}
