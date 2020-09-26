<?php


Route::prefix('admin')->namespace('Admin')->middleware(['auth'])->group(function () {

    // resource
    Route::resource('gps', 'GpsController');
    Route::resource('gpsCustomers', 'GpsGroupController');
    Route::resource('chips', 'GpsChipController');

    
    // EXPOR/IMPORT
    Route::get('gps-export', 'ExportController@exportGps')->name('gps-export');
    Route::post('gps/import', 'GpsImportController@importGps')->name('gps-import');
    Route::post('matching-chips-gps/import', 'GpsImportController@matchingChipsInGps')->name('matching-chip-gps');
    Route::post('chips/import', 'GpsImportController@importChips')->name('chips-import');
    Route::post('clientes-gps/import', 'GpsImportController@importClientesGps')->name('gps-clientes-import');

    //GPS
    Route::post('gps/renewInvoice/{id}','GpsController@renewInvoice')->name('renewInvoice-gps');
    Route::post('gps/cancelled/{id}','GpsController@cancelled')->name('cancelled-gps');
    Route::post('gps/reasign/{id}','GpsController@reasign')->name('reasign-gps');
    Route::post('gps/statsMonths','GpsController@stats')->name('stats-gps');
    // Route::get('gps-update','GpsController@update_dates')->name('update-dates');

});