<?php

use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = \App\Models\User::where('role', \App\Models\User::ADMIN)->first();
        $colombia = [
            'id_country' => 2,
            'currency'   => 'COP',
        ];
        $chile = [
            'id_country' => 1,
            'currency'   => 'CLP',
        ];

        // Seeder Alegra
        $this->load_plan(\App\Models\Plan::ALEGRA, \App\Models\Plan::LIFETIME, 5.00, $admin->id, $colombia);
        $this->load_plan(\App\Models\Plan::ALEGRA, \App\Models\Plan::MONTH, 5.00, $admin->id, $colombia);
        $this->load_plan(\App\Models\Plan::ALEGRA, \App\Models\Plan::YEAR, 5.00, $admin->id, $colombia);

        // SIIGO
        $this->load_plan(\App\Models\Plan::SIIGO, \App\Models\Plan::LIFETIME, 5.00, $admin->id, $colombia);
        $this->load_plan(\App\Models\Plan::SIIGO, \App\Models\Plan::MONTH, 5.00, $admin->id, $colombia);
        $this->load_plan(\App\Models\Plan::SIIGO, \App\Models\Plan::YEAR, 5.00, $admin->id, $colombia);

        // FAC_CHILE
        $this->load_plan(\App\Models\Plan::FAC_CHILE, \App\Models\Plan::YEAR, 1.00, $admin->id, $chile);

        // BEON
        $this->load_plan(\App\Models\Plan::BEON, \App\Models\Plan::LIFETIME, 5.00, 3, $colombia);
        $this->load_plan(\App\Models\Plan::BEON, \App\Models\Plan::YEAR, 5.00, 3, $colombia);
        $this->load_plan(\App\Models\Plan::BEON, \App\Models\Plan::MONTH, 5.00, 3, $colombia);
    }

    /**
     * Función load_plan
     *
     * @param string $name_plan
     * @param string $time
     * @param double $value
     * @param integer $admin_id
     * @param array $data_country
     * @return void
     * @author Matías
     */
    public function load_plan(string $name_plan, string $time, $value, int $admin_id, array $data_country)
    {
        if ($data_country['id_country'] == 1) {

            $plan = new \App\Models\Plan();
            $plan->interval     = $time;
            $plan->amount       = $value;
            $plan->user_id      = $admin_id;
            $plan->platform     = $name_plan;
            $plan->qty_documents = 70; //Cantidad de documentos 
            $plan->country_id   = $data_country['id_country'];
            $plan->currency     = $data_country['currency'];
            $plan->save();
        } else {

            $plan = new \App\Models\Plan();

            $plan->name         = ($name_plan == \App\Models\Plan::BEON) ? 'BEON24' :  '';
            $plan->interval     = $time;
            $plan->amount       = $value;
            $plan->user_id      = $admin_id;
            $plan->platform     = $name_plan;
            $plan->country_id   = $data_country['id_country'];
            $plan->currency     = $data_country['currency'];
            $plan->product_id   = ($name_plan == \App\Models\Plan::BEON) ? 'prod_MemwuqacMTdO7K' :  '';
            $plan->plan_id      = ($name_plan == \App\Models\Plan::BEON) ? 'price_1LvTMRHWd2Y5Fkm7jRJyyY7H' :  '';

            $plan->save();
        }
    }
}
