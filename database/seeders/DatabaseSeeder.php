<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class DatabaseSeeder extends Seeder {
    public function run(): void {
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@supplychain.com',
            'password' => bcrypt('password123')
        ]);

        $positives = ['growth', 'increase', 'profit', 'stable', 'improve', 'safe', 'boom', 'recovery'];
        foreach ($positives as $word) {
            DB::table('positive_words')->insert(['word' => $word]);
        }

        $negatives = ['war', 'crisis', 'inflation', 'delay', 'disaster', 'protest', 'strike', 'decrease', 'drop'];
        foreach ($negatives as $word) {
            DB::table('negative_words')->insert(['word' => $word]);
        }

        DB::table('countries')->insert([
            ['name' => 'Germany', 'iso2' => 'DE', 'currency_code' => 'EUR', 'gdp' => 4500000000000, 'inflation' => 2.1, 'population' => 84000000],
            ['name' => 'China', 'iso2' => 'CN', 'currency_code' => 'CNY', 'gdp' => 17000000000000, 'inflation' => 1.5, 'population' => 1400000000],
            ['name' => 'Indonesia', 'iso2' => 'ID', 'currency_code' => 'IDR', 'gdp' => 1300000000000, 'inflation' => 2.8, 'population' => 275000000],
            ['name' => 'Australia', 'iso2' => 'AU', 'currency_code' => 'AUD', 'gdp' => 1600000000000, 'inflation' => 3.6, 'population' => 26000000],
            ['name' => 'Japan', 'iso2' => 'JP', 'currency_code' => 'JPY', 'gdp' => 4200000000000, 'inflation' => 2.5, 'population' => 125000000],
            ['name' => 'United States', 'iso2' => 'US', 'currency_code' => 'USD', 'gdp' => 27000000000000, 'inflation' => 3.1, 'population' => 335000000]
        ]);
    }
}