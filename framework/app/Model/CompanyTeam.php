<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyTeam extends Model
{
    use HasFactory;
    protected $table = 'companyteams';
    public $timestamps = false; // Disable timestamps
	protected $fillable = ['name', 'team_manager', 'date'];
}
