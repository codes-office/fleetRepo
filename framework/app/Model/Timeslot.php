<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timeslot extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',        // User who created the timeslot
        'company_id',     // Company associated with the timeslot
        'active',         // Indicates if the timeslot is active
        'log',            // Type of log (login or logout)
        'from_time',      // Starting time of the timeslot in hh:mm format
        'to_time',        // Ending time of the timeslot in hh:mm format
        'days_available', // JSON array for the available days
    ];
    
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
