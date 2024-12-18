<?php

/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceItem;
use App\Model\Hyvikk;
use App\Model\ServiceItemsModel;
use App\Model\ServiceReminderModel;
use Auth;
use Illuminate\Http\Request;

class ServiceItemsController extends Controller {

	public function __construct() {
		// $this->middleware(['role:Admin']);
		$this->middleware('permission:ServiceItems add', ['only' => ['create']]);
		$this->middleware('permission:ServiceItems edit', ['only' => ['edit']]);
		$this->middleware('permission:ServiceItems delete', ['only' => ['bulk_delete', 'destroy']]);
		$this->middleware('permission:ServiceItems list');
	}

	public function index() {
		$data['services'] = ServiceItemsModel::orderBy('id', 'desc')->get();
		return view('service_items.index', $data);
	}

	public function create() {
		return view('service_items.create');
	}

	public function store(ServiceItem $request) {
		if ($request->get('time1') != null) {
			$overdue_time = $request->get('time1');
		} else {
			$overdue_time = Hyvikk::get('time_interval');
		}
		ServiceItemsModel::create(['description' => $request->get('description'),
			'time_interval' => $request->get('chk1'),
			'overdue_time' => $overdue_time,
			'overdue_unit' => $request->get('interval1'),
			'meter_interval' => $request->get('chk2'),
			'overdue_meter' => $request->get('meter1'),
			'show_time' => $request->get('chk3'),
			'duesoon_time' => $request->get('time2'),
			'duesoon_unit' => $request->get('interval2'),
			'show_meter' => $request->get('chk4'),
			'duesoon_meter' => $request->get('meter2'),
			'user_id' => Auth::user()->id,
		]);
		return redirect()->route('service-item.index');
	}

	public function edit($id) {
		$data['service'] = ServiceItemsModel::find($id);
		return view('service_items.edit', $data);
	}

	public function update(ServiceItem $request) {
		if ($request->get('time1') != null) {
			$overdue_time = $request->get('time1');
		} else {
			$overdue_time = Hyvikk::get('time_interval');
		}
		$service = ServiceItemsModel::find($request->get('id'));
		$service->description = $request->get('description');
		$service->time_interval = $request->get('chk1');
		$service->overdue_time = $overdue_time;
		$service->overdue_unit = $request->get('interval1');
		$service->meter_interval = $request->get('chk2');
		$service->overdue_meter = $request->get('meter1');
		$service->show_time = $request->get('chk3');
		$service->duesoon_time = $request->get('time2');
		$service->duesoon_unit = $request->get('interval2');
		$service->show_meter = $request->get('chk4');
		$service->duesoon_meter = $request->get('meter2');
		$service->save();

		return redirect()->route('service-item.index');
	}

	public function destroy(Request $request) {
		ServiceItemsModel::find($request->get('id'))->delete();
		ServiceReminderModel::where('service_id', $request->get('id'))->delete();
		return redirect()->route('service-item.index');
	}

	public function bulk_delete(Request $request) {
		ServiceItemsModel::whereIn('id', $request->ids)->delete();
		ServiceReminderModel::whereIn('service_id', $request->ids)->delete();
		return back();
	}

}
