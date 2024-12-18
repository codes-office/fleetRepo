<?php

/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyServicesRequest;
use App\Model\CompanyServicesModel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CompanyServicesController extends Controller {
	public function __construct() {
		// $this->middleware(['role:Admin']);
		$this->middleware('permission:Settings list');
	}

	public function index() {
		$data = CompanyServicesModel::orderBy('id', 'desc')->get();
		return view('company_services.index', compact('data'));
	}

	public function create() {
		return view('company_services.create');
	}

	public function store(CompanyServicesRequest $request) {
		// dd($request->all());
		$data = CompanyServicesModel::create(['title' => $request->title, 'description' => $request->description]);
		$file = $request->file('image');

		if ($request->hasFile('image') && $request->file('image')->isValid()) {
			$destinationPath = './uploads'; // upload path
			$extension = $file->getClientOriginalExtension();

			$fileName1 = Str::uuid() . '.' . $extension;

			$file->move($destinationPath, $fileName1);
			$data->image = $fileName1;
			$data->save();
		}
		return redirect('admin/company-services');
	}

	public function edit($id) {
		$data = CompanyServicesModel::find($id);
		return view('company_services.edit', compact('data'));
	}

	public function update(CompanyServicesRequest $request) {
		$data = CompanyServicesModel::find($request->id);
		$data->title = $request->title;
		$data->description = $request->description;
		$data->save();
		$file = $request->file('image');

		if ($request->hasFile('image') && $request->file('image')->isValid()) {
			$destinationPath = './uploads'; // upload path
			$extension = $file->getClientOriginalExtension();

			$fileName1 = Str::uuid() . '.' . $extension;

			$file->move($destinationPath, $fileName1);
			$data->image = $fileName1;
			$data->save();
		}
		return redirect('admin/company-services');
	}

	public function destroy(Request $request) {
		CompanyServicesModel::find($request->id)->delete();
		return redirect('admin/company-services');
	}

	public function bulk_delete(Request $request) {
		CompanyServicesModel::whereIn('id', $request->ids)->delete();
		return back();
	}
}
