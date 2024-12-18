<?php
/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\MessageModel;

class ContactUs extends Controller {
	public function __construct() {
		$this->middleware('permission:Inquiries list');
	}

	public function index() {
		$data['messages'] = MessageModel::orderBy('id', 'desc')->get();
		return view('contactus', $data);

	}
}
