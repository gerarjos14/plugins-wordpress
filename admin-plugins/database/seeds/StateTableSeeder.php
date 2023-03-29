<?php

use App\Models\State;
use Illuminate\Database\Seeder;

class StateTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $states = json_decode(file_get_contents(resource_path('comunas.json')), true);
      foreach ($states as $value) {
        foreach ($value['comunas'] as $comuna) {
          State::create([
            'name' => $comuna,
            'country_id' => 1,
          ]);
        }
      }
    }
}
