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
Route::get('/admin/suppliers-datatable', 'App\Http\Controllers\Admin\SupplierController@supplierDatatable')->name('suppliers-datatable')->middleware('admin');
Route::get('/admin/supplier-get-data', 'App\Http\Controllers\Admin\SupplierController@supplierGetData')->name('supplier-get-data')->middleware('admin');
Route::post('/admin/supplier-insert', 'App\Http\Controllers\Admin\SupplierController@supplierInsert')->middleware('admin');
Route::post('/admin/supplier-edit', 'App\Http\Controllers\Admin\SupplierController@supplierEdit')->middleware('admin');
Route::post('/admin/supplier-delete', 'App\Http\Controllers\Admin\SupplierController@supplierDelete')->middleware('admin');


Route::get('/admin/items', 'App\Http\Controllers\Admin\ItemController@items')->name('items')->middleware('admin');
Route::get('/admin/item-get-data', 'App\Http\Controllers\Admin\ItemController@itemGetData')->name('item-get-data')->middleware('admin');
Route::get('/admin/items-datatable', 'App\Http\Controllers\Admin\ItemController@itemsDatatable')->name('items-datatable')->middleware('admin');
Route::post('/admin/item-insert', 'App\Http\Controllers\Admin\ItemController@itemInsert')->middleware('admin');
Route::post('/admin/item-edit', 'App\Http\Controllers\Admin\ItemController@itemEdit')->middleware('admin');
Route::post('/admin/item-delete', 'App\Http\Controllers\Admin\ItemController@itemDelete')->middleware('admin');

Route::get('/admin/daily-collect', 'App\Http\Controllers\Admin\DailyCollectController@index')->name('daily-collect')->middleware('admin');
Route::get('/admin/load-insert-collection/{day}', 'App\Http\Controllers\Admin\DailyCollectController@loadInsertCollection')->name('load-insert-collection')->middleware('admin'); // insert and view collection
Route::get('/admin/load-edit-collection/{id}', 'App\Http\Controllers\Admin\DailyCollectController@loadEditCollection')->name('load-edit-collection')->middleware('admin'); // edit collection
Route::post('/admin/insert-collection', 'App\Http\Controllers\Admin\DailyCollectController@insertCollection')->middleware('admin'); // insert collection action
Route::post('/admin/edit-collection', 'App\Http\Controllers\Admin\DailyCollectController@editCollection')->middleware('admin'); // edit collection action
Route::post('/admin/confirm-collection', 'App\Http\Controllers\Admin\DailyCollectController@confirmCollection')->middleware('admin'); // confirm collection action

Route::get('/admin/daily-issue', 'App\Http\Controllers\Admin\DailyIssueController@index')->name('daily-issue')->middleware('admin');
Route::get('/admin/load-insert-issues/{day}', 'App\Http\Controllers\Admin\DailyIssueController@loadInsertIssues')->name('load-insert-issues')->middleware('admin'); // insert and view issues
Route::get('/admin/load-edit-issues/{id}', 'App\Http\Controllers\Admin\DailyIssueController@loadEditIssues')->name('load-edit-issues')->middleware('admin'); // edit issues
Route::post('/admin/insert-issues', 'App\Http\Controllers\Admin\DailyIssueController@insertIssues')->middleware('admin'); // insert issues action
Route::post('/admin/edit-issues', 'App\Http\Controllers\Admin\DailyIssueController@editIssues')->middleware('admin'); // edit issues action
Route::post('/admin/confirm-issues', 'App\Http\Controllers\Admin\DailyIssueController@confirmIssues')->middleware('admin'); // confirm issues action

Route::get('/admin/fertilizer-issue', 'App\Http\Controllers\Admin\FertilizerIssueController@index')->name('fertilizer-issue')->middleware('admin');
Route::get('/admin/load-insert-fertilizer-issues/{day}', 'App\Http\Controllers\Admin\FertilizerIssueController@loadInsertFertilizerIssues')->name('load-insert-fertilizer-issues')->middleware('admin'); // insert and view fertilizer
Route::get('/admin/load-edit-fertilizer-issues/{id}', 'App\Http\Controllers\Admin\FertilizerIssueController@loadEditFertilizerIssues')->name('load-edit-fertilizer-issues')->middleware('admin'); // edit fertilizer
Route::post('/admin/insert-fertilizer-issues', 'App\Http\Controllers\Admin\FertilizerIssueController@insertFertilizerIssues')->middleware('admin'); // insert fertilizer action
Route::post('/admin/edit-fertilizer-issues', 'App\Http\Controllers\Admin\FertilizerIssueController@editFertilizerIssues')->middleware('admin'); // edit fertilizer action
Route::post('/admin/confirm-fertilizer-issues', 'App\Http\Controllers\Admin\FertilizerIssueController@confirmFertilizerIssues')->middleware('admin'); // confirm fertilizer action

Route::get('/admin/advance-issue', 'App\Http\Controllers\Admin\AdvanceIssueController@index')->name('advance-issue')->middleware('admin');
Route::post('/admin/insert-advance', 'App\Http\Controllers\Admin\AdvanceIssueController@insertAdvance')->middleware('admin'); // insert advance action
Route::get('/admin/advance-datatable', 'App\Http\Controllers\Admin\AdvanceIssueController@advanceDatatable')->name('advance-datatable')->middleware('admin');

Route::get('/admin/loan-issue', 'App\Http\Controllers\Admin\LoanIssueController@index')->name('loan-issue')->middleware('admin');
Route::post('/admin/insert-loan', 'App\Http\Controllers\Admin\LoanIssueController@insertLoan')->middleware('admin'); // insert loan action
Route::get('/admin/loan-datatable', 'App\Http\Controllers\Admin\LoanIssueController@loanDatatable')->name('loan-datatable')->middleware('admin');

Route::get('/admin/month-end', 'App\Http\Controllers\Admin\MonthEndController@index')->name('month-end')->middleware('admin');
Route::get('/admin/month-end-datatable', 'App\Http\Controllers\Admin\MonthEndController@monthEndDatatable')->name('month-end-datatable')->middleware('admin');
Route::post('/admin/create-month-end', 'App\Http\Controllers\Admin\MonthEndController@createMonthEnd')->middleware('admin');
Route::post('/admin/print-bulk-bills', 'App\Http\Controllers\Admin\MonthEndController@printBulkBills')->middleware('admin');