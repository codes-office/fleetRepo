<?php

/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

namespace App\Http\Middleware;

use App;
use Auth;
use Closure;
use Hyvikk;

class SetLocaleUser {
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next) {
		if (Auth::user() && Auth::user()->user_type == "C") {
			App::setLocale(Hyvikk::frontend('language'));
		} else {
			App::setLocale(Hyvikk::frontend('language'));
		}
		return $next($request);
	}
}
