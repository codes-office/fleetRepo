<?php

/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookingQuotationRequest;
use App\Mail\DriverBooked;
use App\Mail\VehicleBooked;
use App\Model\Address;
use App\Model\BookingIncome;
use App\Model\BookingQuotationModel;
use App\Model\Bookings;
use App\Model\Hyvikk;
use App\Model\IncCats;
use App\Model\IncomeModel;
use App\Model\User;
use App\Model\VehicleModel;
use Carbon\Carbon;
use DataTables;
use Edujugon\PushNotification\PushNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class BookingQuotationController extends Controller {
	public function __construct() {
		// $this->middleware(['role:Admin']);
		$this->middleware('permission:BookingQuotations add', ['only' => ['create']]);
		$this->middleware('permission:BookingQuotations edit', ['only' => ['edit']]);
		$this->middleware('permission:BookingQuotations delete', ['only' => ['bulk_delete', 'destroy']]);
		$this->middleware('permission:BookingQuotations list');
	}
	public function reject(Request $request) {
		$quote = BookingQuotationModel::find($request->id);
		$quote->status = 1;
		$quote->save();
		return redirect('admin/booking-quotation')->with('msg', 'Booking quotation rejected successfully.');
	}
	public function index() {
		if (Auth::user()->group_id == null || Auth::user()->user_type == "S") {
			$vehicle_ids = VehicleModel::pluck('id')->toArray();
		} else {
			$vehicle_ids = VehicleModel::where('group_id', Auth::user()->group_id)->pluck('id')->toArray();
		}
		$data['data'] = BookingQuotationModel::whereIn('vehicle_id', $vehicle_ids)->orderBy('id', 'desc')->get();
		$data['types'] = IncCats::get();
		return view('booking_quotation.index', $data);
	}

	public function fetch_data(Request $request) {
		if ($request->ajax()) {
			$date_format_setting = (Hyvikk::get('date_format'))?Hyvikk::get('date_format'): 'd-m-Y';
			if (Auth::user()->group_id == null || Auth::user()->user_type == "S") {
				$vehicle_ids = VehicleModel::pluck('id')->toArray();
			} else {
				$vehicle_ids = VehicleModel::where('group_id', Auth::user()->group_id)->pluck('id')->toArray();
			}
			$bookings = BookingQuotationModel::whereIn('vehicle_id', $vehicle_ids)->orderBy('id', 'desc');
			$bookings->select('booking_quotation.*')
				->leftJoin('vehicles', 'booking_quotation.vehicle_id', '=', 'vehicles.id')

				->with(['customer']);

			return DataTables::eloquent($bookings)
				->addColumn('check', function ($user) {
					return '<input type="checkbox" name="ids[]" value="' . $user->id . '" class="checkbox" id="chk' . $user->id . '" onclick=\'checkcheckbox();\'>';
				})
				->addColumn('customer', function ($row) {
					return ($row->customer->name) ?? "";
				})
				->editColumn('pickup_addr', function ($row) {
					return str_replace(",", "<br/>", $row->pickup_addr);
				})
				->editColumn('dest_addr', function ($row) {
					// dd($row->dest_addr);
					return str_replace(",", "<br/>", $row->dest_addr);
				})
				->editColumn('pickup', function ($row) use ($date_format_setting) {
					$pickup = '';
					$pickup = [
						'display' => '',
						'timestamp' => '',
					];
					if (!is_null($row->pickup)) {
						$pickup = date($date_format_setting . ' h:i A', strtotime($row->pickup));
						return [
							'display' => date($date_format_setting . ' h:i A', strtotime($row->pickup)),
							'timestamp' => Carbon::parse($row->pickup),
						];
					}
					return $pickup;
				})
				->editColumn('dropoff', function ($row) use ($date_format_setting) {
					$dropoff = '';
					if (!is_null($row->dropoff)) {
						$dropoff = date($date_format_setting . ' h:i A', strtotime($row->dropoff));
						return [
							'display' => date($date_format_setting . ' h:i A', strtotime($row->dropoff)),
							'timestamp' => Carbon::parse($row->dropoff),
						];
					}
					return $dropoff;
				})

				->editColumn('status', function ($row) {
					if ($row->status == 1) {
						return '<span class="text-danger">' . __('fleet.rejected') . '</span>';
					} else {
						return '<a href="' . url('admin/booking-quotation/approve/' . $row->id) . '" class="btn btn-success" title="' . __('fleet.approve') . '"><i class="fa fa-check"></i></a> &nbsp;
                        <a class="btn btn-danger" data-id="' . $row->id . '" data-toggle="modal" data-target="#rejectModal" href="" title="' . __('fleet.reject') . '"><i class="fa fa-times"></i> </a>';
					}
				})
				->editColumn('payment', function ($row) {
					if ($row->payment == 1) {
						return '<span class="text-success"> ' . __('fleet.paid1') . ' </span>';
					} else {
						return '<span class="text-warning"> ' . __('fleet.pending') . ' </span>';
					}
				})
				->editColumn('tax_total', function ($row) {
					return ($row->tax_total)?Hyvikk::get('currency') . " " . $row->tax_total: "";
				})
				->addColumn('vehicle', function ($user) {
					return ($user->vehicle_id != null) ? $user->vehicle->make_name . '-' . $user->vehicle->model_name . '-' . $user->vehicle->license_plate : '';
				})
				->filterColumn('vehicle', function ($query, $keyword) {
					$query->whereRaw("CONCAT(vehicles.make_name, '-', vehicles.model_name, '-', vehicles.license_plate) LIKE ?", ["%$keyword%"]);
				})
				->filterColumn('ride_status', function ($query, $keyword) {
					$query->whereHas("metas", function ($q) use ($keyword) {
						$q->where('key', 'ride_status');
						$q->whereRaw("value like ?", ["%{$keyword}%"]);
					});
					return $query;
				})
				->addColumn('action', function ($user) {
					return view('booking_quotation.list-actions', ['row' => $user]);
				})
				->filterColumn('payment', function ($query, $keyword) {
					$query->whereRaw("IF(payment = 1 , '" . __('fleet.paid1') . "', '" . __('fleet.pending') . "') like ? ", ["%{$keyword}%"]);

				})
				->filterColumn('pickup', function ($query, $keyword) {
					$query->whereRaw("DATE_FORMAT(pickup,'%d-%m-%Y %h:%i %p') LIKE ?", ["%$keyword%"]);
				})
				->filterColumn('dropoff', function ($query, $keyword) {
					$query->whereRaw("DATE_FORMAT(dropoff,'%d-%m-%Y %h:%i %p') LIKE ?", ["%$keyword%"]);
				})
				->rawColumns(['payment', 'action', 'check', 'status', 'pickup_addr', 'dest_addr'])
				->make(true);
			//return datatables(User::all())->toJson();

		}
	}

	public function create() {
		$group_id = Auth::user()->group_id;
		$data['customers'] = User::where('user_type', 'C')->get();
		$data['drivers'] = [];
		$drivers = User::whereUser_type("D")->get();
		foreach ($drivers as $d) {
			if ($d->getMeta('is_active') == 1) {
				$data['drivers'][] = $d;
			}
		}
		$data['addresses'] = Address::where('customer_id', Auth::user()->id)->get();
		if ($group_id == null || Auth::user()->user_type == "S") {
			$data['vehicles'] = VehicleModel::whereIn_service("1")->get();
		} else {
			$data['vehicles'] = VehicleModel::where([['group_id', $group_id], ['in_service', '1']])->get();
		}
		return view('booking_quotation.create', $data);
	}

	public function store(BookingQuotationRequest $request) {
		// dd($request->all());
		$xx = $this->check_booking($request->get("pickup"), $request->get("dropoff"), $request->get("vehicle_id"));
		if ($xx) {
			$id = BookingQuotationModel::create($request->all())->id;

			Address::updateOrCreate(['customer_id' => $request->get('customer_id'), 'address' => $request->get('pickup_addr')]);

			Address::updateOrCreate(['customer_id' => $request->get('customer_id'), 'address' => $request->get('dest_addr')]);

			return redirect()->route("booking-quotation.index");
		} else {
			return redirect()->route("booking-quotation.create")->withErrors(["error" => "Selected Vehicle is not Available in Given Timeframe"])->withInput();
		}
	}

	public function edit($id) {
		$group_id = Auth::user()->group_id;
		$data['customers'] = User::where('user_type', 'C')->get();

		$data['drivers'] = [];
		$drivers = User::whereUser_type("D")->get();
		foreach ($drivers as $d) {
			if ($d->getMeta('is_active') == 1) {
				$data['drivers'][] = $d;
			}
		}
		$data['addresses'] = Address::where('customer_id', Auth::user()->id)->get();
		if ($group_id == null || Auth::user()->user_type == "S") {
			$data['vehicles'] = VehicleModel::whereIn_service("1")->get();
		} else {
			$data['vehicles'] = VehicleModel::where([['group_id', $group_id], ['in_service', '1']])->get();
		}
		$data['data'] = BookingQuotationModel::find($id);
		return view('booking_quotation.edit', $data);
	}

	public function update(BookingQuotationRequest $request) {
		// dd($request->all());
		$form_data = $request->all();
		unset($form_data['_method']);
		unset($form_data['_token']);
		$xx = $this->check_booking($request->get("pickup"), $request->get("dropoff"), $request->get("vehicle_id"));
		if ($xx) {
			BookingQuotationModel::where('id', $request->id)->update($form_data);
			return redirect('admin/booking-quotation');
		} else {
			return back()->withErrors(["error" => "Selected Vehicle is not Available in Given Timeframe"])->withInput();
		}
	}

	public function destroy(Request $request) {
		BookingQuotationModel::find($request->id)->delete();
		return back();
	}

	protected function check_booking($pickup, $dropoff, $vehicle) {
		$chk = Bookings::where("status", 0)->where('vehicle_id', $vehicle)->whereBetween('pickup', [$pickup, $dropoff])->orWhereBetween('dropoff', [$pickup, $dropoff])->get();

		if (count($chk) > 0) {
			return false;
		} else {
			return true;
		}

	}

	public function invoice($id) {
		$data = BookingQuotationModel::find($id);
		// dd($quote);
		return view('booking_quotation.receipt', compact('data'));
	}

	function print($id) {
		$data = BookingQuotationModel::find($id);
		return view('booking_quotation.print', compact('data'));
	}

	public function approve($id) {
		$group_id = Auth::user()->group_id;
		$data['customers'] = User::where('user_type', 'C')->get();
		$data['drivers'] = User::whereUser_type("D")->get();
		$data['addresses'] = Address::where('customer_id', Auth::user()->id)->get();
		$data['data'] = BookingQuotationModel::find($id);
		if (Auth::user()->group_id == null || Auth::user()->user_type == "S") {
			$data['vehicles'] = VehicleModel::whereRaw("in_service=1 and deleted_at is null and id not in (select vehicle_id from bookings where status=0 and deleted_at is null and  (DATE_SUB(pickup, INTERVAL 15 MINUTE) between '" . $data['data']->pickup . "' and '" . $data['data']->dropoff . "' or DATE_ADD(dropoff, INTERVAL 15 MINUTE) between '" . $data['data']->pickup . "' and '" . $data['data']->dropoff . "'  or dropoff between '" . $data['data']->pickup . "' and '" . $data['data']->dropoff . "'))")->get();
		} else {
			$data['vehicles'] = VehicleModel::whereRaw("in_service=1 and deleted_at is null and group_id=" . Auth::user()->group_id . " and id not in (select vehicle_id from bookings where status=0 and deleted_at is null and  (DATE_SUB(pickup, INTERVAL 15 MINUTE) between '" . $data['data']->pickup . "' and '" . $data['data']->dropoff . "' or DATE_ADD(dropoff, INTERVAL 15 MINUTE) between '" . $data['data']->pickup . "' and '" . $data['data']->dropoff . "'  or dropoff between '" . $data['data']->pickup . "' and '" . $data['data']->dropoff . "'))")->get();
		}

		return view('booking_quotation.approve', $data);
	}

	public function add_booking(BookingQuotationRequest $request) {
		// dd($request->all());
		$xx = $this->check_booking($request->get("pickup"), $request->get("dropoff"), $request->get("vehicle_id"));
		if ($xx) {
			$id = Bookings::create($request->all())->id;

			Address::updateOrCreate(['customer_id' => $request->get('customer_id'), 'address' => $request->get('pickup_addr')]);

			Address::updateOrCreate(['customer_id' => $request->get('customer_id'), 'address' => $request->get('dest_addr')]);

			$booking = Bookings::find($id);
			$booking->user_id = $request->get("user_id");
			$booking->driver_id = $request->get('driver_id');
			$dropoff = Carbon::parse($booking->dropoff);
			$pickup = Carbon::parse($booking->pickup);
			$diff = $pickup->diffInMinutes($dropoff);
			$booking->note = $request->get('note');
			$booking->duration = $diff;
			$booking->accept_status = 1; //0=yet to accept, 1= accept
			$booking->ride_status = "Upcoming";
			$booking->booking_type = 1;
			$booking->journey_date = date('d-m-Y', strtotime($booking->pickup));
			$booking->journey_time = date('H:i:s', strtotime($booking->pickup));
			$booking->receipt = 1;
			$booking->day = $request->get('day');
			$booking->mileage = $request->get('mileage');
			$booking->waiting_time = $request->get('waiting_time');
			$booking->date = date('Y-m-d');
			$booking->total = round($request->get('total'), 2);
			$booking->total_kms = $request->get('mileage');
			$booking->tax_total = round($request->get('tax_total'), 2);
			$booking->total_tax_percent = round($request->get('total_tax_percent'), 2);
			$booking->total_tax_charge_rs = round($request->total_tax_charge_rs, 2);
			$booking->save();

			$inc_id = IncomeModel::create([
				"vehicle_id" => $request->get("vehicle_id"),
				// "amount" => $request->get('total'),
				"amount" => round($request->get('tax_total'), 2),
				"user_id" => $request->get("customer_id"),
				"date" => date('Y-m-d'),
				"mileage" => $request->get("mileage"),
				"income_cat" => 1,
				"income_id" => $booking->id,
				"tax_percent" => round($request->get('total_tax_percent'), 2),
				"tax_charge_rs" => round($request->total_tax_charge_rs, 2),
			])->id;

			BookingIncome::create(['booking_id' => $booking->id, "income_id" => $inc_id]);

			$this->booking_notification($booking->id);
			if (Hyvikk::email_msg('email') == 1) {
				Mail::to($booking->customer->email)->send(new VehicleBooked($booking));
				Mail::to($booking->driver->email)->send(new DriverBooked($booking));
			}
			// browser notification to driver,admin,customer
			BookingQuotationModel::find($request->id)->delete();
			return redirect('admin/booking-quotation')->with('msg', 'Booking quotation approved successfully and added to bookings.');
		} else {
			return back()->withErrors(["error" => "Selected Vehicle is not Available in Given Timeframe"])->withInput();
		}
	}

	public function booking_notification($id) {

		$booking = Bookings::find($id);
		$data['success'] = 1;
		$data['key'] = "upcoming_ride_notification";
		$data['message'] = 'New Ride has been Assigned to you.';
		$data['title'] = "New Upcoming Ride for you !";
		$data['description'] = $booking->pickup_addr . " - " . $booking->dest_addr . " on " . date('d-m-Y', strtotime($booking->pickup));
		$data['timestamp'] = date('Y-m-d H:i:s');
		$data['data'] = array('rideinfo' => array(

			'booking_id' => $booking->id,
			'source_address' => $booking->pickup_addr,
			'dest_address' => $booking->dest_addr,
			'book_timestamp' => date('Y-m-d H:i:s', strtotime($booking->created_at)),
			'ridestart_timestamp' => null,
			'journey_date' => date('d-m-Y', strtotime($booking->pickup)),
			'journey_time' => date('H:i:s', strtotime($booking->pickup)),
			'ride_status' => "Upcoming"),
			'user_details' => array('user_id' => $booking->customer_id, 'user_name' => $booking->customer->name, 'mobno' => $booking->customer->getMeta('mobno'), 'profile_pic' => $booking->customer->getMeta('profile_pic')),
		);
		// dd($data);
		$driver = User::find($booking->driver_id);

		if ($driver->getMeta('fcm_id') != null && $driver->getMeta('is_available') == 1) {
			// PushNotification::app('appNameAndroid')
			//     ->to($driver->getMeta('fcm_id'))
			//     ->send($data);

			$push = new PushNotification('fcm');
			$push->setMessage($data)
				->setApiKey(env('server_key'))
				->setDevicesToken([$driver->getMeta('fcm_id')])
				->send();
		}

	}

	public function bulk_delete(Request $request) {
		BookingQuotationModel::whereIn('id', $request->ids)->delete();
		return back();
	}
}
