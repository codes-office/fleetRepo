<?php

/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleReviewModel extends Model {
	use SoftDeletes;
	protected $dates = ['deleted_at'];
	protected $table = "vehicle_review";
	protected $fillable = [
		'vehicle_id',
		'user_id',
		'reg_no',
		'kms_outgoing',
		'kms_incoming',
		'fuel_level_out',
		'fuel_level_in',
		'datetime_outgoing',
		'datetime_incoming',
		'petrol_card',
		'lights',
		'invertor',
		'car_mats',
		'int_damage',
		'int_lights',
		'ext_car',
		'tyre',
		'ladder',
		'leed',
		'power_tool',
		'ac',
		'head_light',
		'lock',
		'windows',
		'condition',
		'oil_chk',
		'suspension',
		'tool_box',
	];

	public function user() {
		return $this->hasOne("App\Model\User", "id", "user_id")->withTrashed();
	}

	public function vehicle() {
		return $this->hasOne("App\Model\VehicleModel", "id", "vehicle_id")->withTrashed();
	}
}
