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

class PartsModel extends Model {
	use SoftDeletes;
	protected $dates = ['deleted_at'];
	protected $table = 'parts';
	protected $fillable = [
		'image',
		'barcode',
		'number',
		'description',
		'unit_cost',
		'vendor_id',
		'manufacturer',
		'note',
		'stock',
		'udf',
		'category_id',
		'user_id',
		'status',
		'availability',
		'title',
		'year',
		'model',
	];

	public function vendor() {
		return $this->hasOne("App\Model\Vendor", "id", "vendor_id")->withTrashed();
	}

	public function category() {
		return $this->hasOne("App\Model\PartsCategoryModel", "id", "category_id")->withTrashed();
	}
}
