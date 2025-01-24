<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleContract extends Model
{
    use HasFactory;

    // Define the table associated with the model
    protected $table = 'vechilecontract';

    // Specify the primary key for the table
    protected $primaryKey = 'vec_id'; // Assuming 'vec_id' is the primary key

    // Indicate if the IDs are auto-incrementing
    public $incrementing = true;

    // Specify the type of the primary key
    protected $keyType = 'int';

    // Specify the fields that can be mass-assigned
    protected $fillable = ['Name', 'Vechiletype', 'company_name'];

    // Disable timestamps if the table does not have created_at and updated_at columns
    public $timestamps = false;
    
    public function user()
    {
        return $this->belongsTo(User::class,"user_id");
    }
    public function company()
    {
        return $this->belongsTo(User::class, 'company_name', 'id'); // Maps company_name in VehicleContract to id in User
    }
}


