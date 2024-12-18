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

class NotesModel extends Model {
	use SoftDeletes;
	protected $dates = ['deleted_at'];
	protected $table = 'notes';
	protected $fillable = ['user_id', 'vehicle_id', 'customer_id', 'note', 'submitted_on', 'status'];

	public function vehicle() {
		return $this->belongsTo("App\Model\VehicleModel", "vehicle_id", "id")->withTrashed();
	}

	public function customer() {
		return $this->belongsTo("App\Model\User", "customer_id", "id")->withTrashed();
	}

}
