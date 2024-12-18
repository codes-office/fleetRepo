<?php

/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */
namespace App\Http\Controllers\Auth;

use App;
use App\Http\Controllers\Controller;
use Hyvikk;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller {

	use AuthenticatesUsers;

	protected $redirectTo = 'admin/';

	public function __construct() {
		App::setLocale(Hyvikk::get('language'));
		$this->middleware('guest', ['except' => 'logout']);
	}
}
