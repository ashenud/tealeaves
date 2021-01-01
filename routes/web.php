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
Route::get('/admin/suppliers', 'App\Http\Controllers\Admin\SupplierController@suppliers')->name('suppliers')->middleware('admin');
Route::get('/admin/suppliers-datatable', 'App\Http\Controllers\Admin\SupplierController@supplierDatatable')->name('suppliers--datatable')->middleware('admin');
Route::get('/admin/supplier-get-data', 'App\Http\Controllers\Admin\SupplierController@supplierGetData')->name('supplier-get-data')->middleware('admin');
Route::post('/admin/supplier-insert', 'App\Http\Controllers\Admin\SupplierController@supplierInsert')->middleware('admin');
Route::post('/admin/supplier-edit', 'App\Http\Controllers\Admin\SupplierController@supplierEdit')->middleware('admin');
Route::post('/admin/supplier-delete', 'App\Http\Controllers\Admin\SupplierController@supplierDelete')->middleware('admin');


Route::get('/admin/items', 'App\Http\Controllers\Admin\ItemController@items')->name('items')->middleware('admin');
Route::get('/admin/item-get-data', 'App\Http\Controllers\Admin\ItemController@itemGetData')->name('item-get-data')->middleware('admin');
Route::get('/admin/items-datatable', 'App\Http\Controllers\Admin\ItemController@itemsDatatable')->name('items--datatable')->middleware('admin');
Route::post('/admin/item-insert', 'App\Http\Controllers\Admin\ItemController@itemInsert')->middleware('admin');
Route::post('/admin/item-edit', 'App\Http\Controllers\Admin\ItemController@itemEdit')->middleware('admin');
Route::post('/admin/item-delete', 'App\Http\Controllers\Admin\ItemController@itemDelete')->middleware('admin');