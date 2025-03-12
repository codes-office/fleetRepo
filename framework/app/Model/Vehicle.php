<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'vehicle'; // Define the table name if different from Laravel's convention
    
    protected $primaryKey = 'id'; // Define primary key if necessary

    protected $fillable = [
        'user_id',
        'vendor_id',
        'vehicle_id',
        'registration_no',
        'status',
        'inactive_reason',
        'sim_number',
        'device_imei',
        'vehicle_type',
        'contract',
        'working_time',
        'change_contract_from',
        'start_hour',
        'start_minute',
        'send_audit_sms',
        'driver_id',
        'mobile_number',
        'alternative_number',
        'comments',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $dates = ['deleted_at', 'change_contract_from', 'created_at', 'updated_at'];

    public function driver() {
        return $this->hasOne("App\Models\DriverVehicleModel", "vehicle_id", "id");
    }

    public function drivers() {
        return $this->belongsToMany("App\Models\User", 'driver_vehicle', 'vehicle_id', 'driver_id')->using("App\Models\DriverVehicleModel");
    }

    public function income() {
        return $this->hasMany("App\Models\IncomeModel", "vehicle_id", "id")->withTrashed();
    }

    public function expense() {
        return $this->hasMany("App\Models\Expense", "vehicle_id", "id")->withTrashed();
    }

    public function acq() {
        return $this->hasMany("App\Models\AcquisitionModel", "vehicle_id", "id");
    }

    public function group() {
        return $this->hasOne("App\Models\VehicleGroupModel", "id", "group_id")->withTrashed();
    }

    public function reviews() {
        return $this->hasMany("App\Models\VehicleReviewModel", "vehicle_id", "id");
    }

    public function types() {
        return $this->hasOne("App\Models\VehicleTypeModel", "id", "type_id")->withTrashed();
    }
}
