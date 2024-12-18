<?php

/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Mail\DriverBooked;
use App\Mail\VehicleBooked;
use App\Model\Address;
use App\Model\BookingIncome;
use App\Model\BookingPaymentsModel;
use App\Model\Bookings;
use App\Model\IncomeModel;
use App\Model\ServiceReminderModel;
use App\Model\User;
use App\Model\VehicleModel;
use Auth;
use Carbon\Carbon;
use DB;
use Edujugon\PushNotification\PushNotification;
use Hyvikk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;
use Validator;

class BookingsApiController extends Controller {

	public function update_by_customer(Request $request) {
		$validation = Validator::make($request->all(), [
			'id' => 'required|integer',
			'vehicle_id' => 'required|integer',
			// 'driver_id' => 'required|integer',
			'travellers' => 'required|integer',
			'pickup_addr' => 'required',
			'dest_addr' => 'required|different:pickup_addr',
			'pickup_datetime' => 'required',
			'dropoff_datetime' => 'required',
			'udf' => 'nullable|array',
		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {

			$xx = $this->check_edit_booking($request->pickup_datetime, $request->dropoff_datetime, $request->vehicle_id, $request->id);
			if ($xx) {
				Bookings::where('id', $request->id)->update([
					'vehicle_id' => $request->vehicle_id,
					'user_id' => Auth::id(),
					'pickup' => date('Y-m-d H:i:s', strtotime($request->pickup_datetime)),
					'dropoff' => date('Y-m-d H:i:s', strtotime($request->dropoff_datetime)),
					'pickup_addr' => $request->pickup_addr,
					'dest_addr' => $request->dest_addr,
					'travellers' => $request->travellers,
					'status' => 0,
					// 'driver_id' => $request->driver_id,
					'note' => $request->note,
				]);

				$booking = Bookings::find($request->id);

				// if ($booking->ride_status == null) {
				//     $booking->ride_status = "Upcoming";
				// }

				$dropoff = Carbon::parse($request->dropoff_datetime);
				$pickup = Carbon::parse($request->pickup_datetime);
				$diff = $pickup->diffInMinutes($dropoff);
				$booking->duration = $diff;
				$booking->journey_date = date('d-m-Y', strtotime($request->pickup_datetime));
				$booking->journey_time = date('H:i:s', strtotime($request->pickup_datetime));
				$booking->udf = serialize($request->udf);
				$booking->save();

				$data['success'] = "1";
				$data['message'] = "Booking updated successfully!";
				$data['data'] = "";

			} else {
				$data['success'] = "0";
				$data['message'] = "Selected Vehicle is not Available in Given Timeframe";
				$data['data'] = "";

			}
		}
		return $data;
	}

	public function store_by_customer(Request $request) {
		$validation = Validator::make($request->all(), [
			'customer_id' => 'required|integer',
			'vehicle_id' => 'required|integer',
			// 'driver_id' => 'required|integer',
			'travellers' => 'required|integer',
			'pickup_addr' => 'required',
			'dest_addr' => 'required|different:pickup_addr',
			'pickup_datetime' => 'required',
			'dropoff_datetime' => 'required',
			'udf' => 'nullable|array',

		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {

			$xx = $this->check_booking($request->pickup_datetime, $request->dropoff_datetime, $request->vehicle_id);
			if ($xx) {
				$booking = Bookings::create([
					'customer_id' => $request->customer_id,
					'vehicle_id' => $request->vehicle_id,
					'user_id' => Auth::id(),
					'pickup' => date('Y-m-d H:i:s', strtotime($request->pickup_datetime)),
					'dropoff' => date('Y-m-d H:i:s', strtotime($request->dropoff_datetime)),
					'pickup_addr' => $request->pickup_addr,
					'dest_addr' => $request->dest_addr,
					'travellers' => $request->travellers,
					'status' => 0,
					// 'driver_id' => $request->driver_id,
					'note' => $request->note,
				]);

				$dropoff = Carbon::parse($booking->dropoff);
				$pickup = Carbon::parse($booking->pickup);
				$diff = $pickup->diffInMinutes($dropoff);
				$booking->duration = $diff;
				$booking->udf = serialize($request->udf);
				// $booking->accept_status = 1; //0=yet to accept, 1= accept
				// $booking->ride_status = "Upcoming";
				$booking->booking_type = 1;
				$booking->journey_date = date('d-m-Y', strtotime($booking->pickup));
				$booking->journey_time = date('H:i:s', strtotime($booking->pickup));
				$booking->save();

				// browser notification
				$this->push_notification($booking->id);
				if (Hyvikk::email_msg('email') == 1) {
					Mail::to($booking->customer->email)->send(new VehicleBooked($booking));
					// Mail::to($booking->driver->email)->send(new DriverBooked($booking));

				}

				Address::updateOrCreate(['customer_id' => $request->customer_id, 'address' => $request->pickup_addr]);

				Address::updateOrCreate(['customer_id' => $request->customer_id, 'address' => $request->dest_addr]);
				$data['success'] = "1";
				$data['message'] = "Booking added successfully!";
				$data['data'] = "";

			} else {
				$data['success'] = "0";
				$data['message'] = "Selected Vehicle is not Available in Given Timeframe";
				$data['data'] = "";

			}
		}
		return $data;
	}

	public function bulk_delete(Request $request) {
		$validation = Validator::make($request->all(), [
			'ids' => 'required|array',
		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {

			Bookings::whereIn('id', $request->ids)->delete();
			IncomeModel::whereIn('income_id', $request->ids)->where('income_cat', 1)->delete();

			$data['success'] = "1";
			$data['message'] = "Records deleted successfully!";
			$data['data'] = "";
		}
		return $data;
	}

	public function get_dropdowns(Request $request) {

		$validation = Validator::make($request->all(), [
			'id' => 'required|integer',
			'pickup_datetime' => 'required',
			'dropoff_datetime' => 'required',
		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {
			$driver_details = array();
			$vehicle_details = array();
			$id = $request->id;
			$booking = Bookings::find($id);
			$pickup = $request->pickup_datetime;
			$dropoff = $request->dropoff_datetime;
			$exclude_vehicle_ids = Bookings::where('id', '!=', $id)->where("status", 0)
				->where(function ($query) use ($pickup, $dropoff) {
					$query->whereBetween('pickup', [$pickup, $dropoff])
						->orWhereBetween('dropoff', [$pickup, $dropoff]);
				})
				->pluck('vehicle_id')
				->toArray();
			$exclude_driver_ids = Bookings::where('id', '!=', $id)->where("status", 0)
				->where(function ($query) use ($pickup, $dropoff) {
					$query->whereBetween('pickup', [$pickup, $dropoff])
						->orWhereBetween('dropoff', [$pickup, $dropoff]);
				})
				->pluck('driver_id')
				->toArray();
			$vehicle_list = VehicleModel::whereNotIn('id', $exclude_vehicle_ids)->where('in_service', 1);
			$drivers = User::whereNotIn('id', $exclude_driver_ids)->where('user_type', 'D')->get();

			if ($booking != null && $booking->vehicle_typeid != null) {
				$vehicle_list = $vehicle_list->where('type_id', $booking->vehicle_typeid);
			}

			if (!(Auth::user()->group_id == null || Auth::user()->user_type == "S")) {
				$vehicle_list = $vehicle_list->where('group_id', Auth::user()->group_id);
			}
			$vehicles = $vehicle_list->get();

			foreach ($vehicles as $row) {
				$vehicle_details[] = array(
					'vehicle_id' => $row->id,
					'vehicle' => $row->make_name . " - " . $row->model_name . " - " . $row->license_plate,
					'driver_id' => $row->driver_id,
				);
			}
			foreach ($drivers as $row) {
				$driver_details[] = array(
					'driver_id' => $row->id,
					'name' => $row->name,
				);
			}
			$customers = User::where('user_type', 'C')->get();
			$customer_details = array();
			foreach ($customers as $row) {
				$customer_details[] = array(
					'id' => $row->id,
					'name' => $row->name,
				);
			}
			$data['success'] = "1";
			$data['message'] = "Data fetched!";
			$data['data'] = array(
				'vehicles' => $vehicle_details,
				'drivers' => $driver_details,
				'customers' => $customer_details,
			);
		}
		return $data;
	}
	public function dropdowns($id) {
		$driver_details = array();
		$vehicle_details = array();
		if ($id == 0) {
			$drivers = User::whereUser_type("D")->get();
			if (Auth::user()->group_id == null || Auth::user()->user_type == "S") {
				$vehicles = VehicleModel::whereIn_service("1")->get();
			} else {
				$vehicles = VehicleModel::where([['group_id', Auth::user()->group_id], ['in_service', '1']])->get();
			}

		} else {

			$booking = Bookings::find($id);
			$pickup = $booking->pickup;
			$dropoff = $booking->dropoff;
			$exclude_vehicle_ids = Bookings::where('id', '!=', $id)->where("status", 0)
				->where(function ($query) use ($pickup, $dropoff) {
					$query->whereBetween('pickup', [$pickup, $dropoff])
						->orWhereBetween('dropoff', [$pickup, $dropoff]);
				})
				->pluck('vehicle_id')
				->toArray();
			$exclude_driver_ids = Bookings::where('id', '!=', $id)->where("status", 0)
				->where(function ($query) use ($pickup, $dropoff) {
					$query->whereBetween('pickup', [$pickup, $dropoff])
						->orWhereBetween('dropoff', [$pickup, $dropoff]);
				})
				->pluck('driver_id')
				->toArray();
			$vehicle_list = VehicleModel::whereNotIn('id', $exclude_vehicle_ids)->where('in_service', 1);
			$drivers = User::whereNotIn('id', $exclude_driver_ids)->where('user_type', 'D')->get();
			// dd($driver_list);
			if ($booking->vehicle_typeid != null) {
				$vehicle_list = $vehicle_list->where('type_id', $booking->vehicle_typeid);
			}

			if (!(Auth::user()->group_id == null || Auth::user()->user_type == "S")) {
				$vehicle_list = $vehicle_list->where('group_id', Auth::user()->group_id);
			}
			$vehicles = $vehicle_list->get();
		}

		foreach ($vehicles as $row) {
			$vehicle_details[] = array(
				'vehicle_id' => $row->id,
				'vehicle' => $row->make_name . " - " . $row->model_name . " - " . $row->license_plate,
				'driver_id' => $row->driver_id,
			);
		}
		foreach ($drivers as $row) {
			$driver_details[] = array(
				'driver_id' => $row->id,
				'name' => $row->name,
			);
		}
		$customers = User::where('user_type', 'C')->get();
		$customer_details = array();
		foreach ($customers as $row) {
			$customer_details[] = array(
				'id' => $row->id,
				'name' => $row->name,
			);
		}
		$data['success'] = "1";
		$data['message'] = "Data fetched!";
		$data['data'] = array(
			'vehicles' => $vehicle_details,
			'drivers' => $driver_details,
			'customers' => $customer_details,
		);
		return $data;
	}

	public function view_event(Request $request) {
		$validation = Validator::make($request->all(), [
			'id' => 'required|integer',
			'type' => 'required|in:booking,service',
		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {
			$details = array();
			if ($request->type == "booking") {
				$booking = Bookings::find($request->id);
				$details = array(
					'customer' => $booking->customer->name,
					'vehicle' => $booking->vehicle->make_name . " - " . $booking->vehicle->model_name . " - " . $booking->vehicle->license_plate,
					'travellers' => $booking->travellers,
					'note' => $booking->note,
					'pickup_datetime' => $booking->pickup,
					'dropoff_datetime' => $booking->dropoff,
					'pickup_addr' => $booking->pickup_addr,
					'dest_addr' => $booking->dest_addr,
				);

			} elseif ($request->type == "service") {
				$service = ServiceReminderModel::find($request->id);
				$next_due_meter = null;
				if ($service->last_meter == 0) {
					$next_due_meter = $service->vehicle->int_mileage + $service->services->overdue_meter;
				} else {
					$next_due_meter = $service->last_meter + $service->services->overdue_meter;
				}

				$interval = substr($service->services->overdue_unit, 0, -3);
				$int = $service->services->overdue_time . $interval;
				if ($service->last_date != 'N/D') {
					$next_due_date = date('Y-m-d', strtotime($int, strtotime($service->last_date)));
				} else {
					$next_due_date = date('Y-m-d', strtotime($int, strtotime(date('Y-m-d'))));
				}

				$interval = $service->services->overdue_time . " " . $service->services->overdue_unit;
				if ($service->services->overdue_meter != null) {
					$interval .= " or " . $service->services->overdue_meter . " " . Hyvikk::get('dis_format');
				}

				$details = array(
					'vehicle' => $service->vehicle->make_name . " - " . $service->vehicle->model_name . " - " . $service->vehicle->license_plate,
					'service_item' => $service->services->description,
					'next_due_meter' => $next_due_meter . " " . Hyvikk::get('dis_format'),
					'next_due_date' => $next_due_date,
					'start_date' => date('Y-m-d', strtotime($service->last_date)),
					'last_meter' => $service->last_meter,
					'interval' => $interval,
				);
			} else {
				$data['success'] = "0";
				$data['message'] = "Failed to load event details, please try again later!";
				$data['data'] = "";
				return $data;
			}

			$data['success'] = "1";
			$data['message'] = "Data fetched!";
			$data['data'] = $details;

		}
		return $data;
	}

	public function events() {

		$service_events = array();
		$booking_events = array();

		if (Auth::user()->group_id == null || Auth::user()->user_type == "S") {
			$b = Bookings::get();
		} else {
			$vehicle_ids = VehicleModel::where('group_id', Auth::user()->group_id)->pluck('id')->toArray();
			$b = Bookings::whereIn('vehicle_id', $vehicle_ids)->get();
		}

		foreach ($b as $booking) {
			$booking_events[] = array('start' => $booking->pickup,
				'end' => $booking->dropoff,
				'title' => $booking->customer->name,
				'id' => $booking->id,
				'type' => 'booking');

		}

		$reminders = ServiceReminderModel::get();
		foreach ($reminders as $r) {
			$interval = substr($r->services->overdue_unit, 0, -3);
			$int = $r->services->overdue_time . $interval;
			$date = date('Y-m-d', strtotime($int, strtotime(date('Y-m-d'))));
			if ($r->last_date != 'N/D') {
				$date = date('Y-m-d', strtotime($int, strtotime($r->last_date)));
			}

			$service_events[] = array('start' => $date,
				'end' => $date,
				'title' => $r->services->description,
				'id' => $r->id,
				'type' => 'service');

		}
		$data['success'] = "1";
		$data['message'] = "Data fetched!";
		$data['data'] = array(
			'booking_events' => $booking_events,
			'service_events' => $service_events,
		);
		return $data;
	}

	public function cancel_booking(Request $request) {
		$validation = Validator::make($request->all(), [
			'id' => 'required|integer',
			'reason' => 'required',
		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {
			$booking = Bookings::find($request->id);
			if ($booking->status == 0 && $booking->ride_status != "Cancelled" && $booking->receipt != 1) {

				$booking->ride_status = "Cancelled";
				$booking->reason = $request->reason;
				$booking->save();
				// if booking->status != 1 then delete income record
				IncomeModel::where('income_id', $request->id)->where('income_cat', 1)->delete();
				$data['success'] = "1";
				$data['message'] = "Booking has been cancelled successfully!";
				$data['data'] = "";
			} else {
				$data['success'] = "0";
				$data['message'] = "Failed to cancel booking, please try again later!";
				$data['data'] = "";
			}

		}
		return $data;
	}

	public function make_payment(Request $request) {
		$validation = Validator::make($request->all(), [
			'id' => 'required|integer',
		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {
			$booking = Bookings::find($request->id);
			if ($booking->payment == 0 && $booking->status == 1 && Auth::user()->user_type != "C") {

				$booking->payment = 1;
				$booking->payment_method = "cash";
				$booking->save();
				BookingPaymentsModel::create(['method' => 'cash', 'booking_id' => $booking->id, 'amount' => $booking->tax_total, 'payment_details' => null, 'transaction_id' => null, 'payment_status' => "succeeded"]);
				$data['success'] = "1";
				$data['message'] = "Payment completed successfully!";
				$data['data'] = "";
			} else {
				$data['success'] = "0";
				$data['message'] = "Failed to make payment, please try again later!";
				$data['data'] = "";
			}

		}
		return $data;
	}

	public function complete_journey(Request $request) {
		$validation = Validator::make($request->all(), [
			'id' => 'required|integer',
		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {
			$booking = Bookings::find($request->id);
			if ($booking->receipt == 1 && $booking->status == 0 && Auth::user()->user_type != "C") {
				$booking->status = 1;
				$booking->ride_status = "Completed";
				$booking->save();
				$data['success'] = "1";
				$data['message'] = "Journey completed successfully!";
				$data['data'] = "";
			} else {
				$data['success'] = "0";
				$data['message'] = "Failed to complete journey, please try again later!";
				$data['data'] = "";
			}

		}
		return $data;
	}

	public function generate_invoice(Request $request) {
		$validation = Validator::make($request->all(), [
			'id' => 'required|integer',
			'income_id' => 'required|integer',
			'day' => 'required|integer',
			'waiting_time' => 'required|integer',
			'mileage' => 'required|numeric',
			'date' => 'required|date',
			'total' => 'required|numeric',
			'tax_total' => 'required|numeric',
			'total_tax_percent' => 'required|numeric',
			'total_tax_charge_rs' => 'required|numeric',

		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {
			$booking = Bookings::find($request->id);
			if ($booking->status == 0 && $booking->receipt != 1 && Auth::user()->user_type != "C" && $booking->ride_status != "Cancelled") {
				$booking->setMeta([
					'day' => $request->day,
					'mileage' => $request->mileage,
					'waiting_time' => $request->waiting_time,
					'date' => date('Y-m-d', strtotime($request->date)),
					'total' => $request->total,
					'total_kms' => $request->mileage,
					'tax_total' => $request->tax_total,
					'total_tax_percent' => $request->total_tax_percent,
					'total_tax_charge_rs' => $request->total_tax_charge_rs,
					'receipt' => 1,
				]);

				$booking->save();

				$id = IncomeModel::create([
					"vehicle_id" => $booking->vehicle_id,
					"amount" => $request->tax_total,
					"user_id" => $booking->customer_id,
					"date" => date('Y-m-d', strtotime($request->date)),
					"mileage" => $request->mileage,
					"income_cat" => $request->income_id,
					"income_id" => $booking->id,
					"tax_percent" => $request->total_tax_percent,
					"tax_charge_rs" => $request->total_tax_charge_rs,
				])->id;

				BookingIncome::create(['booking_id' => $request->id, "income_id" => $id]);

				$data['success'] = "1";
				$data['message'] = "Booking invoice generated successfully!";
				$data['data'] = "";
			} else {
				$data['success'] = "0";
				$data['message'] = "Failed to generate booking invoice, please try again later!";
				$data['data'] = "";
			}

		}
		return $data;

	}

	public function receipt($id) {
		$details = array();
		$row = Bookings::find($id);
		if ($row->receipt == 1) {
			$income = BookingIncome::whereBooking_id($id)->first();
			$details = array(
				'app_name' => Hyvikk::get('app_name'),
				'icon' => asset('assets/images/' . Hyvikk::get('icon_img')),
				'date' => date('Y-m-d', strtotime($income->booking_income->date)),
				'from_address' => Hyvikk::get('badd1') . ", " . Hyvikk::get('badd2') . ", " . Hyvikk::get('city') . ", " . Hyvikk::get('state') . ", " . Hyvikk::get('country'),
				'to_address' => $row->customer->address,
				'id' => $income->income_id,
				'customer_name' => $row->customer->name,
				'pickup_addr' => $row->pickup_addr,
				'pickup_datetime' => $row->pickup,
				'dest_addr' => $row->dest_addr,
				'dropoff_datetime' => $row->dropoff,
				'vehicle' => ($row->vehicle_id) ? $row->vehicle->make_name . " - " . $row->vehicle->model_name . " - " . $row->vehicle->license_plate : "",
				'driver' => ($row->driver_id) ? $row->driver->name : "",
				'mileage' => $row->mileage . " " . Hyvikk::get('dis_format'),
				'waiting_time' => $row->waiting_time,
				'amount' => Hyvikk::get('currency') . " " . $row->total,
				'tax_percent' => (($row->total_tax_percent) ? $row->total_tax_percent : 0) . "%",
				'tax_charges' => Hyvikk::get('currency') . " " . ($row->total_tax_charge_rs) ? $row->total_tax_charge_rs : 0,
				'total_amount' => Hyvikk::get('currency') . " " . ($row->tax_total) ? $row->tax_total : $row->total,
				'invoice_text' => Hyvikk::get('invoice_text'),
			);

			$data['success'] = "1";
			$data['message'] = "Data fetched!";
			$data['data'] = $details;
		} else {
			$data['success'] = "0";
			$data['message'] = "Failed to load details,please try again later!";
			$data['data'] = "";
		}
		return $data;
	}

	public function delete(Request $request) {

		$validation = Validator::make($request->all(), [
			'id' => 'required|integer',
		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {
			Bookings::find($request->id)->delete();
			IncomeModel::where('income_id', $request->id)->where('income_cat', 1)->delete();
			$data['success'] = "1";
			$data['message'] = "Record deleted successfully!";
			$data['data'] = "";
		}
		return $data;
	}

	public function update(Request $request) {
		$validation = Validator::make($request->all(), [
			'id' => 'required|integer',
			'vehicle_id' => 'required|integer',
			'driver_id' => 'required|integer',
			'travellers' => 'required|integer',
			'pickup_addr' => 'required',
			'dest_addr' => 'required|different:pickup_addr',
			'pickup_datetime' => 'required',
			'dropoff_datetime' => 'required',
			'udf' => 'nullable|array',
		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {

			$xx = $this->check_edit_booking($request->pickup_datetime, $request->dropoff_datetime, $request->vehicle_id, $request->id);
			if ($xx) {
				Bookings::where('id', $request->id)->update([
					'vehicle_id' => $request->vehicle_id,
					'user_id' => Auth::id(),
					'pickup' => date('Y-m-d H:i:s', strtotime($request->pickup_datetime)),
					'dropoff' => date('Y-m-d H:i:s', strtotime($request->dropoff_datetime)),
					'pickup_addr' => $request->pickup_addr,
					'dest_addr' => $request->dest_addr,
					'travellers' => $request->travellers,
					'status' => 0,
					'driver_id' => $request->driver_id,
					'note' => $request->note,
				]);

				$booking = Bookings::find($request->id);

				if ($booking->ride_status == null) {
					$booking->ride_status = "Upcoming";
				}

				$dropoff = Carbon::parse($request->dropoff_datetime);
				$pickup = Carbon::parse($request->pickup_datetime);
				$diff = $pickup->diffInMinutes($dropoff);
				$booking->duration = $diff;
				$booking->journey_date = date('d-m-Y', strtotime($request->pickup_datetime));
				$booking->journey_time = date('H:i:s', strtotime($request->pickup_datetime));
				$booking->udf = serialize($request->udf);
				$booking->save();

				$data['success'] = "1";
				$data['message'] = "Booking updated successfully!";
				$data['data'] = "";

			} else {
				$data['success'] = "0";
				$data['message'] = "Selected Vehicle is not Available in Given Timeframe";
				$data['data'] = "";

			}
		}
		return $data;
	}

	public function store(Request $request) {
		$validation = Validator::make($request->all(), [
			'customer_id' => 'required|integer',
			'vehicle_id' => 'required|integer',
			'driver_id' => 'required|integer',
			'travellers' => 'required|integer',
			'pickup_addr' => 'required',
			'dest_addr' => 'required|different:pickup_addr',
			'pickup_datetime' => 'required',
			'dropoff_datetime' => 'required',
			'udf' => 'nullable|array',

		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {

			$xx = $this->check_booking($request->pickup_datetime, $request->dropoff_datetime, $request->vehicle_id);
			if ($xx) {
				$booking = Bookings::create([
					'customer_id' => $request->customer_id,
					'vehicle_id' => $request->vehicle_id,
					'user_id' => Auth::id(),
					'pickup' => date('Y-m-d H:i:s', strtotime($request->pickup_datetime)),
					'dropoff' => date('Y-m-d H:i:s', strtotime($request->dropoff_datetime)),
					'pickup_addr' => $request->pickup_addr,
					'dest_addr' => $request->dest_addr,
					'travellers' => $request->travellers,
					'status' => 0,
					'driver_id' => $request->driver_id,
					'note' => $request->note,
				]);

				$dropoff = Carbon::parse($booking->dropoff);
				$pickup = Carbon::parse($booking->pickup);
				$diff = $pickup->diffInMinutes($dropoff);
				$booking->duration = $diff;
				$booking->udf = serialize($request->udf);
				$booking->accept_status = 1; //0=yet to accept, 1= accept
				$booking->ride_status = "Upcoming";
				$booking->booking_type = 1;
				$booking->journey_date = date('d-m-Y', strtotime($booking->pickup));
				$booking->journey_time = date('H:i:s', strtotime($booking->pickup));
				$booking->save();
				$this->booking_notification($booking->id);
				// browser notification
				$this->push_notification($booking->id);
				if (Hyvikk::email_msg('email') == 1) {
					Mail::to($booking->customer->email)->send(new VehicleBooked($booking));
					Mail::to($booking->driver->email)->send(new DriverBooked($booking));

				}

				Address::updateOrCreate(['customer_id' => $request->customer_id, 'address' => $request->pickup_addr]);

				Address::updateOrCreate(['customer_id' => $request->customer_id, 'address' => $request->dest_addr]);
				$data['success'] = "1";
				$data['message'] = "Booking added successfully!";
				$data['data'] = "";

			} else {
				$data['success'] = "0";
				$data['message'] = "Selected Vehicle is not Available in Given Timeframe";
				$data['data'] = "";

			}
		}
		return $data;
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

		if ($driver->fcm_id != null && $driver->is_available == 1) {
			// PushNotification::app('appNameAndroid')
			//     ->to($driver->fcm_id)
			//     ->send($data);

			$push = new PushNotification('fcm');
			$push->setMessage($data)
				->setApiKey(env('server_key'))
				->setDevicesToken([$driver->fcm_id])
				->send();

		}

	}

	protected function check_edit_booking($pickup, $dropoff, $vehicle, $id) {

		$chk = Bookings::where('id', '!=', $id)->where("status", 0)->where('vehicle_id', $vehicle)
			->where(function ($query) use ($pickup, $dropoff) {
				$query->whereBetween('pickup', [$pickup, $dropoff])
					->orWhereBetween('dropoff', [$pickup, $dropoff]);
			})
			->get();
		// dd($chk);
		if (count($chk) > 0) {
			return false;
		} else {
			return true;
		}

	}

	protected function check_booking($pickup, $dropoff, $vehicle) {
		$chk = Bookings::where("status", 0)->where('vehicle_id', $vehicle)->whereBetween('pickup', [$pickup, $dropoff])->orWhereBetween('dropoff', [$pickup, $dropoff])->get();

		if (count($chk) > 0) {
			return false;
		} else {
			return true;
		}

	}

	public function bookings() {
		if (Auth::user()->user_type == "C") {
			$records = Bookings::where('customer_id', Auth::user()->id)->orderBy('id', 'desc')->get();
		} elseif (Auth::user()->group_id == null || Auth::user()->user_type == "S") {
			$records = Bookings::orderBy('id', 'desc')->get();
		} else {
			$vehicle_ids = VehicleModel::where('group_id', Auth::user()->group_id)->pluck('id')->toArray();
			$records = Bookings::whereIn('vehicle_id', $vehicle_ids)->orderBy('id', 'desc')->get();
		}
		$details = array();
		foreach ($records as $row) {
			$udf = "";
			$blank = array();
			if (unserialize($row->getMeta('udf'))) {
				$test = unserialize($row->getMeta('udf'));
				foreach ($test as $key => $val) {
					$blank[] = array(
						'name' => $key,
						'value' => $val,
					);
				}
				$udf = $blank;
			}
			$details[] = array(
				'id' => $row->id,
				'customer_id' => $row->customer_id,
				'vehicle_id' => $row->vehicle_id,
				'pickup_datetime' => $row->pickup,
				'dropoff_datetime' => $row->dropoff,
				'pickup_addr' => $row->pickup_addr,
				'dest_addr' => $row->dest_addr,
				'travellers' => $row->travellers,
				'status' => $row->status,
				'driver_id' => $row->driver_id,
				'note' => $row->note,
				'tax_total' => ($row->tax_total) ? $row->tax_total : "",
				'currency' => Hyvikk::get('currency'),
				'customer' => $row->customer->name,
				'vehicle' => ($row->vehicle_id) ? $row->vehicle->make_name . " - " . $row->vehicle->model_name . " - " . $row->vehicle->license_plate : "",
				'driver' => ($row->driver_id) ? $row->driver->name : "",
				'ride_status' => $row->ride_status,
				'journey_status' => ($row->status == 1) ? "Completed" : "Not Completed",
				'receipt' => ($row->receipt) ? $row->receipt : 0,
				'payment' => ($row->payment) ? $row->payment : 0,
				"udf" => $udf,
			);
		}
		$data['success'] = "1";
		$data['message'] = "Data fetched!";
		$data['data'] = $details;
		return $data;
	}
}
