<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CategorySeeder extends Seeder{

    public function run(){
        DB::table('categories')->insert([
            'name' => 'Lentes',
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'Armazones',
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'Lentes de contacto',
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'Varios',
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'Monofocales',
            'category_id' => 1,
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'Bifocales',
            'category_id' => 1,
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'Progresivos',
            'category_id' => 1,
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'Plastico',
            'category_id' => 5,
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'Policarbonato',
            'category_id' => 5,
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'Hi-index',
            'category_id' => 5,
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'Plastico',
            'category_id' => 6,
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'Policarbonato',
            'category_id' => 6,
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'Plastico',
            'category_id' => 7,
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'Policarbonato',
            'category_id' => 7,
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'Hi-index',
            'category_id' => 7,
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
    }
}
