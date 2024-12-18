<?php

/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReasonRequest;
use App\Model\ReasonsModel;
use Illuminate\Http\Request;

class ReasonController extends Controller {
	public function __construct() {
		// $this->middleware(['role:Admin']);
		$this->middleware('permission:Settings list');
	}

	public function index() {
		$reasons = ReasonsModel::orderBy('id', 'desc')->get();
		return view('cancel-reasons.index', compact('reasons'));
	}

	public function create() {
		return view('cancel-reasons.create');
	}

	public function store(ReasonRequest $request) {

		ReasonsModel::updateOrCreate(['reason' => $request->get('reason')]);
		return redirect()->route('cancel-reason.index');
	}

	public function edit($id) {
		$reason = ReasonsModel::find($id);
		return view('cancel-reasons.edit', compact('reason'));
	}

	public function update(ReasonRequest $request) {
		$reason = ReasonsModel::find($request->get('id'));
		$reason->reason = $request->get('reason');
		$reason->save();
		return redirect()->route('cancel-reason.index');
	}

	public function destroy(Request $request) {
		ReasonsModel::find($request->get('id'))->delete();
		return redirect()->route('cancel-reason.index');
	}

	public function bulk_delete(Request $request) {
		ReasonsModel::whereIn('id', $request->ids)->delete();
		return back();
	}

}
