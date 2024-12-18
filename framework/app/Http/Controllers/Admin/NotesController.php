<?php

/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\NotesRequest;
use App\Model\NotesModel;
use App\Model\User;
use App\Model\VehicleModel;
use Auth;
use Illuminate\Http\Request;

class NotesController extends Controller {
	public function __construct() {
		// $this->middleware(['role:Admin']);
		$this->middleware('permission:Notes add', ['only' => ['create']]);
		$this->middleware('permission:Notes edit', ['only' => ['edit']]);
		$this->middleware('permission:Notes delete', ['only' => ['bulk_delete', 'destroy']]);
		$this->middleware('permission:Notes list');
	}

	public function index() {
		if (Auth::User()->user_type == "S") {
			$index['data'] = NotesModel::orderBy('id', 'desc')->get();
		} else {
			$index['data'] = NotesModel::where('customer_id', Auth::User()->id)->orderBy('id', 'desc')->get();
		}

		return view('notes.index', $index);
	}

	public function create() {
		if (Auth::user()->group_id == null || Auth::user()->user_type == "S") {
			$data['vehicles'] = VehicleModel::whereIn_service("1")->get();
		} else {
			$data['vehicles'] = VehicleModel::where('group_id', Auth::user()->group_id)->whereIn_service("1")->get();
		}
		$data['customers'] = User::where('user_type', '!=', 'C')->where('deleted_at', null)->get();
		return view('notes.create', $data);
	}

	public function store(NotesRequest $request) {
		// dd($request->all());
		$note = new NotesModel();
		$note->vehicle_id = $request->get('vehicle_id');
		$note->customer_id = $request->get('customer_id');
		$note->note = $request->get('note');
		$note->submitted_on = $request->get('submitted_on');
		$note->status = $request->get('status');
		$note->user_id = Auth::user()->id;
		$note->save();

		return redirect()->route('notes.index');
	}

	public function edit($id) {
		if (Auth::user()->group_id == null || Auth::user()->user_type == "S") {
			$index['vehicles'] = VehicleModel::whereIn_service("1")->get();
		} else {
			$index['vehicles'] = VehicleModel::where('group_id', Auth::user()->group_id)->whereIn_service("1")->get();
		}
		$index['customers'] = User::where('user_type', '!=', 'C')->get();
		$index['data'] = NotesModel::whereId($id)->first();
		return view('notes.edit', $index);
	}

	public function update(NotesRequest $request) {
		// dd($request->all());
		$note = NotesModel::find($request->get("id"));
		$note->vehicle_id = $request->get('vehicle_id');
		$note->customer_id = $request->get('customer_id');
		$note->note = $request->get('note');
		$note->submitted_on = $request->get('submitted_on');
		$note->status = $request->get('status');
		$note->save();

		return redirect()->route('notes.index');

	}

	public function destroy(Request $request) {
		NotesModel::find($request->get('id'))->delete();
		return redirect()->route('notes.index');
	}

	public function bulk_delete(Request $request) {
		NotesModel::whereIn('id', $request->ids)->delete();
		return back();
	}
}
