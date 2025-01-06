<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyTeam extends Model
{
    use HasFactory;

    protected $table = 'companyteam'; // Define the table name if it's not the plural of the model name.

    // Define the primary key, assuming 'id' is the primary key
    protected $primaryKey = 'id';

    // Set the columns that are mass assignable
    protected $fillable = [
        'Company_id',
        'Team_Name',
        'Manager',
    ];

    // Disable timestamps if you don't have 'created_at' and 'updated_at' columns
    public $timestamps = false;

    // Define any relationships (if necessary)
    // Example: If 'company_id' references the 'Company' model
   // In CompanyTeam model
public function company() {
    return $this->belongsTo(User::class, 'Company_id', 'id'); // Adjust 'User' to the model name holding company info
}

}