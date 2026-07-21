<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Port extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_id',
        'port_name',
        'status',
        'country_code',
        'country_iso',
        'latitude',
        'longitude',
    ];
}