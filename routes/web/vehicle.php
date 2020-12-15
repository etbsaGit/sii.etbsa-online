<?php

Route::prefix('admin')->namespace('Admin')->middleware(['auth'])->group(function () {

    // resource
    Route::resource('vehicles', 'VehicleController');
    Route::resource('vehicle-dispersal', 'VehicleDispersalController');
    Route::resource('vehicle-services', 'VehicleServiceController');


    Route::post('vehicle-dispersal/{id}/estatus', 'VehicleDispersalController@changeEstatus')->name('dispersal.change.estatus');
    Route::post('vehicle-services/{id}/estatus', 'VehicleServiceController@changeEstatus')->name('dispersal.change.estatus');
});
