<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Port;
use App\Models\RiskScore;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalCountries = Country::query()->count();
        $activePorts = Port::query()->where('status', 'active')->count();
        $avgRisk = RiskScore::query()->avg('total_risk_score') ?? 0;
        $countries = Country::all();

        return view('dashboard', compact('totalCountries', 'activePorts', 'avgRisk', 'countries'));
    }

    public function getCountryData(string $iso): JsonResponse
    {
        /** @var Country|null $country */
        $country = Country::query()->where('iso_code', $iso)->first();

        if (!$country) {
            return response()->json(['error' => 'Country not found'], 404);
        }

        /** @var RiskScore|null $risk */
        $risk = RiskScore::query()->where('country_id', $country->id)->latest()->first();

        return response()->json([
            'name'       => $country->name,
            'currency'   => $country->currency ?? 'USD',
            'inflation'  => ($country->inflation ?? 0) . '%',
            'gdp'        => '$' . number_format($country->gdp ?? 0),
            'population' => number_format($country->population ?? 0),
            'risk_score' => $risk ? $risk->total_risk_score : 42.5,
        ]);
    }
}