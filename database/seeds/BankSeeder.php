<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('config')->insert([
            'name' => 'bank',
            'value' => 'banamex',
        ]);
        DB::table('config')->insert([
            'name' => 'bank',
            'value' => 'santander',
        ]);
        DB::table('config')->insert([
            'name' => 'bank',
            'value' => 'scotiabank',
        ]);
        DB::table('config')->insert([
            'name' => 'bank',
            'value' => 'azteca',
        ]);
        DB::table('config')->insert([
            'name' => 'bank',
            'value' => 'hsbc',
        ]);
        DB::table('config')->insert([
            'name' => 'bank',
            'value' => 'bbva',
        ]);
        DB::table('config')->insert([
            'name' => 'bank',
            'value' => 'banorte',
        ]);
        DB::table('config')->insert([
            'name' => 'bank',
            'value' => 'inbursa',
        ]);
        DB::table('config')->insert([
            'name' => 'bank',
            'value' => 'del bajio',
        ]);
        DB::table('config')->insert([
            'name' => 'bank',
            'value' => 'afirme',
        ]);
        DB::table('config')->insert([
            'name' => 'bank',
            'value' => 'bancoppel',
        ]);
    }
}
