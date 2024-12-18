<?php

/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Model\EmailContent;
use App\Model\User;
use Hyvikk;
use Illuminate\Http\Request;
use Validator;

class EmailSettingsApiController extends Controller {

	public function set_email_content(Request $request) {
		$validation = Validator::make($request->all(), [
			'type' => 'required|in:insurance,vehicle_licence,driving_licence,registration,service_reminder',
			'content' => 'required',
		]);

		$errors = $validation->errors();
		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";

		} else {
			$type = $request->type;
			if ($type == "insurance") {
				EmailContent::where('key', 'insurance')->update(['value' => $request->get('content')]);
			} elseif ($type == "vehicle_licence") {
				EmailContent::where('key', 'vehicle_licence')->update(['value' => $request->get('content')]);
			} elseif ($type == "driving_licence") {
				EmailContent::where('key', 'driving_licence')->update(['value' => $request->get('content')]);
			} elseif ($type == "registration") {
				EmailContent::where('key', 'registration')->update(['value' => $request->get('content')]);
			} elseif ($type == "service_reminder") {
				EmailContent::where('key', 'service_reminder')->update(['value' => $request->get('content')]);
			}

			$data['success'] = "1";
			$data['message'] = "Email content updated successfully!";
			$data['data'] = "";
		}
		return $data;
	}

	public function get_email_content() {
		$data['success'] = "1";
		$data['message'] = "Data Received.";
		$data['data'] = array(
			'insurance' => Hyvikk::email_msg('insurance'),
			'vehicle_licence' => Hyvikk::email_msg('vehicle_licence'),
			'driving_licence' => Hyvikk::email_msg('driving_licence'),
			'registration' => Hyvikk::email_msg('registration'),
			'service_reminder' => Hyvikk::email_msg('service_reminder'),
		);
		return $data;
	}

	public function save_email_settings(Request $request) {
		$validation = Validator::make($request->all(), [
			'user_ids' => 'required',
			'notification_ids' => 'required',
		]);

		$errors = $validation->errors();
		if (count($errors) > 0) {
			$data['success'] = 0;
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";

		} else {
			// $user_ids = [1];
			// $notification_ids = [1, 2, 3];
			$user_ids = $request->user_ids;
			$notification_ids = $request->notification_ids;
			EmailContent::where('key', 'users')->update(['value' => serialize($user_ids)]);
			EmailContent::where('key', 'options')->update(['value' => serialize($notification_ids)]);

			$data['success'] = "1";
			$data['message'] = "Email Notifications updated successfully!";
			$data['data'] = "";
		}
		return $data;
	}

	public function enable_disable(Request $request) {
		$validation = Validator::make($request->all(), [
			'value' => 'required|integer|in:0,1',
		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {
			$key = ($request->value == 1) ? "Enabled" : "Disabled";
			EmailContent::where('key', 'email')->update(['value' => $request->value]);
			$data['success'] = "1";
			$data['message'] = "Email notification has been " . $key . " successfully!";
			$data['data'] = "";
		}
		return $data;
	}

	public function get_email_settings() {
		$all_users = User::where('user_type', '!=', 'C')->where('user_type', '!=', 'D')->get();
		$user_list = array();
		foreach ($all_users as $row) {
			$user_list[] = array('name' => $row->name, 'id' => $row->id);
		}
		$selected_users = EmailContent::where('key', 'users')->first();
		$selected_options = EmailContent::where('key', 'options')->first();
		$options = array();
		$selected_user_list = array();
		if ($selected_options->value != null) {
			$options = unserialize($selected_options->value);
		}
		if ($selected_users->value != null) {
			$selected_user_list = unserialize($selected_users->value);
		}
		$notifications = array(
			["key" => "Registration Notification", "value" => 1],
			["key" => "Insurance Notification", "value" => 2],
			["key" => "Vehicle Licence Notification", "value" => 3],
			["key" => "Driving Licence Notification", "value" => 4],
			["key" => "Service Reminder Notification", "value" => 5],
		);
		$data['success'] = "1";
		$data['message'] = "Data fetched!";
		$data['data'] = array(
			'list_of_all_user' => $user_list,
			'selected_user_ids' => $selected_user_list,
			'selected_notifications' => $options,
			'notification_list' => $notifications,
			'email' => (Hyvikk::email_msg('email') == 1) ? "Enabled" : "Disabled",
			'email_value' => Hyvikk::email_msg('email'),
		);
		return $data;
	}
}
