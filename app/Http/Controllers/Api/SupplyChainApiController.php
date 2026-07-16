<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Country;
use App\Services\ExternalApiService;
use App\Services\RiskScoringEngine;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;   

class SupplyChainApiController extends Controller {
    protected $apiService;
    protected $riskEngine;

    public function __construct(ExternalApiService $apiService, RiskScoringEngine $riskEngine) {
        $this->apiService = $apiService;
        $this->riskEngine = $riskEngine;
    }

    public function getCountries() {
        return response()->json(\App\Models\Country::all(), 200);
    }

    public function getRiskScore(Request $request) {
        $countryIso = $request->get('iso', 'DE'); 
        $country = Country::query()->where('iso2', $countryIso)->first();

        if (!$country) {
            return response()->json(['message' => 'Country not found'], 404);
        }

        $latLongs = ['DE' => [52.52, 13.41], 'CN' => [39.90, 116.40], 'ID' => [-6.20, 106.81], 'AU' => [-35.28, 149.13]];
        $coords = $latLongs[$countryIso] ?? [0.0, 0.0];
        
        $weatherData = $this->apiService->getWeatherData($coords[0], $coords[1]);
        $windSpeed = $weatherData['current_weather']['windspeed'] ?? 0;
        $weatherRisk = $windSpeed > 30 ? 85 : ($windSpeed > 15 ? 50 : 15);

        $inflationData = $this->apiService->getInflationData($countryIso);
        $latestInflation = $inflationData[0]['value'] ?? $country->inflation;
        $inflationRisk = $latestInflation > 5 ? 80 : ($latestInflation > 2 ? 40 : 10);

        $gnewsKey = "MASUKKAN_GNEWS_API_KEY_KAMU_DISINI"; 
        $cacheKey = "news_risk_" . $countryIso;
        $sentimentResult = Cache::remember($cacheKey, 3600, function () use ($country, $gnewsKey) {
            $newsResponse = Http::get("https://gnews.io/api/v4/search?q=logistics+{$country->name}&token={$gnewsKey}&lang=en");
            $sentimentText = "stable trade and growth observed"; // fallback
            if ($newsResponse->successful() && isset($newsResponse->json()['articles'])) {
                $articles = $newsResponse->json()['articles'];
            if (count($articles) > 0) {
                $sentimentText = $articles[0]['title'] . " " . $articles[0]['description'];
                }
            }
            return $this->riskEngine->analyzeNewsSentiment($sentimentText);
        });

    $newsRisk = $sentimentResult['risk_rating'];

        $exchangeData = $this->apiService->getExchangeRate($country->currency_code);
        $currencyRisk = isset($exchangeData['rates']['USD']) && $exchangeData['rates']['USD'] < 0.5 ? 60 : 20;

        $finalRisk = $this->riskEngine->calculateTotalRisk($weatherRisk, $inflationRisk, $newsRisk, $currencyRisk);

        return response()->json([
            'country' => $country->name,
            'iso2' => $countryIso,
            'currency' => $country->currency_code,
            'metrics' => [
                'gdp' => $country->gdp,
                'population' => $country->population,
                'current_windspeed' => $windSpeed . " km/h",
                'current_inflation' => round($latestInflation, 2) . "%"
            ],
            'sentiment_analysis' => [
                'positive' => $sentimentResult['positive'] . "%",
                'neutral' => $sentimentResult['neutral'] . "%",
                'negative' => $sentimentResult['negative'] . "%"
            ],
            'components_score' => [
                'weather' => $weatherRisk,
                'inflation' => $inflationRisk,
                'currency' => $currencyRisk,
                'news' => $newsRisk
            ],
            'total_risk_score' => $finalRisk['score'],
            'status' => $finalRisk['status']
        ], 200);
    }

        public function getPorts(Request $request) {
         $countryIso = $request->get('iso', 'ID');
         $samplePorts = [
            'ID' => [['name' => 'Tanjung Priok', 'lat' => -6.10, 'lon' => 106.89], ['name' => 'Tanjung Perak', 'lat' => -7.20, 'lon' => 112.73]],
            'DE' => [['name' => 'Port of Hamburg', 'lat' => 53.54, 'lon' => 9.93]],
            'CN' => [['name' => 'Port of Shanghai', 'lat' => 31.22, 'lon' => 121.48]],
            'AU' => [['name' => 'Port of Sydney', 'lat' => -33.86, 'lon' => 151.21]],
            'JP' => [['name' => 'Port of Tokyo', 'lat' => 35.62, 'lon' => 139.79]],
            'US' => [['name' => 'Port of Los Angeles', 'lat' => 33.74, 'lon' => -118.26]],
            'SG' => [['name' => 'Port of Singapore', 'lat' => 1.26, 'lon' => 103.81]],
            'GB' => [['name' => 'Port of Felixstowe', 'lat' => 51.96, 'lon' => 1.31]],
            'CA' => [['name' => 'Port of Vancouver', 'lat' => 49.29, 'lon' => -123.11]],
            'KR' => [['name' => 'Port of Busan', 'lat' => 35.10, 'lon' => 129.04]],
            'IN' => [['name' => 'Nhava Sheva (Port of Mumbai)', 'lat' => 18.95, 'lon' => 72.95]],
        ];

        $ports = isset($samplePorts[$countryIso]) ? $samplePorts[$countryIso] : [];
        return response()->json($ports);
    }
}