<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExternalApiService {
    
    // 1. Integrasi Open-Meteo API (Tanpa API Key)
    public function getWeatherData($lat, $lon) {
        try {
            $response = Http::get("https://api.open-meteo.com/v1/forecast", [
                'latitude' => $lat,
                'longitude' => $lon,
                'current_weather' => true,
                'hourly' => 'rain,weathercode'
            ]);
            
            return $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
            Log::error("Open-Meteo API Error: " . $e->getMessage());
            return null;
        }
    }

    // 2. Integrasi World Bank API (Mencari Tren Inflasi Negara)
    public function getInflationData($countryIso) {
        try {
            // FP.CPI.TOTL.ZG adalah kode indikator untuk Inflasi di World Bank
            $response = Http::get("https://api.worldbank.org/v2/country/{$countryIso}/indicator/FP.CPI.TOTL.ZG", [
                'format' => 'json',
                'per_page' => 5
            ]);
            
            $data = $response->json();
            return (isset($data[1])) ? $data[1] : null;
        } catch (\Exception $e) {
            Log::error("World Bank API Error: " . $e->getMessage());
            return null;
        }
    }

    // 3. Integrasi ExchangeRate API (Real-time Currency)
    public function getExchangeRate($baseCurrency) {
        try {
            // Menggunakan endpoint free tanpa key
            $response = Http::get("https://open.er-api.com/v6/latest/{$baseCurrency}");
            return $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
            Log::error("ExchangeRate API Error: " . $e->getMessage());
            return null;
        }
    }
}