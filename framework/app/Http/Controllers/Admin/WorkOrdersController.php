<?php

/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\WorkOrderRequest;
use App\Model\Hyvikk;
use App\Model\Mechanic;
use App\Model\PartsModel;
use App\Model\PartsUsedModel;
use App\Model\VehicleModel;
use App\Model\Vendor;
use App\Model\WorkOrderLogs;
use App\Model\WorkOrders;
use Auth;
use DataTables;
use Illuminate\Http\Request;

class WorkOrdersController extends Controller {

	public function __construct() {
		// $this->middleware(['role:Admin']);
		$this->middleware('permission:WorkOrders add', ['only' => ['create']]);
		$this->middleware('permission:WorkOrders edit', ['only' => ['edit']]);
		$this->middleware('permission:WorkOrders delete', ['only' => ['bulk_delete', 'destroy']]);
		$this->middleware('permission:WorkOrders list');
	}

	public function logs() {
		if (Auth::user()->group_id == null || Auth::user()->user_type == "S") {
			$vehicle_ids = VehicleModel::pluck('id')->toArray();
		} else {
			$vehicle_ids = VehicleModel::where('group_id', Auth::user()->group_id)->pluck('id')->toArray();
		}
		$index['data'] = WorkOrderLogs::whereIn('vehicle_id', $vehicle_ids)->latest()->get();
		return view('work_orders.logs', $index);
	}

	public function index() {
		if (Auth::user()->group_id == null || Auth::user()->user_type == "S") {
			$vehicle_ids = VehicleModel::pluck('id')->toArray();
		} else {
			$vehicle_ids = VehicleModel::where('group_id', Auth::user()->group_id)->pluck('id')->toArray();
		}
		$index['data'] = WorkOrders::whereIn('vehicle_id', $vehicle_ids)->orderBy('id', 'desc')->get();
		return view('work_orders.index', $index);
	}

	public function fetch_data(Request $request) {
		if ($request->ajax()) {

			if (Auth::user()->group_id == null || Auth::user()->user_type == "S") {
				$vehicle_ids = VehicleModel::pluck('id')->toArray();
			} else {
				$vehicle_ids = VehicleModel::where('group_id', Auth::user()->group_id)->pluck('id')->toArray();
			}
			$work_orders = WorkOrders::select('work_orders.*')->whereIn('vehicle_id', $vehicle_ids)->orderBy('id', 'desc')
				->leftJoin('vehicles', 'work_orders.vehicle_id', '=', 'vehicles.id')

				->with(['mechanic', 'vendor', 'parts']);

			$date_format_setting = (Hyvikk::get('date_format'))?Hyvikk::get('date_format'): 'd-m-Y';
			return DataTables::eloquent($work_orders)
				->addColumn('check', function ($work_order) {
					$tag = '<input type="checkbox" name="ids[]" value="' . $work_order->id . '" class="checkbox" id="chk' . $work_order->id . '" onclick=\'checkcheckbox();\'>';
					return $tag;
				})

				->addColumn('vehicle_image', function ($work_order) {
					$src = ($work_order->vehicle_id != null)?asset('uploads/' . $work_order->vehicle->vehicle_image): asset('assets/images/vehicle.jpeg');

					return '<img src="' . $src . '" height="70px" width="70px">';
				})
				->addColumn('vehicle', function ($work_order) {
					return $work_order->vehicle->year . '<br/>' . $work_order->vehicle->make_name . '-' . $work_order->vehicle->model_name . '<br/>' .
					'<b>' . __('fleet.vin') . ': </b>' . $work_order->vehicle->vin . '<br/>' .
					'<b>' . __('fleet.plate') . ': </b>' . $work_order->vehicle->license_plate;

				})
				->filterColumn('vehicle', function ($query, $keyword) {
					$query->whereRaw("CONCAT(vehicles.year , '\n' , vehicles.make_name , '-' , vehicles.model_name , '\n' ,
                    '" . __('fleet.vin') . ": ' , vehicles.vin , '\n' ,
                    '" . __('fleet.plate') . ": ' , vehicles.license_plate) like ?", ["%$keyword%"]);
					return $query;
				})
				->addColumn('vendor', function ($work_order) {
					return ($work_order->vendor->name) ?? "";
				})
				->editColumn('price', function ($work_order) {
					return Hyvikk::get('currency') . $work_order->price;
				})
				->editColumn('parts_price_total', function ($work_order) {
					return Hyvikk::get('currency') . $work_order->parts->sum('total');
				})
				->editColumn('total', function ($work_order) {
					return Hyvikk::get('currency') . ($work_order->price + $work_order->parts->sum('total'));
				})
				->addColumn('mechanic', function ($work_order) {
					return ($work_order->mechanic->name) ?? "";
				})
				->editColumn('status', function ($work_order) {
					$tag = '';
					switch ($work_order->status) {
					case 'Completed':
						$tag = '<span class="text-success">' . $work_order->status . '</span>';
						break;
					case 'Pending':
						$tag = '<span class="text-warning">' . $work_order->status . '</span>';
						break;

					default:
						$tag = $work_order->status;
						break;
					}
					return $tag;
				})
				->editColumn('created_at', function ($work_order) use ($date_format_setting) {
					return date($date_format_setting, strtotime($work_order->created_at));
				})
				->editColumn('required_by', function ($work_order) use ($date_format_setting) {
					return date($date_format_setting, strtotime($work_order->required_by));
				})
				->filterColumn('created_at', function ($query, $keyword) {
					$query->whereRaw("DATE_FORMAT(work_orders.created_at,'%d-%m-%Y') LIKE ?", ["%$keyword%"]);
				})
				->filterColumn('required_by', function ($query, $keyword) {
					$query->whereRaw("DATE_FORMAT(required_by,'%d-%m-%Y') LIKE ?", ["%$keyword%"]);
				})

				->addColumn('action', function ($work_order) {
					return view('work_orders.list-actions', ['row' => $work_order]);
				})
				->rawColumns(['vehicle_image', 'vehicle', 'action', 'check', 'status'])
				->addIndexColumn()
				->make(true);
			//return datatables(User::all())->toJson();

		}
	}

