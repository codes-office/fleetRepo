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

class Vendor extends Model {
	use SoftDeletes;
	use HasFactory;
	protected $dates = ['deleted_at'];
	protected $table = 'vendors';
	protected $fillable = ['user_id', 'name', 'type', 'website', 'note', 'phone', 'address1', 'address2', 'city', 'province', 'email', 'photo', 'udf', 'country', 'postal_code'];
}
