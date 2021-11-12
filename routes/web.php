<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
DB::listen(function($query){
    //Imprimimos la consulta ejecutada
    //var_dump($bindings);
    echo "<pre> {$query->sql}{$query->bindings[0]} </pre>";
    //dd($query->sql,$query->bindings);
});
*/

/*
Route::get('/', 'WebController@index');
Route::get('/contactos/{id?}', 'WebController@index');
Route::get('/consultorio/{id?}', 'WebController@index');
Route::get('/pedidos/{id?}', 'WebController@index');
Route::get('/notas/{id?}', 'WebController@index');
Route::get('/almacen/{id?}', 'WebController@index');
Route::get('/usuarios/{id?}', 'WebController@index');
Route::get('/configuraciones', 'WebController@index');
*/

Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
Route::get('/{any}', 'WebController@index')->where('any', '.*');