	public function create() {
		if (Auth::user()->group_id == null || Auth::user()->user_type == "S") {
			$data['vehicles'] = VehicleModel::whereIn_service("1")->get();
		} else {
			$data['vehicles'] = VehicleModel::where('group_id', Auth::user()->group_id)->whereIn_service("1")->get();
		}

		$data['vendors'] = Vendor::get();
		$data['mechanic'] = Mechanic::get();
		$data['parts'] = PartsModel::where('stock', '>', 0)->where('availability', 1)->get();
		return view('work_orders.create', $data);
	}

	public function store(WorkOrderRequest $request) {
		$order = new WorkOrders();
		$order->required_by = $request->get('required_by');
		$order->vehicle_id = $request->get('vehicle_id');
		$order->vendor_id = $request->get('vendor_id');
		$order->mechanic_id = $request->get('mechanic_id');
		$order->status = $request->get('status');
		$order->description = $request->get('description');
		$order->meter = $request->get('meter');
		$order->price = $request->get('price');
		$order->note = $request->get('note');
		$order->user_id = Auth::user()->id;
		$order->save();
		$log = WorkOrderLogs::create([
			'created_on' => date('Y-m-d', strtotime($order->created_at)),
			'vehicle_id' => $order->vehicle_id,
			'vendor_id' => $order->vendor_id,
			'required_by' => $order->required_by,
			'status' => $order->status,
			'description' => $order->description,
			'meter' => $order->meter,
			'note' => $order->note,
			'price' => $order->price,
			'mechanic_id' => $order->mechanic_id,
			'type' => "Created",
		]);
		$parts = $request->parts;

		if ($parts != null) {
			foreach ($parts as $part_id => $qty) {

				$update_part = PartsModel::find($part_id);
				PartsUsedModel::create(['work_id' => $order->id, 'part_id' => $part_id, 'qty' => $qty, 'price' => $update_part->unit_cost, 'total' => $qty * $update_part->unit_cost]);
				$update_part->stock = $update_part->stock - $qty;
				$update_part->save();
				if ($update_part->stock == 0) {
					$update_part->availability = 0;
					$update_part->save();
				}
			}
		}
		$log->parts_price = $order->parts->sum('total');
		$log->save();
		return redirect()->route('work_order.index');
	}

	public function edit($id) {
		$index['parts'] = PartsModel::where('stock', '>', 0)->where('availability', 1)->get();
		$index['data'] = WorkOrders::whereId($id)->first();
		if (Auth::user()->group_id == null || Auth::user()->user_type == "S") {
			$index['vehicles'] = VehicleModel::whereIn_service("1")->get();
		} else {
			$index['vehicles'] = VehicleModel::where('group_id', Auth::user()->group_id)->whereIn_service("1")->get();
		}
		$index['vendors'] = Vendor::get();
		$index['mechanic'] = Mechanic::get();
		return view('work_orders.edit', $index);
	}

	public function update(WorkOrderRequest $request) {
		$order = WorkOrders::find($request->get("id"));
		$order->required_by = $request->get('required_by');
		$order->vehicle_id = $request->get('vehicle_id');
		$order->vendor_id = $request->get('vendor_id');
		$order->status = $request->get('status');
		$order->description = $request->get('description');
		$order->mechanic_id = $request->get('mechanic_id');
		$order->meter = $request->get('meter');
		$order->price = $request->get('price');
		$order->note = $request->get('note');
		$order->save();

		$log = WorkOrderLogs::create([
			'created_on' => date('Y-m-d', strtotime($order->created_at)),
			'vehicle_id' => $order->vehicle_id,
			'vendor_id' => $order->vendor_id,
			'required_by' => $order->required_by,
			'status' => $order->status,
			'description' => $order->description,
			'meter' => $order->meter,
			'note' => $order->note,
			'price' => $order->price,
			'mechanic_id' => $order->mechanic_id,
			'type' => "Updated",
			'user_id' => Auth::user()->id,
		]);
		$parts = $request->parts;

		if ($parts != null) {
			foreach ($parts as $part_id => $qty) {
				$update_part = PartsModel::find($part_id);

				PartsUsedModel::create(['work_id' => $order->id, 'part_id' => $part_id, 'qty' => $qty, 'price' => $update_part->unit_cost, 'total' => $qty * $update_part->unit_cost]);
				$update_part->stock = $update_part->stock - $qty;
				$update_part->save();
				if ($update_part->stock == 0) {
					$update_part->availability = 0;
					$update_part->save();
				}
			}
		}
		$log->parts_price = $order->parts->sum('total');
		$log->save();
		return redirect()->route('work_order.index');
	}

	public function destroy(Request $request) {
		WorkOrders::find($request->get('id'))->delete();
		return redirect()->back();
	}

	public function bulk_delete(Request $request) {
		WorkOrders::whereIn('id', $request->ids)->delete();
		return back();
	}

	public function remove_part($id) {
		$usedpart = PartsUsedModel::find($id);
		$part = PartsModel::find($usedpart->part_id);
		$part->stock = $part->stock + $usedpart->qty;
		$part->save();
		if ($part->stock > 0) {
			$part->availability = 1;
			$part->save();
		}
		$usedpart->delete();
		return back();
	}

	public function parts_used($id) {
		$order = WorkOrders::find($id);
		return view('work_orders.parts_used', compact('order'));
	}
}
