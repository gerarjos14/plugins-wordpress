<?php

use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new \App\Models\User();
        $user->name = 'Admin';
        $user->email = 'contacto@lars.net.co';
        $user->password = \Illuminate\Support\Facades\Hash::make('12345678');
        $user->role = \App\Models\User::ADMIN;
        $user->save();

        $agency             = new \App\Models\User();
        $agency->name       = 'Lars Chile';
        $agency->email      = 'agencia@agencia.com';
        $agency->password   = \Illuminate\Support\Facades\Hash::make('12345678');
        $agency->role       = \App\Models\User::AGENCY;
        $agency->country_id = 1; //CHILE
        $agency->save();
        $agency->bank_account()->create();

        $agency             = new \App\Models\User();
        $agency->name       = 'Lars BEON';
        $agency->email      = 'lars_beon@beon.com';
        $agency->password   = \Illuminate\Support\Facades\Hash::make('12345678');
        $agency->role       = \App\Models\User::AGENCY;
        $agency->country_id = 2; // COLOMBIA
        $agency->save();
        $agency->bank_account()->create();


        $customer                 = new \App\Models\User();
        $customer->name           = 'Cliente';
        $customer->email          = 'cliente@cliente.com';
        $customer->password       = \Illuminate\Support\Facades\Hash::make('12345678');
        $customer->role           = \App\Models\User::CUSTOMER;
        $customer->country_id     = 1; //CHILE
        $customer->parent_id      = $agency->id;
        $customer->allow_lifetime = false;

        $customer->save();
        $customer->key()->create([
            'alegra_user'           => 'contacto@lars.net.co',
            'alegra_token'          => 'ec883947073c3580e4ae', 
            'wc_consumer_key'       => 'ck_52e42a4bbf8fc60abe3b00958a56115568d6c31d',
            'wc_consumer_secret'    => 'cs_9d47c2362add23714149200d96783ae40ab79e58',
            'website'               => 'https://plugins.healtheworld.com.co/',
        ]);
    }
}
