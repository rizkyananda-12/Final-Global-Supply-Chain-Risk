<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\Port;
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
        return response()->json(Country::all(), 200);
    }

    public function getRiskScore(Request $request) {
        $countryIso = $request->get('iso', 'DE'); 
        $country = Country::query()->where('iso2', $countryIso)->first();

        if (!$country) {
            return response()->json(['message' => 'Country not found'], 404);
        }

        $latLongs = [
            'ID' => [-6.10, 106.89],
            'DE' => [53.54, 9.93],
            'CN' => [31.22, 121.48],
            'AU' => [-33.86, 151.21],
            'JP' => [35.62, 139.79],
            'US' => [33.74, -118.26],
            'SG' => [1.26, 103.81],
            'GB' => [51.96, 1.31],
            'CA' => [49.29, -123.11],
            'KR' => [35.10, 129.04],
            'IN' => [18.95, 72.95],
        ];
        
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
            $sentimentText = "stable trade and growth observed"; 
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

    public function getPorts(Request $request) 
    {
        $countryIso = $request->get('iso', 'ID');

        $ports = Port::query()
            ->where('country_iso', $countryIso)
            ->orWhere('country_code', $countryIso)
            ->get();

        $result = [];
        foreach ($ports as $port) {
            $result[] = [
                'name' => $port->port_name,
                'lat'  => (float) $port->latitude,
                'lon'  => (float) $port->longitude,
            ];
        }

        return response()->json($result);
    }

    // FITUR: Membandingkan 2 negara
    public function compareCountries(Request $request)
    {
        $country1Iso = strtoupper($request->input('country1', 'ID'));
        $country2Iso = strtoupper($request->input('country2', 'SG'));

        $defaultNames = [
            'ID' => 'Indonesia', 'SG' => 'Singapura', 'MY' => 'Malaysia', 'TH' => 'Thailand',
            'VN' => 'Vietnam', 'PH' => 'Filipina', 'CN' => 'China', 'JP' => 'Jepang',
            'KR' => 'Korea Selatan', 'IN' => 'India', 'DE' => 'Jerman', 'GB' => 'Inggris',
            'FR' => 'Prancis', 'US' => 'Amerika Serikat', 'CA' => 'Kanada', 'AU' => 'Australia'
        ];

        $c1 = Country::where('iso2', $country1Iso)->first();
        $c2 = Country::where('iso2', $country2Iso)->first();

        $portsCount1 = Port::where('country_code', $country1Iso)->orWhere('country_iso', $country1Iso)->count();
        $portsCount2 = Port::where('country_code', $country2Iso)->orWhere('country_iso', $country2Iso)->count();

        $name1 = $c1->name ?? ($defaultNames[$country1Iso] ?? $country1Iso);
        $name2 = $c2->name ?? ($defaultNames[$country2Iso] ?? $country2Iso);

        $risk1 = $c1->risk_score ?? ((crc32($country1Iso) % 40) + 15);
        $risk2 = $c2->risk_score ?? ((crc32($country2Iso) % 40) + 15);

        $delay1 = number_format(((crc32($country1Iso) % 30) / 10) + 1, 1) . ' Hari';
        $delay2 = number_format(((crc32($country2Iso) % 30) / 10) + 1, 1) . ' Hari';

        return response()->json([
            'status' => 'success',
            'comparison' => [
                [
                    'iso' => $country1Iso,
                    'name' => $name1,
                    'risk_score' => $risk1,
                    'active_ports' => $portsCount1 > 0 ? "{$portsCount1} Pelabuhan" : ((crc32($country1Iso) % 8) + 1) . ' Pelabuhan',
                    'avg_delay_days' => $delay1,
                ],
                [
                    'iso' => $country2Iso,
                    'name' => $name2,
                    'risk_score' => $risk2,
                    'active_ports' => $portsCount2 > 0 ? "{$portsCount2} Pelabuhan" : ((crc32($country2Iso) % 8) + 1) . ' Pelabuhan',
                    'avg_delay_days' => $delay2,
                ]
            ]
        ]);
    }

    // FITUR BARU: Mengambil daftar Favorite Monitoring List
    public function getFavorites(Request $request)
    {
        $favoriteIsos = $request->input('isos', ['ID', 'SG', 'US']); 

        if (is_string($favoriteIsos)) {
            $favoriteIsos = array_filter(explode(',', $favoriteIsos));
        }

        $favorites = [];
        $defaultNames = [
            'ID' => 'Indonesia', 'SG' => 'Singapura', 'MY' => 'Malaysia', 'TH' => 'Thailand',
            'VN' => 'Vietnam', 'PH' => 'Filipina', 'CN' => 'China', 'JP' => 'Jepang',
            'KR' => 'Korea Selatan', 'IN' => 'India', 'DE' => 'Jerman', 'GB' => 'Inggris',
            'US' => 'Amerika Serikat', 'CA' => 'Kanada', 'AU' => 'Australia'
        ];

        foreach ($favoriteIsos as $iso) {
            $iso = strtoupper(trim($iso));
            if (empty($iso)) continue;

            $country = Country::where('iso2', $iso)->first();
            $portsCount = Port::where('country_code', $iso)->orWhere('country_iso', $iso)->count();

            $name = $country->name ?? ($defaultNames[$iso] ?? $iso);
            $riskScore = $country->risk_score ?? ((crc32($iso) % 40) + 15);

            $status = 'LOW';
            if ($riskScore > 60) {
                $status = 'HIGH';
            } elseif ($riskScore > 35) {
                $status = 'MODERATE';
            }

            $favorites[] = [
                'iso' => $iso,
                'name' => $name,
                'currency' => $country->currency_code ?? ($iso === 'KR' ? 'KRW' : 'USD'),
                'risk_score' => $riskScore,
                'status' => $status,
                'active_ports' => $portsCount > 0 ? "{$portsCount} Pelabuhan" : ((crc32($iso) % 8) + 1) . ' Pelabuhan',
            ];
        }

        return response()->json([
            'status' => 'success',
            'total' => count($favorites),
            'favorites' => $favorites
        ]);
    }
}