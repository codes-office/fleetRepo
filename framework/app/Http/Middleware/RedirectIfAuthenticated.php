<?php

/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated {

	public function handle($request, Closure $next, $guard = null) {
		if (!$this->alreadyInstalled()) {
			return redirect("installation");
		}
		if (Auth::guard($guard)->check()) {
			return redirect('admin');
		}

		return $next($request);
	}

	public function alreadyInstalled() {
		return (file_exists(storage_path('installed')) && file_get_contents(storage_path('installed')) == "version6.5");
	}
}
