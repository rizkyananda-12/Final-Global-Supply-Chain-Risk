<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Port;
use App\Models\Country;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder {
    public function run(): void {
        \App\Models\User::factory()->create([
            'name' => 'Admin Supply Chain',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password123'),
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
            ['name' => 'United States', 'iso2' => 'US', 'currency_code' => 'USD', 'gdp' => 27000000000000, 'inflation' => 3.1, 'population' => 335000000],
            ['name' => 'Singapore', 'iso2' => 'SG', 'currency_code' => 'SGD', 'gdp' => 500000000000, 'inflation' => 2.0, 'population' => 6000000],
            ['name' => 'United Kingdom', 'iso2' => 'GB', 'currency_code' => 'GBP', 'gdp' => 3100000000000, 'inflation' => 2.2, 'population' => 67000000],
            ['name' => 'Canada', 'iso2' => 'CA', 'currency_code' => 'CAD', 'gdp' => 2100000000000, 'inflation' => 2.4, 'population' => 39000000],
            ['name' => 'South Korea', 'iso2' => 'KR', 'currency_code' => 'KRW', 'gdp' => 1700000000000, 'inflation' => 2.6, 'population' => 51000000],
            ['name' => 'India', 'iso2' => 'IN', 'currency_code' => 'INR', 'gdp' => 3700000000000, 'inflation' => 4.8, 'population' => 1430000000]
            ]);
        {
        User::updateOrCreate(
            ['email' => 'admin@risk.com'],
            [
                'name' => 'Admin System',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'user@risk.com'],
            [
                'name' => 'User Operator',
                'password' => Hash::make('password123'),
                'role' => 'user',
            ]
        );

        $id = Country::updateOrCreate(
            ['iso_code' => 'ID'],
            [
                'name' => 'Indonesia',
                'currency' => 'IDR',
                'inflation' => 2.5,
                'gdp' => 1300000000000,
                'population' => 275000000,
            ]
        );

        $sg = Country::updateOrCreate(
            ['iso_code' => 'SG'],
            [
                'name' => 'Singapore',
                'currency' => 'SGD',
                'inflation' => 1.8,
                'gdp' => 400000000000,
                'population' => 5600000,
            ]
        );

        $cn = Country::updateOrCreate(
            ['iso_code' => 'CN'],
            [
                'name' => 'China',
                'currency' => 'CNY',
                'inflation' => 0.5,
                'gdp' => 17000000000000,
                'population' => 1400000000,
            ]
        );

        Port::create(['country_id' => $id->id, 'port_name' => 'Tanjung Priok', 'status' => 'active']);
        Port::create(['country_id' => $id->id, 'port_name' => 'Tanjung Perak', 'status' => 'active']);
        Port::create(['country_id' => $id->id, 'port_name' => 'Belawan Port', 'status' => 'active']);
        
        Port::create(['country_id' => $sg->id, 'port_name' => 'Port of Singapore', 'status' => 'active']);
        Port::create(['country_id' => $sg->id, 'port_name' => 'Jurong Port', 'status' => 'active']);

        Port::create(['country_id' => $cn->id, 'port_name' => 'Port of Shanghai', 'status' => 'active']);
        Port::create(['country_id' => $cn->id, 'port_name' => 'Ningbo-Zhoushan Port', 'status' => 'active']);
        Port::create(['country_id' => $cn->id, 'port_name' => 'Shenzhen Port', 'status' => 'active']);
    }
}
    }
