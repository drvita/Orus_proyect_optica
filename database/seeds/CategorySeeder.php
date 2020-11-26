<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CategorySeeder extends Seeder{

    public function run(){
        DB::table('categories')->insert([
            'name' => 'lentes',
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'armazones',
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'lentes de contacto',
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'varios',
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'monofocales',
            'category_id' => 1,
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'bifocales',
            'category_id' => 1,
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'progresivo basico',
            'category_id' => 1,
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'progresivo digital',
            'category_id' => 1,
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'plastico',
            'category_id' => 5,
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'policarbonato',
            'category_id' => 5,
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'hi-index',
            'category_id' => 5,
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'plastico',
            'category_id' => 6,
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'policarbonato',
            'category_id' => 6,
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'plastico',
            'category_id' => 7,
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'policarbonato',
            'category_id' => 7,
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'plastico',
            'category_id' => 8,
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'policarbonato',
            'category_id' => 8,
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'hi-index',
            'category_id' => 8,
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'antirreflejantes',
            'category_id' => 9,
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'photo',
            'category_id' => 9,
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'ar & photo',
            'category_id' => 9,
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'blanco',
            'category_id' => 9,
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'antirreflejantes',
            'category_id' => 10,
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'photo',
            'category_id' => 10,
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'ar & photo',
            'category_id' => 10,
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'blanco',
            'category_id' => 10,
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'antirreflejantes',
            'category_id' => 11,
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'photo',
            'category_id' => 11,
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'ar & photo',
            'category_id' => 11,
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'blanco',
            'category_id' => 11,
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'photo',
            'category_id' => 12,
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'blanco',
            'category_id' => 12,
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'blanco',
            'category_id' => 13,
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'antirreflejantes',
            'category_id' => 14,
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'photo',
            'category_id' => 14,
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'ar & photo',
            'category_id' => 14,
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'blanco',
            'category_id' => 14,
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'antirreflejantes',
            'category_id' => 15,
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'photo',
            'category_id' => 15,
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'ar & photo',
            'category_id' => 15,
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'blanco',
            'category_id' => 15,
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'antirreflejantes',
            'category_id' => 16,
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'photo',
            'category_id' => 16,
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'ar & photo',
            'category_id' => 16,
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'blanco',
            'category_id' => 16,
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'antirreflejantes',
            'category_id' => 17,
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'photo',
            'category_id' => 17,
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'ar & photo',
            'category_id' => 17,
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'blanco',
            'category_id' => 17,
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'antirreflejantes',
            'category_id' => 18,
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'photo',
            'category_id' => 18,
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'ar & photo',
            'category_id' => 18,
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'blanco',
            'category_id' => 18,
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
    }
}
