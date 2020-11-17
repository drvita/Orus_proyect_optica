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
*/

Route::get('/', 'WebController@index');
Route::get('/contactos', 'WebController@index');
Route::get('/contactos/registro/{id?}', 'WebController@index');
Route::get('/consultorio', 'WebController@index');
Route::get('/consultorio/registro/{id?}', 'WebController@index');
Route::get('/pedidos', 'WebController@index');
Route::get('/pedidos/registro/{id?}', 'WebController@index');
Route::get('/notas', 'WebController@index');
Route::get('/notas/registro/{id?}', 'WebController@index');
Route::get('/almacen', 'WebController@index');
Route::get('/almacen/registro/{id?}', 'WebController@index');
Route::get('/usuarios', 'WebController@index');
Route::get('/usuarios/registro/{id?}', 'WebController@index');
Route::get('/configuraciones', 'WebController@index');
