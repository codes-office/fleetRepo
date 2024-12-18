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

class BookingQuotationModel extends Model {
	use SoftDeletes;
	protected $dates = ['deleted_at'];
	protected $table = "booking_quotation";
	protected $fillable = [
		'customer_id', 'vehicle_id', 'user_id', 'pickup', 'dropoff', 'pickup_addr', 'dest_addr', 'travellers', 'status', 'comment', 'dropoff_time', 'driver_id', 'note', 'day', 'mileage', 'waiting_time', 'total', 'tax_total', 'total_tax_percent', 'total_tax_charge_rs',
	];

	public function vehicle() {
		return $this->hasOne("App\Model\VehicleModel", "id", "vehicle_id")->withTrashed();
	}

	public function customer() {
		return $this->hasOne("App\Model\User", "id", "customer_id")->withTrashed();
	}

	public function driver() {
		return $this->hasOne("App\Model\User", "id", "driver_id")->withTrashed();
	}

	public function user() {
		return $this->hasOne("App\Model\User", "id", "user_id")->withTrashed();
	}

}
