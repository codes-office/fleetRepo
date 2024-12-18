<?php

/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mechanic extends Model {
	use SoftDeletes;
	use HasFactory;
	protected $dates = ['deleted_at'];
	protected $fillable = [
		'email', 'name', 'contact_number', 'category', 'user_id',
	];
	protected $table = "mechanics";

}
