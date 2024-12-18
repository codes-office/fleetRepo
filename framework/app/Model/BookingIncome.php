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

class BookingIncome extends Model {

	use SoftDeletes;
	protected $dates = ['deleted_at'];
	protected $table = "booking_income";
	protected $fillable = ['booking_id', 'income_id'];

	public function booking_income() {
		return $this->hasOne("App\Model\IncomeModel", "id", "income_id")->withTrashed();
	}

}
