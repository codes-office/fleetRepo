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

class FareSettings extends Model {
	use SoftDeletes;
	protected $dates = ['deleted_at'];
	protected $fillable = [
		'fare_name', 'fare_value', 'type_id', 'slab_index',
	];
	protected $table = "fare_settings";

	public static function get($key) {

		return ApiSettings::whereName($key)->first()->key_value;

	}
	public function vehicleType()
	{
		return $this->belongsTo(VehicleTypeModel::class, 'type_id');
	}
}
