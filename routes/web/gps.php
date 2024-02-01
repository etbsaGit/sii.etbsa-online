<?php


Route::prefix('admin')->namespace('Admin')->middleware(['auth'])->group(function () {

    // resource
    Route::resource('gpsCustomers', 'GpsGroupController');
    Route::resource('chips', 'GpsChipController');

    //GPS
    Route::resource('gps', 'GpsController');
    Route::put('gps/{gps}/invoice', 'GpsController@invoice')->name('gps.invoice');
    Route::put('gps/{gps}/cancel', 'GpsController@cancel')->name('gps.cancel');
    Route::post('gps/{gps}/reasign', 'GpsController@reasign')->name('gps.reasign');
    Route::get('gps/stats/allYear', 'GpsController@stats')->name('gps.stats');
    Route::get('gps/search/resources', 'GpsController@resources')->name('gps.resources');

    // Route::get('gps-update','GpsController@update_dates')->name('update-dates');

    // EXPOR/IMPORT
    Route::get('gps-export', 'ExportController@exportGps')->name('gps.export');
    Route::get('gps-chips-export', 'ExportController@exportGpsChips')->name('gps.chips.export');
    Route::get('gps-groups-export', 'ExportController@exportGpsGroups')->name('gps.groups.export');
    // Route::post('gps/import', 'GpsImportController@importGps')->name('gps-import');
    // Route::post('matching-chips-gps/import', 'GpsImportController@matchingChipsInGps')->name('matching-chip-gps');
    // Route::post('chips/import', 'GpsImportController@importChips')->name('chips-import');
    // Route::post('clientes-gps/import', 'GpsImportController@importClientesGps')->name('gps-clientes-import');
});
