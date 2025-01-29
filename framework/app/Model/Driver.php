<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory;
    protected $fillable = [
        'Driver_name',
        'city',
        'DOB',
        'phone_code',
        'phone',
        'api_token',
        'remember_token',
        'emp_id',
        'vendor_id',
        'license_number',
        'license_number_date',
        'induction_date',
        'badge_number',
        'badge_number_date',
        'alternate_gov_id',
        'alternate_gov_id_number',
        'background_verification_status',
        'background_verification_date',
        'police_verification_status',
        'police_verification_date',
        'medical_verification_status',
        'medical_verification_date',
        'training_date',
        'eye_test_date',
        'driver_license_image',
        'induction_file',
        'alternate_gov_file',
        'background_verification_file',
        'police_verification_file',
        'medical_verification_file',
        'training_file',
        'eye_test_file',
        'documents_file',
        'current_address_file',
    ];
}
