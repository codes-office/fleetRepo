<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Model\Company;
use App\Models\VehicleType; 

class Contractdb extends Model {
    use HasFactory;

    protected $table = 'contractdb';

    protected $fillable = [
        'id',
        'contractType',
        'contractTypePackage',
        'shortCode',
        'numberOfDuties',
        'allottedKmPerMonth',
        'minHoursPerDay',
        'packageCostPerMonth',
        'pricingForExtraDuty',
        'costPerKmAfterMinKm',
        'costPerHourAfterMinHours',
        'garageKmOnReporting',
        'garageHoursPerDay',
        'baseDieselPrice',
        'mileage',
        'seatingCapacity',
        'acPriceAdjustmentPerKm',
        'minTripsPerMonth',
        'Vechiletype',
        'company_name',
    ];


    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id'); // Use correct foreign key
    }
}
