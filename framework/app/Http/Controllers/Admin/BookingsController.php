<?php

/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookingRequest;
use App\Mail\CustomerInvoice;
use App\Mail\DriverBooked;
use App\Mail\VehicleBooked;
use App\Mail\BookingCancelled;
use App\Model\Address;
use App\Model\BookingIncome;
use App\Model\BookingPaymentsModel;
use App\Model\Bookings;
use App\Model\Hyvikk;
use App\Model\IncCats;
use App\Model\IncomeModel;
use App\Model\ServiceReminderModel;
use App\Model\User;
use App\Model\Timeslot;
use App\Model\VehicleModel;
use App\Model\VehicleTypeModel;
use App\Model\ReasonsModel;
use Auth;
use Carbon\Carbon;
use DataTables;
use DB;
use Edujugon\PushNotification\PushNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;
use Illuminate\Support\Facades\Log;


class BookingsController extends Controller {
	public function __construct() {
		// $this->middleware(['role:Admin']);
		$this->middleware('permission:Bookings add', ['only' => ['create']]);
		$this->middleware('permission:Bookings edit', ['only' => ['edit']]);
		$this->middleware('permission:Bookings delete', ['only' => ['bulk_delete', 'destroy']]);
		$this->middleware('permission:Bookings list');
	}
	public function transactions() {
		$data['data'] = BookingPaymentsModel::orderBy('id', 'desc')->get();
		return view('bookings.transactions', $data);
	}

	public function merge_bookings(Request $request) {
		$startTime = $request->input('start_time'); // Example: '10:00'
		$endTime = $request->input('end_time');    // Example: '11:00'
		$date = $request->input('date');           // Example: '2024-12-16'
	
		$bookings = collect(); // Initialize as an empty collection
	
		if ($startTime && $endTime && $date) {
			$start = Carbon::createFromFormat('Y-m-d ', "{$date} {$startTime}");
			$end = Carbon::createFromFormat('Y-m-d ', "{$date} {$endTime}");
	
			// Filter bookings by date and time
			$bookings = Bookings::whereBetween('pickup', [$startTime, $endTime])->get();

		}
	
		return view('bookings.merge', [
			'bookings' => $bookings,
			'timeSlots' => $this->generateTimeSlots()
		]);
	}
		

private function generateTimeSlots() {
    $slots = [];
    for ($i = 0; $i < 24; $i++) {
        $start = str_pad($i, 2, '0', STR_PAD_LEFT) . ":00";
        $end = str_pad($i + 1, 2, '0', STR_PAD_LEFT) . ":00";
        $slots[] = ['start' => $start, 'end' => $end];
    }
    return $slots;
}


	public function transactions_fetch_data(Request $request) {
		if ($request->ajax()) {
			$date_format_setting = (Hyvikk::get('date_format'))?Hyvikk::get('date_format'): 'd-m-Y';
			$payments = BookingPaymentsModel::select('booking_payments.*')->with('booking.customer')->orderBy('id', 'desc');

			return DataTables::eloquent($payments)
				->addColumn('customer', function ($row) {
					return ($row->booking->customer->name) ?? "";
				})
				->editColumn('amount', function ($row) {
					return ($row->amount)?Hyvikk::get('currency') . " " . $row->amount: "";
				})
				->editColumn('created_at', function ($row) use ($date_format_setting) {
					$created_at = '';
					$created_at = [
						'display' => '',
						'timestamp' => '',
					];
					if (!is_null($row->created_at)) {
						$created_at = date($date_format_setting . ' h:i A', strtotime($row->created_at));
						return [
							'display' => date($date_format_setting . ' h:i A', strtotime($row->created_at)),
							'timestamp' => Carbon::parse($row->created_at),
						];
					}
					return $created_at;
				})
				->filterColumn('created_at', function ($query, $keyword) {
					$query->whereRaw("DATE_FORMAT(created_at,'%d-%m-%Y %h:%i %p') LIKE ?", ["%$keyword%"]);
				})
				->make(true);
			//return datatables(User::all())->toJson();

		}
	}

	public function index() {

		$data['types'] = IncCats::get();
		$data['reasons'] = ReasonsModel::get();
		return view("bookings.index", $data);
	}

