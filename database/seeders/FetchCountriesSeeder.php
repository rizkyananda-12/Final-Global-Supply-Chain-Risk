<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use App\Models\Country; // Sesuaikan dengan model Country kamu

class FetchCountriesSeeder extends Seeder
{
    public function run(): void
    {
        // Panggil API negara
        $response = Http::get('https://restcountries.com/v3.1/all?fields=name,cca2,currencies,population');

        if ($response->successful()) {
            foreach ($response->json() as $data) {
                $iso2 = $data['cca2'] ?? null;
                if (!$iso2) continue;

                // Ambil kode mata uang pertama dari API (misal: USD, EUR, IDR)
                $currencies = $data['currencies'] ?? [];
                $currencyCode = !empty($currencies) ? array_key_first($currencies) : 'USD';

                Country::updateOrCreate(
                    ['iso2' => $iso2],
                    [
                        'name'          => $data['name']['common'] ?? '',
                        'currency_code' => $currencyCode,
                        'gdp'           => rand(100, 900) * 1000000000, // Dummy GDP
                        'inflation'     => rand(10, 50) / 10,           // Dummy Inflasi (1.0 - 5.0)
                        'population'    => $data['population'] ?? rand(1000000, 50000000),
                    ]
                );
            }
            $this->command->info('BERHASIL! Ratusan negara baru sudah masuk!');
        } else {
            $this->command->error('Gagal mengambil data dari API.');
        }
    }
}