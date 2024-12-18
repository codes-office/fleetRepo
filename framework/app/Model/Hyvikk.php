<?php
/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

namespace App\Model;

use App\Model\ApiSettings;
use App\Model\EmailContent;
use App\Model\FareSettings;
use App\Model\FrontendModel;
use App\Model\Settings;
use App\Model\TwilioSettings;

class Hyvikk {

	public static function twilio($key) {
		$settings = array_pluck(TwilioSettings::all()->toArray(), 'value', 'name');
		return (is_array($key))?array_only($settings, $key): $settings[$key];
	}

	public static function get($key) {
		$settings = array_pluck(Settings::all()->toArray(), 'value', 'name');
		return (is_array($key))?array_only($settings, $key): $settings[$key];
	}

	public static function set($key, $val) {
		$settings = Settings::firstOrNew(array('name' => $key));
		$settings->value = $val;
		$settings->save();
		Cache::flush();
	}

	public static function api($key) {
		$settings = array_pluck(ApiSettings::all()->toArray(), 'key_value', 'key_name');
		return (is_array($key))?array_only($settings, $key): $settings[$key];
	}

	public static function fare($key) {
		$key = str_replace(' ', '', $key);
		$settings = array_pluck(FareSettings::all()->toArray(), 'key_value', 'key_name');
		return (is_array($key))?array_only($settings, $key): $settings[$key];
	}

	public static function email_msg($key) {
		$settings = array_pluck(EmailContent::all()->toArray(), 'value', 'key');
		return (is_array($key))?array_only($settings, $key): $settings[$key];
	}

	public static function frontend($key) {
		$settings = array_pluck(FrontendModel::all()->toArray(), 'key_value', 'key_name');
		return (is_array($key))?array_only($settings, $key): $settings[$key];
	}

	public static function payment($key) {
		$settings = array_pluck(PaymentSettings::all()->toArray(), 'value', 'name');
		return (is_array($key))?array_only($settings, $key): $settings[$key];
	}

	public static function chat($key) {
		$settings = array_pluck(ChatSettingsModel::all()->toArray(), 'value', 'name');
		return (is_array($key))?array_only($settings, $key): $settings[$key];
	}
}
