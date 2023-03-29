<?php

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Currency::create([
            "name" => "Peso chileno",
            "code" => "CLP",   
            "country_id"   => 1,
        ]);

        Currency::create([
            "name" => "Peso colombiano",
            "code" => "COP",           
            "country_id"   => 2,
        ]);
    }
}
