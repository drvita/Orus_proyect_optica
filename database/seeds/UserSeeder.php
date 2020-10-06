<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserSeeder extends Seeder{

    public function run(){
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
        DB::table('contacts')->insert([
            'name' => "Diana Martinez Glez",
            'rfc' => "MAGV191185MP3",
            'email' => "veritoo@domain.com",
            'type' => 0,
            'business' => 0,
            'telnumbers' => "3121236887",
            'birthday' => new Carbon("1985/11/19"),
            'domicilio' => "And Antonio Cardenas 334",
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('contacts')->insert([
            'name' => "Uriel Glez M",
            'rfc' => "GOMU160509TT4",
            'email' => "uriel@domain.com",
            'type' => 0,
            'business' => 0,
            'telnumbers' => "3123178569",
            'birthday' => new Carbon("2009/05/16"),
            'domicilio' => "Volcan chicon 144, Vista Volcanes",
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:01'),
            "updated_at" => new Carbon('2020/01/01 00:00:01')
        ]);
        DB::table('contacts')->insert([
            'name' => "Comicion Federal de Electricidad",
            'rfc' => "CFE600101B56",
            'email' => "cfe@domain.com",
            'type' => 0,
            'business' => 1,
            'telnumbers' => "3123178569,3123130000",
            'birthday' => new Carbon("1960/01/01"),
            'domicilio' => "Av Benito Juarez 600, Centro",
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:01'),
            "updated_at" => new Carbon('2020/01/01 00:00:01')
        ]);
        DB::table('contacts')->insert([
            'name' => "Lentes optica center",
            'rfc' => "OCC600101B56",
            'email' => "optica_center@domain.com",
            'type' => 1,
            'business' => 1,
            'telnumbers' => "3345628597,3123130000",
            'birthday' => new Carbon("1960/01/01"),
            'domicilio' => "Av prologancion periferico 6520, Centro, Zapopan",
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:01'),
            "updated_at" => new Carbon('2020/01/01 00:00:01')
        ]);
    }
}
