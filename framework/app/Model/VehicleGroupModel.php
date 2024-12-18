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

class VehicleGroupModel extends Model {
	use SoftDeletes;
	protected $dates = ['deleted_at'];
	protected $table = 'vehicle_group';
	protected $fillable = ['name', 'description', 'note', 'user_id'];

	public function group() {
		return $this->hasMany("App\Model\VehicleModel", "group_id", "id")->withTrashed();
	}
}
