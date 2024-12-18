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

class ServiceReminderModel extends Model {

	use SoftDeletes;
	protected $dates = ['deleted_at'];
	protected $table = "service_reminder";
	protected $fillable = ['vehicle_id', 'service_id', 'last_date', 'last_meter', 'user_id'];

	public function services() {
		return $this->hasOne("App\Model\ServiceItemsModel", "id", "service_id")->withTrashed();
	}

	public function vehicle() {
		return $this->belongsTo("App\Model\VehicleModel", "vehicle_id", "id")->withTrashed();
	}
}
