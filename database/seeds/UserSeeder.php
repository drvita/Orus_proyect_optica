<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserSeeder extends Seeder{

    public function run(){
        DB::table('users')->insert([
            "name" => "Salvador Galindo",
            "username" => "Admin",
            "email" => "admin@domain.com",
            "rol" => 2,
            "password" => Hash::make("123123"),
            "api_token" => "8150b31694f597622bdc979cc7d35989b24f343e0d96ff77b5d3dcbb8717373e",
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('contacts')->insert([
            'name' => "Diana Martinez Glez",
            'rfc' => "MAGV191185MP3",
            'email' => "veritoo@domain.com",
            'type' => 1,
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
            'type' => 1,
            'telnumbers' => "3123178569",
            'birthday' => new Carbon("2009/05/16"),
            'domicilio' => "Volcan chicon 144, Vista Volcanes",
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
    }
}
