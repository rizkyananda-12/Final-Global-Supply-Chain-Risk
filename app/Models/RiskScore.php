<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiskScore extends Model
{
    protected $fillable = [
        'country_id',
        'total_risk_score',
        'status',
        'current_inflation',
        'gdp',
        'population'
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}