		public function fetch_data(Request $request) {
			if ($request->ajax()) {
				$date_format_setting = (Hyvikk::get('date_format'))?Hyvikk::get('date_format'): 'd-m-Y';
				if (Auth::user()->user_type == "C") {
					$bookings = Bookings::where('customer_id', Auth::id())->latest();
				} elseif (Auth::user()->group_id == null || Auth::user()->user_type == "S") {
					$bookings = Bookings::latest();
				} else {
					$vehicle_ids = VehicleModel::where('group_id', Auth::user()->group_id)->pluck('id')->toArray();
					$bookings = Bookings::whereIn('vehicle_id', $vehicle_ids)->latest();
				}
				$bookings->select('bookings.*')
					->leftJoin('vehicles', 'bookings.vehicle_id', '=', 'vehicles.id')

					->leftJoin('bookings_meta', function ($join) {
						$join->on('bookings_meta.booking_id', '=', 'bookings.id')
							->where('bookings_meta.key', '=', 'vehicle_typeid');
					})
					->leftJoin('vehicle_types', 'bookings_meta.value', '=', 'vehicle_types.id')
					->with(['customer', 'metas']);

				return DataTables::eloquent($bookings)
					->addColumn('check', function ($user) {
						return '<input type="checkbox" name="ids[]" value="' . $user->id . '" class="checkbox" id="chk' . $user->id . '" onclick=\'checkcheckbox();\'>';
					})
					->addColumn('customer', function ($row) {
						return ($row->customer->name) ?? "";
					})
					->addColumn('travellers', function ($row) {
						return ($row->travellers) ?? "";
					})
					->addColumn('ride_status', function ($row) {
						return ($row->getMeta('ride_status')) ?? "";
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
						$dropoff = [
							'display' => '',
							'timestamp' => '',
						];
						if (!is_null($row->dropoff)) {
							$dropoff = date($date_format_setting . ' h:i A', strtotime($row->dropoff));
							return [
								'display' => date($date_format_setting . ' h:i A', strtotime($row->dropoff)),
								'timestamp' => Carbon::parse($row->dropoff),
							];
						}
						return $dropoff;
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
					->addColumn('vehicle', function ($row) {
						$vehicle_type = VehicleTypeModel::find($row->getMeta('vehicle_typeid'));
						return !empty($row->vehicle_id) ? $row->vehicle->make_name . '-' . $row->vehicle->model_name . '-' . $row->vehicle->license_plate : ($vehicle_type->displayname) ?? "";
					})
					->filterColumn('vehicle', function ($query, $keyword) {
						$query->whereRaw("CONCAT(vehicles.make_name , '-' , vehicles.model_name , '-' , vehicles.license_plate) like ?", ["%$keyword%"])
							->orWhereRaw("(vehicle_types.displayname like ? and bookings.vehicle_id IS NULL)", ["%$keyword%"]);
						return $query;
					})
					->filterColumn('ride_status', function ($query, $keyword) {
						$query->whereHas("metas", function ($q) use ($keyword) {
							$q->where('key', 'ride_status');
							$q->whereRaw("value like ?", ["%{$keyword}%"]);
						});
						return $query;
					})
					->filterColumn('tax_total', function ($query, $keyword) {
						$query->whereHas("metas", function ($q) use ($keyword) {
							$q->where('key', 'tax_total');
							$q->whereRaw("value like ?", ["%{$keyword}%"]);
						});
						return $query;
					})
					->addColumn('action', function ($user) {
						return view('bookings.list-actions', ['row' => $user]);
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
					->filterColumn('travellers', function ($query, $keyword) {
						$query->where("travellers",'LIKE','%'.$keyword.'%');
					})
					->rawColumns(['payment', 'action', 'check', 'pickup_addr', 'dest_addr'])
					->make(true);
				//return datatables(User::all())->toJson();

			}
		}

	public function receipt($id) {
		$data['id'] = $id;
		$data['i'] = $book = BookingIncome::whereBooking_id($id)->first();
		// $data['info'] = IncomeModel::whereId($book['income_id'])->first();
		$data['booking'] = Bookings::find($id);
		return view("bookings.receipt", $data);
	}

	function print($id) {
		$data['i'] = $book = BookingIncome::whereBooking_id($id)->first();
		// $data['info'] = IncomeModel::whereId($book['income_id'])->first();
		$data['booking'] = Bookings::whereId($id)->get()->first();
		return view("bookings.print", $data);
	}

	public function payment($id) {
		$booking = Bookings::find($id);
		$booking->payment = 1;
		$booking->payment_method = "cash";
		$booking->save();
		BookingPaymentsModel::create(['method' => 'cash', 'booking_id' => $id, 'amount' => $booking->tax_total, 'payment_details' => null, 'transaction_id' => null, 'payment_status' => "@lang('fleet.succeeded')"]);
		return redirect()->route('bookings.index');
	}

	public function complete_post(Request $request) {
		// dd($request->all());
		if($request->get('total')<1){
			return redirect()->back()->withErrors(["error" => "Invoice amount cannot be Zero or less than 0"]);
		}
		$booking = Bookings::find($request->get("booking_id"));

		$booking->setMeta([
			'customerId' => $request->get('customerId'),
			'vehicleId' => $request->get('vehicleId'),
			'day' => $request->get('day'),
			'mileage' => $request->get('mileage'),
			'waiting_time' => $request->get('waiting_time'),
			'date' => $request->get('date'),
			'total' => round($request->get('total'), 2),
			'total_kms' => $request->get('mileage'),
			'ride_status' => 'Ongoing',
			'tax_total' => round($request->get('tax_total'), 2),
			'total_tax_percent' => round($request->get('total_tax_charge'), 2),
			'total_tax_charge_rs' => round($request->total_tax_charge_rs, 2),
		]);
		if ($booking->driver->driver_commision != null) {
			$commision = $booking->driver->driver_commision;
			$amnt = $commision;
			if ($booking->driver->driver_commision_type == 'percent') {
				$amnt = ($booking->total * $commision) / 100;
			}
			// $driver_amount = round($booking->total - $amnt, 2);
			$booking->driver_amount = $amnt;
			$booking->driver_commision = $booking->driver->driver_commision;
			$booking->driver_commision_type = $booking->driver->driver_commision_type;
			$booking->save();
		}
		$booking->save();

		$id = IncomeModel::create([
			"vehicle_id" => $request->get("vehicleId"),
			// "amount" => $request->get('total'),
			"amount" => $request->get('tax_total'),
			"driver_amount" => $booking->driver_amount ?? $request->get('tax_total'),
			"user_id" => $request->get("customerId"),
			"date" => $request->get('date'),
			"mileage" => $request->get("mileage"),
			"income_cat" => $request->get("income_type"),
			"income_id" => $booking->id,
			"tax_percent" => $request->get('total_tax_charge'),
			"tax_charge_rs" => $request->total_tax_charge_rs,
		])->id;

		BookingIncome::create(['booking_id' => $request->get("booking_id"), "income_id" => $id]);
		$xx = Bookings::whereId($request->get("booking_id"))->first();
		// $xx->status = 1;
		$xx->receipt = 1;
		$xx->save();

		if (Hyvikk::email_msg('email') == 1) {
			Mail::to($booking->customer->email)->send(new CustomerInvoice($booking));
		}
		return redirect()->route("bookings.index");

	}

	public function complete($id) {

		$xx = Bookings::find($id);
		$xx->status = 1;
		$xx->ride_status = "Completed";
		$xx->save();
		return redirect()->route("bookings.index");
	}

	public function get_driver(Request $request) {
        //  dd($request->all());
        $from_date = $request->get("from_date");
        $to_date = $request->get("to_date");
		$driverInterval = Hyvikk::get('driver_interval').' MINUTE';
        $req_type = $request->get("req");
        if ($req_type == "new" || $request->req == 'true') {
			
			// This query is old version 
			// $q="SELECT id, name AS text
			// FROM users
			// WHERE user_type = 'D'
			// AND deleted_at IS NULL
			// AND id NOT IN (
			// 	SELECT DISTINCT driver_id
			// 	FROM bookings
			// 	WHERE deleted_at IS NULL
			// 	AND (
			// 		(dropoff BETWEEN DATE_ADD('" . $from_date . "', INTERVAL ".$driverInterval.") AND DATE_SUB('" . $to_date . "', INTERVAL ".$driverInterval."))
			// 		OR (pickup BETWEEN DATE_ADD('" . $from_date . "', INTERVAL ".$driverInterval.") AND DATE_SUB('" . $to_date . "', INTERVAL ".$driverInterval."))
			// 		OR (pickup < DATE_ADD('" . $from_date . "', INTERVAL ".$driverInterval.") AND dropoff > DATE_SUB('" . $to_date . "', INTERVAL ".$driverInterval."))
			// 	)
			// )";



			// Un comment this if the below query does not work

			// $q = "SELECT id, name AS text
			// FROM users
			// WHERE user_type = 'D'
			// AND deleted_at IS NULL
			// AND id NOT IN (
			// 	SELECT DISTINCT driver_id
			// 	FROM bookings
			// 	WHERE deleted_at IS NULL
			// 	AND (
			// 		(dropoff BETWEEN DATE_ADD('" . $from_date . "', INTERVAL " . $driverInterval . ") AND DATE_SUB('" . $to_date . "', INTERVAL " . $driverInterval . "))
			// 		OR (pickup BETWEEN DATE_ADD('" . $from_date . "', INTERVAL " . $driverInterval . ") AND DATE_SUB('" . $to_date . "', INTERVAL " . $driverInterval . "))
			// 		OR (pickup < DATE_ADD('" . $from_date . "', INTERVAL " . $driverInterval . ") AND dropoff > DATE_SUB('" . $to_date . "', INTERVAL " . $driverInterval . "))
			// 	)
			// )
			// AND id NOT IN (
			// 	SELECT DISTINCT driver_id
			// 	FROM bookings
			// 	WHERE deleted_at IS NULL
			// 	AND (
			// 		(pickup BETWEEN DATE_SUB('" . $from_date . "', INTERVAL " . $driverInterval . ") AND '" . $to_date . "')
			// 		OR (dropoff BETWEEN DATE_SUB('" . $from_date . "', INTERVAL " . $driverInterval . ") AND '" . $to_date . "')
			// 		OR (dropoff > '" . $to_date . "' AND pickup < DATE_ADD('" . $to_date . "', INTERVAL " . $driverInterval . "))
			// 	)
			// )";


			
			$q = "SELECT id, name AS text
			FROM users
			WHERE user_type = 'D'
			AND deleted_at IS NULL
			AND id NOT IN (
				SELECT DISTINCT driver_id
				FROM bookings
				WHERE deleted_at IS NULL
				AND cancellation = 0
				AND (
					(dropoff BETWEEN DATE_ADD('" . $from_date . "', INTERVAL " . $driverInterval . ") AND DATE_SUB('" . $to_date . "', INTERVAL " . $driverInterval . "))
					OR (pickup BETWEEN DATE_ADD('" . $from_date . "', INTERVAL " . $driverInterval . ") AND DATE_SUB('" . $to_date . "', INTERVAL " . $driverInterval . "))
					OR (pickup < DATE_ADD('" . $from_date . "', INTERVAL " . $driverInterval . ") AND dropoff > DATE_SUB('" . $to_date . "', INTERVAL " . $driverInterval . "))
				)
			)
			AND id NOT IN (
				SELECT DISTINCT driver_id
				FROM bookings
				WHERE deleted_at IS NULL
				AND cancellation = 0
				AND (
					(pickup BETWEEN DATE_SUB('" . $from_date . "', INTERVAL " . $driverInterval . ") AND '" . $to_date . "')
					OR (dropoff BETWEEN DATE_SUB('" . $from_date . "', INTERVAL " . $driverInterval . ") AND '" . $to_date . "')
					OR (dropoff > '" . $to_date . "' AND pickup < DATE_ADD('" . $to_date . "', INTERVAL " . $driverInterval . "))
				)
			)";


            $new = [];
            $d = collect(DB::select($q));
            foreach ($d as $ro) {
                array_push($new, array("id" => $ro->id, "text" => $ro->text));
            }
            $r['data'] = $new;
        } else {
            // dd('test');
            $id = $request->get("id");
            $current = Bookings::find($id);
            // $q = "SELECT id, name AS text
			// FROM users
			// WHERE user_type = 'D'
			// AND deleted_at IS NULL
			// AND id NOT IN (
			// 	SELECT DISTINCT driver_id
			// 	FROM bookings
			// 	WHERE deleted_at IS NULL
			// 	AND cancellation = 0
			// 	AND (
			// 		(dropoff BETWEEN DATE_ADD('" . $from_date . "', INTERVAL ".$driverInterval.") AND DATE_SUB('" . $to_date . "', INTERVAL ".$driverInterval."))
			// 		OR (pickup BETWEEN DATE_ADD('" . $from_date . "', INTERVAL ".$driverInterval.") AND DATE_SUB('" . $to_date . "', INTERVAL ".$driverInterval."))
			// 		OR (pickup < DATE_ADD('" . $from_date . "', INTERVAL ".$driverInterval.") AND dropoff > DATE_SUB('" . $to_date . "', INTERVAL ".$driverInterval."))
			// 	)
			// 	AND driver_id <> '" . $current->driver_id . "'
			// )";

			$q = "SELECT id, name AS text
			FROM users
			WHERE user_type = 'D'
			AND deleted_at IS NULL
			AND id NOT IN (
				SELECT DISTINCT driver_id
				FROM bookings
				WHERE deleted_at IS NULL
				AND cancellation = 0
				AND (
					(dropoff BETWEEN DATE_ADD('" . $from_date . "', INTERVAL " . $driverInterval . ") AND DATE_SUB('" . $to_date . "', INTERVAL " . $driverInterval . "))
					OR (pickup BETWEEN DATE_ADD('" . $from_date . "', INTERVAL " . $driverInterval . ") AND DATE_SUB('" . $to_date . "', INTERVAL " . $driverInterval . "))
					OR (pickup < DATE_ADD('" . $from_date . "', INTERVAL " . $driverInterval . ") AND dropoff > DATE_SUB('" . $to_date . "', INTERVAL " . $driverInterval . "))
				)
			)
			AND id NOT IN (
				SELECT DISTINCT driver_id
				FROM bookings
				WHERE deleted_at IS NULL
				AND cancellation = 0
				AND (
					(pickup BETWEEN DATE_SUB('" . $from_date . "', INTERVAL " . $driverInterval . ") AND '" . $to_date . "')
					OR (dropoff BETWEEN DATE_SUB('" . $from_date . "', INTERVAL " . $driverInterval . ") AND '" . $to_date . "')
					OR (dropoff > '" . $to_date . "' AND pickup < DATE_ADD('" . $to_date . "', INTERVAL " . $driverInterval . "))
				)
				AND driver_id <> '" . $current->driver_id . "'
			)";


            $d = collect(DB::select($q));
            $chk = $d->where('id', $current->driver_id);
            $r['show_error'] = "yes";
            if (count($chk) > 0) {
                $r['show_error'] = "no";
            }
            $new = array();
            foreach ($d as $ro) {
                if ($ro->id === $current->driver_id) {
                    array_push($new, array("id" => $ro->id, "text" => $ro->text, 'selected' => true));
                } else {
                    array_push($new, array("id" => $ro->id, "text" => $ro->text));
                }
            }
            $r['data'] = $new;
        }
        // dd($r);
        $new1 = [];
        foreach ($r['data'] as $r1) {
            $user = User::where('id', $r1['id'])->first();
            if ($user->getMeta('is_active') == 1) {
                // dd($r1);
                $new1[] = $r1;
            }
        }
        $r['data'] = $new1;
        return $r;
    }

	public function get_vehicle(Request $request) {

		$from_date = $request->get("from_date");
		$to_date = $request->get("to_date");
		$req_type = $request->get("req");
		$vehicleInterval = Hyvikk::get('vehicle_interval').' MINUTE';
		if ($req_type == "new") {
			$xy = array();
			if (Auth::user()->group_id == null || Auth::user()->user_type == "S") {
				// $q = "select id from vehicles where in_service=1 and deleted_at is null  and  id not in(select vehicle_id from bookings where  deleted_at is null  and ((dropoff between '" . $from_date . "' and '" . $to_date . "' or pickup between '" . $from_date . "' and '" . $to_date . "') or (DATE_ADD(dropoff, INTERVAL 10 MINUTE)>='" . $from_date . "' and DATE_SUB(pickup, INTERVAL 10 MINUTE)<='" . $to_date . "')))";
				$q = "SELECT id
				FROM vehicles
				WHERE in_service = 1
				AND deleted_at IS NULL
				AND id NOT IN (
					SELECT DISTINCT vehicle_id
					FROM bookings
					WHERE deleted_at IS NULL
					AND cancellation = 0
					AND (
						(dropoff BETWEEN '" . $from_date . "' AND '" . $to_date . "'
						OR pickup BETWEEN '" . $from_date . "' AND '" . $to_date . "')
						OR (DATE_ADD(dropoff, INTERVAL " . $vehicleInterval . ") >= '" . $from_date . "' AND DATE_SUB(pickup, INTERVAL " . $vehicleInterval . ") <= '" . $to_date . "')
					)
				)";
			} else {
				// $q = "select id from vehicles where in_service=1 and deleted_at is null and group_id=" . Auth::user()->group_id . " and  id not in(select vehicle_id from bookings where  deleted_at is null  and ((dropoff between '" . $from_date . "' and '" . $to_date . "' or pickup between '" . $from_date . "' and '" . $to_date . "') or (DATE_ADD(dropoff, INTERVAL 10 MINUTE)>='" . $from_date . "' and DATE_SUB(pickup, INTERVAL 10 MINUTE)<='" . $to_date . "')))";

				$q = "SELECT id
				FROM vehicles
				WHERE in_service = 1
				AND deleted_at IS NULL
				AND group_id = " . Auth::user()->group_id . "
				AND id NOT IN (
					SELECT DISTINCT vehicle_id
					FROM bookings
					WHERE deleted_at IS NULL
					AND cancellation = 0
					AND (
						(dropoff BETWEEN '" . $from_date . "' AND '" . $to_date . "'
						OR pickup BETWEEN '" . $from_date . "' AND '" . $to_date . "')
						OR (DATE_ADD(dropoff, INTERVAL " . $vehicleInterval . ") >= '" . $from_date . "' AND DATE_SUB(pickup, INTERVAL " . $vehicleInterval . ") <= '" . $to_date . "')
					)
				)";
			}
			$d = collect(DB::select($q));

			$new = array();
			foreach ($d as $ro) {
				$vhc = VehicleModel::find($ro->id);
				$text = $vhc->make_name . "-" . $vhc->model_name . "-" . $vhc->license_plate;
				array_push($new, array("id" => $ro->id, "text" => $text));

			}
			//dd($new);
			$r['data'] = $new;
			return $r;

		} else {
			$id = $request->get("id");
			$current = Bookings::find($id);
			if ($current->vehicle_typeid != null) {
				$condition = " and type_id = '" . $current->vehicle_typeid . "'";

			} else {
				$condition = "";
			}

			if (Auth::user()->group_id == null || Auth::user()->user_type == "S") {
				// $q = "select id from vehicles where in_service=1 " . $condition . " and id not in (select vehicle_id from bookings where id!=$id and  deleted_at is null  and ((dropoff between '" . $from_date . "' and '" . $to_date . "' or pickup between '" . $from_date . "' and '" . $to_date . "') or (DATE_ADD(dropoff, INTERVAL 10 MINUTE)>='" . $from_date . "' and DATE_SUB(pickup, INTERVAL 10 MINUTE)<='" . $to_date . "')))";
				$q = "SELECT id
				FROM vehicles
				WHERE in_service = 1" . $condition . "
				AND id NOT IN (
					SELECT DISTINCT vehicle_id
					FROM bookings
					WHERE id != $id
					AND deleted_at IS NULL
					AND cancellation = 0
					AND (
						(dropoff BETWEEN DATE_ADD('" . $from_date . "', INTERVAL " . $vehicleInterval . ") AND DATE_SUB('" . $to_date . "', INTERVAL " . $vehicleInterval . "))
						OR (pickup BETWEEN DATE_ADD('" . $from_date . "', INTERVAL " . $vehicleInterval . ") AND DATE_SUB('" . $to_date . "', INTERVAL " . $vehicleInterval . "))
						OR (DATE_ADD(dropoff, INTERVAL " . $vehicleInterval . ") >= '" . $from_date . "' AND DATE_SUB(pickup, INTERVAL " . $vehicleInterval . ") <= '" . $to_date . "')
					)
				)";
			} else {
				// $q = "select id from vehicles where in_service=1 " . $condition . " and group_id=" . Auth::user()->group_id . " and id not in (select vehicle_id from bookings where id!=$id and  deleted_at is null  and ((dropoff between '" . $from_date . "' and '" . $to_date . "' or pickup between '" . $from_date . "' and '" . $to_date . "') or (DATE_ADD(dropoff, INTERVAL 10 MINUTE)>='" . $from_date . "' and DATE_SUB(pickup, INTERVAL 10 MINUTE)<='" . $to_date . "')))";
				$q = "SELECT id
				FROM vehicles
				WHERE in_service = 1" . $condition . "
				AND group_id = " . Auth::user()->group_id . "
				AND id NOT IN (
					SELECT DISTINCT vehicle_id
					FROM bookings
					WHERE id != $id
					AND deleted_at IS NULL
					AND cancellation = 0
					AND (
						(dropoff BETWEEN DATE_ADD('" . $from_date . "', INTERVAL " . $vehicleInterval . ") AND DATE_SUB('" . $to_date . "', INTERVAL " . $vehicleInterval . "))
						OR (pickup BETWEEN DATE_ADD('" . $from_date . "', INTERVAL " . $vehicleInterval . ") AND DATE_SUB('" . $to_date . "', INTERVAL " . $vehicleInterval . "))
						OR (DATE_ADD(dropoff, INTERVAL " . $vehicleInterval . ") >= '" . $from_date . "' AND DATE_SUB(pickup, INTERVAL " . $vehicleInterval . ") <= '" . $to_date . "')
					)
				)";
			}

			$d = collect(DB::select($q));

			$chk = $d->where('id', $current->vehicle_id);
			$r['show_error'] = "yes";
			if (count($chk) > 0) {
				$r['show_error'] = "no";
			}

			$new = array();
			foreach ($d as $ro) {
				$vhc = VehicleModel::find($ro->id);
				$text = $vhc->make_name . "-" . $vhc->model_name . "-" . $vhc->license_plate;
				if ($ro->id == $current->vehicle_id) {
					array_push($new, array("id" => $ro->id, "text" => $text, "selected" => true));
				} else {
					array_push($new, array("id" => $ro->id, "text" => $text));
				}
			}
			$r['data'] = $new;
			return $r;
		}

	}

	public function calendar_event($id) {
		$data['booking'] = Bookings::find($id);
		return view("bookings.event", $data);

	}
	public function calendar_view() {
		$booking = Bookings::where('user_id', Auth::user()->id)->where('cancellation',0)->exists();
		return view("bookings.calendar", compact('booking'));
	}

	public function service_view($id) {
		$data['service'] = ServiceReminderModel::find($id);
		return view("bookings.service_event", $data);
	}

	public function calendar(Request $request) {
		$data = array();
		$start = $request->get("start");
		$end = $request->get("end");
		if (Auth::user()->group_id == null || Auth::user()->user_type == "S") {
			$b = Bookings::where('cancellation',0)->get();
		} else {
			$vehicle_ids = VehicleModel::where('group_id', Auth::user()->group_id)->pluck('id')->toArray();
			$b = Bookings::whereIn('vehicle_id', $vehicle_ids)->where('cancellation',0)->get();
		}

		foreach ($b as $booking) {
			$x['start'] = $booking->pickup;
			$x['end'] = $booking->dropoff;
			if ($booking->status == 1) {
				$color = "grey";
			} else {
				$color = "red";
			}
			$x['backgroundColor'] = $color;
			$x['title'] = $booking->customer->name;
			$x['id'] = $booking->id;
			$x['type'] = 'calendar';

			array_push($data, $x);
		}

		$reminders = ServiceReminderModel::get();
		foreach ($reminders as $r) {
			$interval = substr($r->services->overdue_unit, 0, -3);
			$int = $r->services->overdue_time . $interval;
			$date = date('Y-m-d', strtotime($int, strtotime(date('Y-m-d'))));
			if ($r->last_date != 'N/D') {
				$date = date('Y-m-d', strtotime($int, strtotime($r->last_date)));
			}

			$x['start'] = $date;
			$x['end'] = $date;

			$color = "green";

			$x['backgroundColor'] = $color;
			$x['title'] = $r->services->description;
			$x['id'] = $r->id;
			$x['type'] = 'service';
			array_push($data, $x);
		}
		return $data;
	}

	public function create() {
		$user = Auth::user()->group_id;
		$data['customers'] = User::where('user_type', 'C')->get();
		$drivers = User::whereUser_type("D")->get();
		$data['drivers'] = [];
	
		foreach ($drivers as $d) {
			if ($d->getMeta('is_active') == 1) {
				$data['drivers'][] = $d;
			}
		}
	
		$data['addresses'] = Address::where('customer_id', Auth::user()->id)->get();
	
		if ($user == null) {
			$data['vehicles'] = VehicleModel::whereIn_service("1")->get();
		} else {
			$data['vehicles'] = VehicleModel::where([['group_id', $user], ['in_service', '1']])->get();
		}
	
		// Fetch available time slots
		$data['timeSlots'] = TimeSlot::all();
		// Log::info("hi there ");
		// Log::info($data);
	
		return view("bookings.create", $data);
	}

	
// 	public function getShifts(Request $request)
// {
//     $timeSlots = []; // Ensure $timeSlots is always defined

//     if ($request->ajax()) {
//         $log = $request->action;
// 		// Log::info($log); 


//         // Fetch shifts based on action type (login/logout)
//         $timeSlots = Timeslot::where('log', $log)->pluck('shift');
// 		Log::info("Fetched shifts: ", $timeSlots->toArray());
//     }

//     return response()->json(['timeSlots' => $timeSlots]); // Always return a response
// }


 public function getAvailableDays(Request $request)
    {
        Log::info("AJAX Request Received", ['request_data' => $request->all()]);

        if ($request->ajax()) {
            Log::info("Request is an AJAX call");

            // Fetch distinct available days
            $days = Timeslot::distinct()->pluck('days_available')->unique()->values()->toArray();

            Log::info("Days Fetched", ['days' => $days]);

            if (empty($days)) {
                Log::warning("No days available found in the database.");
                return response()->json(['success' => false, 'message' => 'No days available found.']);
            }

            return response()->json(['success' => true, 'days_available' => $days]);
        }

        Log::warning("Request is not AJAX");
        return response()->json(['success' => false, 'message' => 'Invalid request']);
    }



	public function edit($id) {
		$booking = Bookings::whereId($id)->get()->first();
		// dd($booking->vehicle_typeid);
		if ($booking->vehicle_typeid != null) {
			$condition = " and type_id = '" . $booking->vehicle_typeid . "'";
		} else {
			$condition = "";
		}
		$q = "select id,name,deleted_at from users where user_type='D' and deleted_at is null and id not in (select user_id from bookings where status=0 and  id!=" . $id . " and deleted_at is null and  (DATE_SUB(pickup, INTERVAL 15 MINUTE) between '" . $booking->pickup . "' and '" . $booking->dropoff . "' or DATE_ADD(dropoff, INTERVAL 15 MINUTE) between '" . $booking->pickup . "' and '" . $booking->dropoff . "' or dropoff between '" . $booking->pickup . "' and '" . $booking->dropoff . "'))";
		// $drivers = collect(DB::select($q));
		if (Auth::user()->group_id == null || Auth::user()->user_type == "S") {
			$q1 = "select * from vehicles where in_service=1" . $condition . " and deleted_at is null and id not in (select vehicle_id from bookings where status=0 and  id!=" . $id . " and deleted_at is null and  (DATE_SUB(pickup, INTERVAL 15 MINUTE) between '" . $booking->pickup . "' and '" . $booking->dropoff . "' or DATE_ADD(dropoff, INTERVAL 15 MINUTE) between '" . $booking->pickup . "' and '" . $booking->dropoff . "'  or dropoff between '" . $booking->pickup . "' and '" . $booking->dropoff . "'))";
		} else {
			$q1 = "select * from vehicles where in_service=1" . $condition . " and deleted_at is null and group_id=" . Auth::user()->group_id . " and id not in (select vehicle_id from bookings where status=0 and  id!=" . $id . " and deleted_at is null and  (DATE_SUB(pickup, INTERVAL 15 MINUTE) between '" . $booking->pickup . "' and '" . $booking->dropoff . "' or DATE_ADD(dropoff, INTERVAL 15 MINUTE) between '" . $booking->pickup . "' and '" . $booking->dropoff . "'  or dropoff between '" . $booking->pickup . "' and '" . $booking->dropoff . "'))";
		}

		$v_ids = array();
		$vehicles_data = collect(DB::select($q1));
		foreach ($vehicles_data as $v) {
			$v_ids[] = $v->id;
		}
		$vehicles = VehicleModel::whereIn('id', $v_ids)->get();
		$index['drivers'] = [];
		$drivers = User::whereUser_type("D")->get();
		// $drivers = $this->get_driver($from_date,$to_date);
		foreach ($drivers as $d) {
			if ($d->getMeta('is_active') == 1) {
				$index['drivers'][] = $d;
			}
		}
		$index['vehicles'] = $vehicles;
		$index['data'] = $booking;
		$index['udfs'] = unserialize($booking->getMeta('udf'));

		return view("bookings.edit", $index);
	}

	public function destroy(Request $request) {
		// dd($request->get('id'));
		Bookings::find($request->get('id'))->delete();
		IncomeModel::where('income_id', $request->get('id'))->where('income_cat', 1)->delete();

		return redirect()->route('bookings.index');
	}

	protected function check_booking($pickup, $dropoff, $vehicle) {

		$chk = DB::table("bookings")
			->where("status", 0)
			->where("vehicle_id", $vehicle)
			->whereNull("deleted_at")
			->where("pickup", ">=", $pickup)
			->where("dropoff", "<=", $dropoff)
			->get();

		if (count($chk) > 0) {
			return false;
		} else {
			return true;
		}

	}

	public function store(BookingRequest $request) {
		
		// dd($request->all()); // Dumps all request data
		// exit();
		$max_seats = VehicleModel::find($request->get('vehicle_id'))->types->seats;
		$xx = $this->check_booking($request->get("pickup"), $request->get("dropoff"), $request->get("vehicle_id"));
		if ($xx) {
			if($request->get("travellers") > $max_seats){
				return redirect()->route("bookings.create")->withErrors(["error" => "Number of Travellers exceed seating capity of the vehicle | Seats Available : ".$max_seats.""])->withInput();
			}else{
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
				$booking->udf = serialize($request->get('udf'));
				$booking->accept_status = 1; //0=yet to accept, 1= accept
				$booking->ride_status = "Upcoming";
				$booking->booking_type = 1;
				$booking->journey_date = date('d-m-Y', strtotime($booking->pickup));
				$booking->journey_time = date('H:i:s', strtotime($booking->pickup));
				$booking->save();
				$mail = Bookings::find($id);
				$this->booking_notification($booking->id);
	
				// send sms to customer while adding new booking
				$this->sms_notification($booking->id);
	
				// browser notification
				$this->push_notification($booking->id);
				if (Hyvikk::email_msg('email') == 1) {
					Mail::to($mail->customer->email)->send(new VehicleBooked($booking));
					Mail::to($mail->driver->email)->send(new DriverBooked($booking));
				}
				return redirect()->route("bookings.index");
			}
		} else {
			return redirect()->route("bookings.create")->withErrors(["error" => "Selected Vehicle is not Available in Given Timeframe"])->withInput();
		}
	}

	public function sms_notification($booking_id) {
		$booking = Bookings::find($booking_id);

		$id = Hyvikk::twilio('sid');
		$token = Hyvikk::twilio('token');
		$from = Hyvikk::twilio('from');
		$to = $booking->customer->mobno; // twilio trial verified number
		$driver_no = $booking->driver->phone_code . $booking->driver->phone;

		$customer_name = $booking->customer->name;
		$customer_contact = $booking->customer->mobno;
		$driver_name = $booking->driver->name;
		$driver_contact = $booking->driver->phone;
		$pickup_address = $booking->pickup_addr;
		$destination_address = $booking->dest_addr;
		$pickup_datetime = date(Hyvikk::get('date_format') . " H:i", strtotime($booking->pickup));
		$dropoff_datetime = date(Hyvikk::get('date_format') . " H:i", strtotime($booking->dropoff));
		$passengers = $booking->travellers;

		$search = ['$customer_name', '$customer_contact', '$pickup_address', '$pickup_datetime', '$passengers', '$destination_address', '$dropoff_datetime', '$driver_name', '$driver_contact'];
		$replace = [$customer_name, $customer_contact, $pickup_address, $pickup_datetime, $passengers, $destination_address, $dropoff_datetime, $driver_name, $driver_contact];

		// send sms to customer
		$body = str_replace($search, $replace, Hyvikk::twilio("customer_message"));

		$url = "https://api.twilio.com/2010-04-01/Accounts/$id/SMS/Messages";

		// $new_body = str_split($body, 120);
		$new_body = explode("\n", wordwrap($body, 120));

		foreach ($new_body as $row) {
			$data = array(
				'From' => $from,
				'To' => $to,
				'Body' => $row,
			);
			$post = http_build_query($data);
			$x = curl_init($url);
			curl_setopt($x, CURLOPT_POST, true);
			curl_setopt($x, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($x, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($x, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($x, CURLOPT_USERPWD, "$id:$token");
			curl_setopt($x, CURLOPT_POSTFIELDS, $post);
			$y = curl_exec($x);
			curl_close($x);
		}

		// send sms to drivers
		$driver_body = str_replace($search, $replace, Hyvikk::twilio("driver_message"));

		$msg_body = explode("\n", wordwrap($driver_body, 120));

		foreach ($msg_body as $row) {
			$data = array(
				'From' => $from,
				'To' => $driver_no,
				'Body' => $row,
			);
			$post = http_build_query($data);
			$x = curl_init($url);
			curl_setopt($x, CURLOPT_POST, true);
			curl_setopt($x, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($x, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($x, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($x, CURLOPT_USERPWD, "$id:$token");
			curl_setopt($x, CURLOPT_POSTFIELDS, $post);
			$y = curl_exec($x);
			curl_close($x);
		}
		// dd($y);

	}

	public function push_notification($id) {
		$booking = Bookings::find($id);
		$auth = array(
			'VAPID' => array(
				'subject' => 'Alert about new post',
				'publicKey' => 'BKt+swntut+5W32Psaggm4PVQanqOxsD5PRRt93p+/0c+7AzbWl87hFF184AXo/KlZMazD5eNb1oQVNbK1ti46Y=',
				'privateKey' => 'NaMmQJIvddPfwT1rkIMTlgydF+smNzNXIouzRMzc29c=', // in the real world, this would be in a secret file
			),
		);

		$select1 = DB::table('push_notification')->select('*')->whereIn('user_id', [$booking->user_id])->get()->toArray();

		$webPush = new WebPush($auth);

		foreach ($select1 as $fetch) {
			$sub = Subscription::create([
				'endpoint' => $fetch->endpoint, // Firefox 43+,
				'publicKey' => $fetch->publickey, // base 64 encoded, should be 88 chars
				'authToken' => $fetch->authtoken, // base 64 encoded, should be 24 chars
				'contentEncoding' => $fetch->contentencoding,
			]);
			$user = User::find($fetch->user_id);

			$title = __('fleet.new_booking');
			$body = __('fleet.customer') . ": " . $booking->customer->name . ", " . __('fleet.pickup') . ": " . date(Hyvikk::get('date_format') . ' g:i A', strtotime($booking->pickup)) . ", " . __('fleet.pickup_addr') . ": " . $booking->pickup_addr . ", " . __('fleet.dropoff_addr') . ": " . $booking->dest_addr;
			$url = url('admin/bookings');

			$array = array(
				'title' => $title ?? "",
				'body' => $body ?? "",
				'img' => url('assets/images/' . Hyvikk::get('icon_img')),
				'url' => $url ?? url('admin/'),
			);
			$object = json_encode($array);

			if ($fetch->user_id == $user->id) {
				$test = $webPush->sendOneNotification($sub, $object);
			}
			foreach ($webPush->flush() as $report) {

				$endpoint = $report->getRequest()->getUri()->__toString();

			}

		}

	}
	public function update(BookingRequest $request) {
		//   dd($request->all());
		$max_seats = VehicleModel::find($request->get('vehicle_id'))->types->seats;
		if($request->get("travellers") > $max_seats){
			return redirect()->route("bookings.edit",$request->get('id'))->withErrors(["error" => "Number of Travellers exceed seating capity of the vehicle | Seats Available : ".$max_seats.""])->withInput();
		}

		$booking = Bookings::whereId($request->get("id"))->first();

		$booking->vehicle_id = $request->get("vehicle_id");
		$booking->user_id = $request->get("user_id");
		$booking->driver_id = $request->get('driver_id');
		$booking->travellers = $request->get("travellers");
		$booking->pickup = $request->get("pickup");
		$booking->dropoff = $request->get("dropoff");
		$booking->pickup_addr = $request->get("pickup_addr");
		$booking->dest_addr = $request->get("dest_addr");
		if ($booking->ride_status == null) {
			$booking->ride_status = "Upcoming";
		}

		$dropoff = Carbon::parse($request->get("dropoff"));
		$pickup = Carbon::parse($request->get("pickup"));
		$booking->note = $request->get('note');
		$diff = $pickup->diffInMinutes($dropoff);
		$booking->duration = $diff;
		$booking->journey_date = date('d-m-Y', strtotime($request->get("pickup")));
		$booking->journey_time = date('H:i:s', strtotime($request->get("pickup")));
		$booking->udf = serialize($request->get('udf'));
		$booking->save();

		return redirect()->route('bookings.index');

	}

	public function prev_address(Request $request) {
		$booking = Bookings::where('customer_id', $request->get('id'))->orderBy('id', 'desc')->first();
		if ($booking != null) {
			$r = array('pickup_addr' => $booking->pickup_addr, 'dest_addr' => $booking->dest_addr);
		} else {
			$r = array('pickup_addr' => "", 'dest_addr' => "");
		}

		return $r;
	}

	public function print_bookings() {
		if (Auth::user()->user_type == "C") {
			$data['data'] = Bookings::where('customer_id', Auth::user()->id)->orderBy('id', 'desc')->get();
		} else {
			$data['data'] = Bookings::orderBy('id', 'desc')->get();
		}

		return view('bookings.print_bookings', $data);
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
			$push = new PushNotification('fcm_id');
			$push->setMessage($data)
				->setApiKey(env('server_key'))
				->setDevicesToken([$driver->getMeta('fcm_id')])
				->send();
			// PushNotification::app('appNameAndroid')
			//     ->to($driver->getMeta('fcm_id'))
			//     ->send($data);
		}

	}

	public function bulk_delete(Request $request) {
		Bookings::whereIn('id', $request->ids)->delete();
		IncomeModel::whereIn('income_id', $request->ids)->where('income_cat', 1)->delete();
		return back();
	}

	public function cancel_booking(Request $request) {
		// dd($request->all());
		$booking = Bookings::find($request->cancel_id);
		$booking->cancellation = 1;
		$booking->ride_status = "Cancelled";
		$booking->reason = $request->reason;
		$booking->save();
		// if booking->status != 1 then delete income record
		IncomeModel::where('income_id', $request->cancel_id)->where('income_cat', 1)->delete();
		if (Hyvikk::email_msg('email') == 1) {
			Mail::to($booking->customer->email)->send(new BookingCancelled($booking,$booking->customer->name));
			Mail::to($booking->driver->email)->send(new BookingCancelled($booking,$booking->driver->name));
		}
		return back()->with(['msg' => 'Booking cancelled successfully!']);
	}
}
