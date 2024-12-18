<?php

/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PartsCategoryRequest;
use App\Model\PartsCategoryModel;
use Auth;
use Illuminate\Http\Request;

class PartsCategoryController extends Controller {
	public function __construct() {
		// $this->middleware(['role:Admin']);
		$this->middleware('permission:PartsCategory add', ['only' => ['create']]);
		$this->middleware('permission:PartsCategory edit', ['only' => ['edit']]);
		$this->middleware('permission:PartsCategory delete', ['only' => ['bulk_delete', 'destroy']]);
		$this->middleware('permission:PartsCategory list');
	}

	public function index() {
		$records = PartsCategoryModel::orderBy('id', 'desc')->get();
		return view('parts_category.index', compact('records'));
	}

	public function create() {
		return view('parts_category.create');
	}

	public function store(PartsCategoryRequest $request) {
		PartsCategoryModel::create(['name' => $request->name, 'user_id' => Auth::user()->id]);
		return redirect()->route('parts-category.index');
	}

	public function edit($id) {
		$data = PartsCategoryModel::find($id);
		return view('parts_category.edit', compact('data'));
	}

	public function update(PartsCategoryRequest $request) {
		PartsCategoryModel::where('id', $request->id)->update(['name' => $request->name]);
		return redirect()->route('parts-category.index');
	}

	public function destroy(Request $request) {
		PartsCategoryModel::find($request->id)->delete();
		return redirect()->route('parts-category.index');
	}

	public function bulk_delete(Request $request) {
		PartsCategoryModel::whereIn('id', $request->ids)->delete();
		return back();
	}
}
