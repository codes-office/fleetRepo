<?php
/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

*/

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PasswordResetModel extends Model {
	protected $table = "password_resets";
	protected $fillable = ['email', 'token', 'created_at'];
	public $timestamps = false;
}
