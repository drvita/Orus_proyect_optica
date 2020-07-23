<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CategorySeeder extends Seeder{

    public function run(){
        DB::table('categories')->insert([
            'name' => 'Lentes',
            'descripcion' => "Categoria principal para lentes",
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'Armazones',
            'descripcion' => "Categoria principal para armazones",
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'Lentes de contacto',
            'descripcion' => "Categoria principal para lentes de contacto",
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'Varios',
            'descripcion' => "Categoria para otros productos no relacionados",
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'Monofocales',
            'descripcion' => "Categoria para lentes monofocales",
            'category_id' => 1,
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'Bifocales',
            'descripcion' => "Categoria para lentes bifocales",
            'category_id' => 1,
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
        DB::table('categories')->insert([
            'name' => 'Progresivos',
            'descripcion' => "Categoria para lentes progresivos",
            'category_id' => 1,
            'user_id' => 1,
            "created_at" => new Carbon('2020/01/01 00:00:00'),
            "updated_at" => new Carbon('2020/01/01 00:00:00')
        ]);
    }
}
