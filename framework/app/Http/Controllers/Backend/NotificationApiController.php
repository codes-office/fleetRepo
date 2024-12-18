<?php

/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

namespace App\Http\Controllers\Backend;

// use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\ServiceReminderModel;
use App\Model\User;
use App\Model\VehicleModel;
use Auth;
use Hyvikk;

class NotificationApiController extends Controller {
	public function counts() {
		$r = 0;
		$i = 0;
		$l = 0;
		$d = 0;
		$s = 0;
		$user = Auth::user();
		foreach ($user->unreadNotifications as $notification) {
			if ($notification->type == "App\Notifications\RenewRegistration") {$r++;} elseif ($notification->type == "App\Notifications\RenewInsurance") {$i++;} elseif ($notification->type == "App\Notifications\RenewVehicleLicence") {$l++;} elseif ($notification->type == "App\Notifications\RenewDriverLicence") {$d++;} elseif ($notification->type == "App\Notifications\ServiceReminderNotification") {$s++;}

		}
		$n = $r + $i + $l + $d + $s;
		$data['success'] = "1";
		$data['message'] = "Data fetched!";
		$data['data'] = array(
			'registration' => $r,
			'insurance' => $i,
			'vehicle_licence' => $l,
			'driver_licende' => $d,
			'service_reminder' => $s,
			'total_count' => $n,
		);
		return $data;
	}

	public function vehicle_registration() {
		$user = Auth::user();
		$notifications = $user->notifications->where('type', 'App\Notifications\RenewRegistration');
		$notifications->markAsRead();
		$details = array();
		foreach ($notifications as $row) {
			$remaining_days = "Expired";
			if (strtotime($row->data['msg']) > strtotime("now")) {
				$to = \Carbon\Carbon::now();
				$from = \Carbon\Carbon::createFromFormat('Y-m-d', $row->data['date']);
				$remaining_days = $to->diffInDays($from);
			}
			$vehicle = VehicleModel::find($row->data['vid']);
			$details[] = array(
				'image' => ($vehicle->vehicle_image != null)?asset('uploads/' . $vehicle->vehicle_image): asset("assets/images/vehicle.jpeg"),
				'vehicle' => $vehicle->make_name . " - " . $vehicle->model_name,
				'notification' => __('fleet.reg_certificate') . date(Hyvikk::get('date_format'), strtotime($row->data['msg'])),
				'remaining_days' => $remaining_days,
			);
		}
		$data['success'] = "1";
		$data['message'] = "Data fetched!";
		$data['data'] = $details;
		return $data;
	}

	public function vehicle_insurance() {
		$user = Auth::user();
		$notifications = $user->notifications->where('type', 'App\Notifications\RenewInsurance');
		$notifications->markAsRead();
		$details = array();
		foreach ($notifications as $row) {
			$remaining_days = "Expired";
			if (strtotime($row->data['msg']) > strtotime("now")) {
				$to = \Carbon\Carbon::now();
				$from = \Carbon\Carbon::createFromFormat('Y-m-d', $row->data['date']);
				$remaining_days = $to->diffInDays($from);
			}
			$vehicle = VehicleModel::find($row->data['vid']);
			$details[] = array(
				'image' => ($vehicle->vehicle_image != null)?asset('uploads/' . $vehicle->vehicle_image): asset("assets/images/vehicle.jpeg"),
				'vehicle' => $vehicle->make_name . " - " . $vehicle->model_name,
				'notification' => __('fleet.vehicle_insurance') . date(Hyvikk::get('date_format'), strtotime($row->data['msg'])),
				'remaining_days' => $remaining_days,
			);
		}
		$data['success'] = "1";
		$data['message'] = "Data fetched!";
		$data['data'] = $details;
		return $data;
	}

	public function vehicle_license() {
		$user = Auth::user();
		$notifications = $user->notifications->where('type', 'App\Notifications\RenewVehicleLicence');
		$notifications->markAsRead();
		$details = array();
		foreach ($notifications as $row) {
			$remaining_days = "Expired";
			if (strtotime($row->data['msg']) > strtotime("now")) {
				$to = \Carbon\Carbon::now();
				$from = \Carbon\Carbon::createFromFormat('Y-m-d', $row->data['date']);
				$remaining_days = $to->diffInDays($from);
			}
			$vehicle = VehicleModel::find($row->data['vid']);
			$details[] = array(
				'image' => ($vehicle->vehicle_image != null)?asset('uploads/' . $vehicle->vehicle_image): asset("assets/images/vehicle.jpeg"),
				'vehicle' => $vehicle->make_name . " - " . $vehicle->model_name,
				'notification' => __('fleet.vehicle_licence') . date(Hyvikk::get('date_format'), strtotime($row->data['msg'])),
				'remaining_days' => $remaining_days,
			);
		}
		$data['success'] = "1";
		$data['message'] = "Data fetched!";
		$data['data'] = $details;
		return $data;
	}

	public function driver_license() {
		$user = Auth::user();
		$notifications = $user->notifications->where('type', 'App\Notifications\RenewDriverLicence');
		$notifications->markAsRead();
		$details = array();
		foreach ($notifications as $row) {
			$remaining_days = "Expired";
			if (strtotime($row->data['msg']) > strtotime("now")) {
				$to = \Carbon\Carbon::now();
				$from = \Carbon\Carbon::createFromFormat('Y-m-d', $row->data['date']);
				$remaining_days = $to->diffInDays($from);
			}
			$user = User::find($row->data['vid']);
			$details[] = array(
				'image' => ($user->driver_image != null)?asset('uploads/' . $user->driver_image): asset("assets/images/no-user.jpg"),
				'name' => $user->first_name . " " . $user->last_name,
				'notification' => __('fleet.driver_licence') . date(Hyvikk::get('date_format'), strtotime($row->data['msg'])),
				'remaining_days' => $remaining_days,
			);
		}
		$data['success'] = "1";
		$data['message'] = "Data fetched!";
		$data['data'] = $details;
		return $data;
	}

	public function service_reminder() {
		$user = Auth::user();
		$notifications = $user->notifications->where('type', 'App\Notifications\ServiceReminderNotification');
		$notifications->markAsRead();
		$details = array();
		foreach ($notifications as $row) {
			// dd($row->data['vid']);
			$remaining_days = "Expired";
			if (strtotime($row->data['date']) > strtotime("now")) {
				$to = \Carbon\Carbon::now();
				$from = \Carbon\Carbon::createFromFormat('Y-m-d', $row->data['date']);
				$remaining_days = $to->diffInDays($from);
			}
			$reminder = ServiceReminderModel::find($row->data['vid']);
			$details[] = array(
				'image' => ($reminder->vehicle->vehicle_image != null)?asset('uploads/' . $reminder->vehicle->vehicle_image): asset("assets/images/vehicle.jpeg"),
				'vehicle' => $reminder->vehicle->year . " " . $reminder->vehicle->make_name . " " . $reminder->vehicle->model_name,
				'vin' => $reminder->vehicle->vin,
				'license_plate' => $reminder->vehicle->license_plate,
				'notification' => $row->data['msg'],
				'remaining_days' => $remaining_days,
			);
		}
		$data['success'] = "1";
		$data['message'] = "Data fetched!";
		$data['data'] = $details;
		return $data;
	}
}
