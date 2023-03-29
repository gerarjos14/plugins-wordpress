<?php

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Country::create([
            "id"   => 1,
            "name" => "Chile",
            "code" => "CL",   
        ]);

        Country::create([
            "id"   => 2,
            "name" => "Colombia",
            "code" => "CO",           
        ]);
    }
}
