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

class ReviewModel extends Model {
	use SoftDeletes;
	protected $dates = ['deleted_at'];
	protected $table = 'reviews';
	protected $fillable = ['user_id', 'booking_id', 'driver_id', 'ratings', 'review_text'];

	public function user() {
		return $this->hasOne("App\Model\User", "id", "user_id")->withTrashed();
	}

}
