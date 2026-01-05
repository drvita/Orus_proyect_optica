<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserSeeder extends Seeder
{

    public function run()
    {
        DB::table('users')->insert([
            "name" => "Orus Bot",
            "username" => "OrusBot",
            "email" => "OrusBot@domain.com",
            "rol" => 0,
            "password" => Hash::make("OrusBot#1000"),
            "api_token" => "8150b31694f597622bdc979cc7d35989b24f343e0d96ff77b5d3dcbb8717373e",
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('users')->insert([
            "name" => "Salvador Galindo",
            "username" => "Admin",
            "email" => "admin@domain.com",
            "rol" => 0,
            "password" => Hash::make("Pretty.01#"),
            "api_token" => "b650355aca5956bdae1fe8c3497f1b7b24e7302ff2165ea0803853084175d2e2",
            "created_at" => new Carbon('2020/01/01 00:00:01'),
            "updated_at" => new Carbon('2020/01/01 00:00:01')
        ]);
    }
}
