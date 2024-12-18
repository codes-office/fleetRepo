<?php

/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

namespace App\Model;

use App\Model\VehicleModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Kodeine\Metable\Metable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable {
	use Metable;
	use SoftDeletes;
	use Notifiable;
	use HasApiTokens;
	use HasRoles;
	use HasFactory;
	protected $dates = ['deleted_at'];
	protected $table = "users";
	protected $metaTable = 'users_meta'; //optional.
	protected $fillable = [
		'name', 'email', 'password', 'user_type', 'group_id', 'api_token',
	];

	protected $hidden = ['password', 'remember_token', 'api_token'];

	protected function getMetaKeyName() {
		return 'user_id'; // The parent foreign key
	}

	public function user_data() {
		return $this->hasMany("App\Model\UserData", 'user_id', 'id')->withTrashed();
	}

	public function bookings() {
		return $this->hasMany('App\Model\Bookings', 'driver_id')->withTrashed();
	}

	public function vehicle_detail() {
		return $this->hasMany('App\Model\VehicleModel', 'user_id', 'id')->withTrashed();
	}

	public function driver_vehicle() {
		return $this->hasOne("App\Model\DriverVehicleModel", "driver_id", "id");
	}

	public function vehicles() {
		return $this->belongsToMany(VehicleModel::class, 'driver_vehicle', 'driver_id', 'vehicle_id', 'id', 'id')->using(DriverVehicleModel::class);
	}

	// public function getUserMeta($key = '', $defaut = '') {

	//     $key = trim($key);

	//     if( '' == $key ){
	//         return $defaut;
	//     }

	//     $metas = $this->metas->filter(function ($metas) use ($key) {
	//         return $metas->key === $key;
	//     });

	//     if ($metas->count() > 0) {
	//         return $metas->first()->value;
	//     }

	//     return $defaut;
	// }

}
