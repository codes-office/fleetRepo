<?php

/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\ReviewModel;

class ReviewRatings extends Controller {
	public function index() {
		$data['reviews'] = ReviewModel::orderBy('id', 'desc')->get();

		return view('reviews', $data);
	}
}
