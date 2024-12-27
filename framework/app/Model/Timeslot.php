<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timeslot extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'pickup_time', 'drop_time'];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
