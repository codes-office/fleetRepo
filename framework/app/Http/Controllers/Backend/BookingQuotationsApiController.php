<?php

/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Model\Address;
use App\Model\BookingQuotationModel;
use App\Model\Bookings;
use App\Model\VehicleModel;
use Auth;
use Carbon\Carbon;
use Hyvikk;
use Illuminate\Http\Request;
use Validator;

class BookingQuotationsApiController extends Controller {

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

			BookingQuotationModel::whereIn('id', $request->ids)->delete();

			$data['success'] = "1";
			$data['message'] = "Records deleted successfully!";
			$data['data'] = "";
		}
		return $data;
	}

	public function receipt($id) {
		$details = array();
		$row = BookingQuotationModel::find($id);
		$details = array(
			'app_name' => Hyvikk::get('app_name'),
			'icon' => asset('assets/images/' . Hyvikk::get('icon_img')),
			'date' => date('Y-m-d'),
			'from_address' => Hyvikk::get('badd1') . ", " . Hyvikk::get('badd2') . ", " . Hyvikk::get('city') . ", " . Hyvikk::get('state') . ", " . Hyvikk::get('country'),
			'to_address' => $row->customer->address,
			'id' => $row->id,
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
		return $data;
	}

	public function reject(Request $request) {
		$validation = Validator::make($request->all(), [
			'id' => 'required|integer',
		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {
			$quote = BookingQuotationModel::find($request->id);
			$quote->status = 1;
			$quote->save();

			$data['success'] = "1";
			$data['message'] = "Booking quotation rejected successfully.";
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
			BookingQuotationModel::find($request->id)->delete();
			$data['success'] = "1";
			$data['message'] = "Record deleted successfully!";
			$data['data'] = "";
		}
		return $data;
	}

	public function approve(Request $request) {
		$validation = Validator::make($request->all(), [
			'id' => 'required|integer',
			'customer_id' => 'required|integer',
			'vehicle_id' => 'required|integer',
			'driver_id' => 'required|integer',
			'travellers' => 'required|integer',
			'pickup_addr' => 'required',
			'dest_addr' => 'required|different:pickup_addr',
			'pickup_datetime' => 'required',
			'dropoff_datetime' => 'required',
			'day' => 'required|integer',
			'mileage' => 'required|numeric',
			'waiting_time' => 'required|integer',
			'total' => 'required|numeric',
			'total_tax_percent' => 'required|numeric',
			'total_tax_charge_rs' => 'required|numeric',
			'tax_total' => 'required|numeric',
		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {
			// dd($request->all());
			$xx = $this->check_booking($request->pickup_datetime, $request->dropoff_datetime, $request->vehicle_id);
			if ($xx) {
				$id = Bookings::create([
					'customer_id' => $request->customer_id,
					'vehicle_id' => $request->vehicle_id,
					'user_id' => Auth::id(),
					'pickup' => $request->pickup_datetime,
					'dropoff' => $request->dropoff_datetime,
					'pickup_addr' => $request->pickup_addr,
					'dest_addr' => $request->dest_addr,
					'travellers' => $request->travellers,
					'status' => 0,
					'driver_id' => $request->driver_id,
					'note' => $request->note,
				])->id;

				$booking = Bookings::find($id);
				$dropoff = Carbon::parse($booking->dropoff);
				$pickup = Carbon::parse($booking->pickup);
				$diff = $pickup->diffInMinutes($dropoff);
				$booking->duration = $diff;
				$booking->accept_status = 1; //0=yet to accept, 1= accept
				$booking->ride_status = "Upcoming";
				$booking->booking_type = 1;
				$booking->journey_date = date('d-m-Y', strtotime($booking->pickup));
				$booking->journey_time = date('H:i:s', strtotime($booking->pickup));
				$booking->receipt = 1;
				$booking->day = $request->day;
				$booking->mileage = $request->mileage;
				$booking->waiting_time = $request->waiting_time;
				$booking->date = date('Y-m-d');
				$booking->total = $request->total;
				$booking->total_kms = $request->mileage;
				$booking->tax_total = $request->tax_total;
				$booking->total_tax_percent = $request->total_tax_percent;
				$booking->total_tax_charge_rs = $request->total_tax_charge_rs;
				$booking->save();

				$inc_id = IncomeModel::create([
					"vehicle_id" => $request->vehicle_id,
					"amount" => $request->tax_total,
					"user_id" => $request->customer_id,
					"date" => date('Y-m-d'),
					"mileage" => $request->mileage,
					"income_cat" => 1,
					"income_id" => $booking->id,
					"tax_percent" => $request->total_tax_percent,
					"tax_charge_rs" => $request->total_tax_charge_rs,
				])->id;

				BookingIncome::create(['booking_id' => $booking->id, "income_id" => $inc_id]);
				Address::updateOrCreate(['customer_id' => $request->customer_id, 'address' => $request->pickup_addr]);

				Address::updateOrCreate(['customer_id' => $request->customer_id, 'address' => $request->dest_addr]);
				$this->booking_notification($booking->id);
				if (Hyvikk::email_msg('email') == 1) {
					Mail::to($booking->customer->email)->send(new VehicleBooked($booking));
					Mail::to($booking->driver->email)->send(new DriverBooked($booking));
				}
				BookingQuotationModel::find($request->id)->delete();
				$data['success'] = "1";
				$data['message'] = "Booking quotation approved successfully and added to bookings.";
				$data['data'] = "";

			} else {
				$data['success'] = "0";
				$data['message'] = "Selected Vehicle is not Available in Given Timeframe";
				$data['data'] = "";
			}
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
			'day' => 'required|integer',
			'mileage' => 'required|numeric',
			'waiting_time' => 'required|integer',
			'total' => 'required|numeric',
			'total_tax_percent' => 'required|numeric',
			'total_tax_charge_rs' => 'required|numeric',
			'tax_total' => 'required|numeric',
		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {

			$xx = $this->check_booking($request->pickup_datetime, $request->dropoff_datetime, $request->vehicle_id);
			if ($xx) {
				BookingQuotationModel::where('id', $request->id)->update([
					'vehicle_id' => $request->vehicle_id,
					'pickup' => date('Y-m-d H:i:s', strtotime($request->pickup_datetime)),
					'dropoff' => date('Y-m-d H:i:s', strtotime($request->dropoff_datetime)),
					'pickup_addr' => $request->pickup_addr,
					'dest_addr' => $request->dest_addr,
					'travellers' => $request->travellers,
					'status' => 0,
					'driver_id' => $request->driver_id,
					'note' => $request->note,
					'day' => $request->day,
					'mileage' => $request->mileage,
					'waiting_time' => $request->waiting_time,
					'total' => $request->total,
					'tax_total' => $request->tax_total,
					'total_tax_percent' => $request->total_tax_percent,
					'total_tax_charge_rs' => $request->total_tax_charge_rs,
				]);

				$data['success'] = "1";
				$data['message'] = "Booking quotation updated successfully!";
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
			'day' => 'required|integer',
			'mileage' => 'required|numeric',
			'waiting_time' => 'required|integer',
			'total' => 'required|numeric',
			'total_tax_percent' => 'required|numeric',
			'total_tax_charge_rs' => 'required|numeric',
			'tax_total' => 'required|numeric',
		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {

			$xx = $this->check_booking($request->pickup_datetime, $request->dropoff_datetime, $request->vehicle_id);
			if ($xx) {
				BookingQuotationModel::create([
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
					'day' => $request->day,
					'mileage' => $request->mileage,
					'waiting_time' => $request->waiting_time,
					'total' => $request->total,
					'tax_total' => $request->tax_total,
					'total_tax_percent' => $request->total_tax_percent,
					'total_tax_charge_rs' => $request->total_tax_charge_rs,
				]);

				Address::updateOrCreate(['customer_id' => $request->customer_id, 'address' => $request->pickup_addr]);

				Address::updateOrCreate(['customer_id' => $request->customer_id, 'address' => $request->dest_addr]);
				$data['success'] = "1";
				$data['message'] = "Booking quotation added successfully!";
				$data['data'] = "";

			} else {
				$data['success'] = "0";
				$data['message'] = "Selected Vehicle is not Available in Given Timeframe";
				$data['data'] = "";

			}
		}
		return $data;
	}

	protected function check_booking($pickup, $dropoff, $vehicle) {
		$chk = Bookings::where("status", 0)->where('vehicle_id', $vehicle)->whereBetween('pickup', [$pickup, $dropoff])->orWhereBetween('dropoff', [$pickup, $dropoff])->get();

		if (count($chk) > 0) {
			return false;
		} else {
			return true;
		}

	}

	public function quotes() {
		if (Auth::user()->group_id == null || Auth::user()->user_type == "S") {
			$vehicle_ids = VehicleModel::pluck('id')->toArray();
		} else {
			$vehicle_ids = VehicleModel::where('group_id', Auth::user()->group_id)->pluck('id')->toArray();
		}

		$records = BookingQuotationModel::whereIn('vehicle_id', $vehicle_ids)->orderBy('id', 'desc')->get();
		$details = array();
		foreach ($records as $row) {

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
				'status_label' => ($row->status == 0) ? "Pending" : "Rejected",
				'driver_id' => $row->driver_id,
				'note' => $row->note,
				'day' => $row->day,
				'mileage' => $row->mileage,
				'waiting_time' => $row->waiting_time,
				'total' => $row->total,
				'tax_total' => $row->tax_total,
				'total_tax_percent' => $row->total_tax_percent,
				'total_tax_charge_rs' => $row->total_tax_charge_rs,
				'currency' => Hyvikk::get('currency'),
				'customer' => $row->customer->name,
				'vehicle' => $row->vehicle->make_name . " - " . $row->vehicle->model_name . " - " . $row->vehicle->license_plate,
				'driver' => $row->driver->name,
			);
		}
		$data['success'] = "1";
		$data['message'] = "Data fetched!";
		$data['data'] = $details;
		return $data;
	}
}
