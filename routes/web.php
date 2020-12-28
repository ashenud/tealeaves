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

Route::get('/', function () {
    return view('welcome');
});

Route::post('/authentication', 'App\Http\Controllers\LoginController@login');
Route::post('/logout', 'App\Http\Controllers\LoginController@logout');

/* @@ admin controllers @@ */
Route::get('/admin/dashboard', 'App\Http\Controllers\Admin\AdminController@index')->name('admin')->middleware('admin');
Route::get('/admin/supplier-insert', 'App\Http\Controllers\Admin\SupplierController@supplierInsert')->name('supplier-add')->middleware('admin');
Route::post('/admin/supplier-insert-action', 'App\Http\Controllers\Admin\SupplierController@supplierInsertAction')->middleware('admin');