<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            "name" => "Salvador Galindo",
            "username" => "Admin",
            "email" => "admin@domain.com",
            "rol" => 2,
            "password" => Hash::make("123123"),
            "api_token" => "8150b31694f597622bdc979cc7d35989b24f343e0d96ff77b5d3dcbb8717373e"
        ]);
    }
}
