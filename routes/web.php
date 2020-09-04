<?php

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

Route::get('/', 'Front\\HomeController@index')->name('front.home');
Route::get('files/{id}/preview', 'Front\\FileController@filePreview')->name('front.file.preview');
Route::get('files/{id}/download', 'Front\\FileController@fileDownload')->name('front.file.download');

Auth::routes();

// NOTE:
// remove the demo middleware before you start on a project, this middleware if only
// for demo purpose to prevent viewers to modify data on a live demo site

// admin
Route::prefix('admin')->namespace('Admin')->middleware(['auth'])->group(function () {
    // single page
    Route::get('/', 'SinglePageController@displaySPA')->name('admin.spa');

    // resource routes
    Route::resource('users', 'UserController');
    Route::resource('groups', 'GroupController');
    Route::resource('permissions', 'PermissionController');
    Route::resource('files', 'FileController');
    Route::resource('file-groups', 'FileGroupController');
    Route::resource('gps', 'GpsController');
    Route::resource('gpsCustomers', 'GpsGroupController');
    Route::resource('chips', 'GpsChipController');

    Route::post('clientes-gps/import', 'GpsImportController@importClientesGps')->name('gps-clientes-import');
    Route::post('gps/import', 'GpsImportController@importGps')->name('gps-import');
    Route::post('chips/import', 'GpsImportController@importChips')->name('chips-import');
    Route::post('matching-chips-gps/import', 'GpsImportController@matchingChipsInGps')->name('matching-chip-gps');
    Route::get('gps-export', 'ExportController@exportGps')->name('gps-export');

    //GPS
    Route::post('gps/renewInvoice/{id}','GpsController@renewInvoice')->name('renewInvoice-gps');
    Route::post('gps/cancelled/{id}','GpsController@cancelled')->name('cancelled-gps');
    Route::post('gps/reasign/{id}','GpsController@reasign')->name('reasign-gps');
    Route::post('gps/statsMonths','GpsController@stats')->name('stats-gps');

    //Marketing
    Route::get('marketing/sales_history','MarketingController@salesHistory')->name('salesHistory');
    Route::get('marketing/sales_history/resources','MarketingController@resources')->name('salesHistoryResources');
    Route::get('marketing/export', 'ExportController@exportMarketing')->name('marketingExport');
});


//Clear Cache facade value:
Route::get('/clear-cache', function() {
    $exitCode = Artisan::call('cache:clear');
    return '<h1>Cache facade value cleared</h1>';
});

//Reoptimized class loader:
Route::get('/optimize', function() {
    $exitCode = Artisan::call('optimize');
    return '<h1>Reoptimized class loader</h1>';
});

//Route cache:
Route::get('/route-cache', function() {
    $exitCode = Artisan::call('route:cache');
    return '<h1>Routes cached</h1>';
});

//Clear Route cache:
Route::get('/route-clear', function() {
    $exitCode = Artisan::call('route:clear');
    return '<h1>Route cache cleared</h1>';
});

//Clear View cache:
Route::get('/view-clear', function() {
    $exitCode = Artisan::call('view:clear');
    return '<h1>View cache cleared</h1>';
});

//Clear Config cache:
Route::get('/config-cache', function() {
    $exitCode = Artisan::call('config:cache');
    return '<h1>Clear Config cleared</h1>';
});