<?php

Route::prefix('admin')->namespace('Admin')->middleware(['auth'])->group(function () {

    // resource
    Route::resource('vehicles', 'VehicleController');
    Route::resource('vehicles-ticket-card', 'VehicleTicketCardController');
    Route::resource('vehicles-fuels', 'VehicleFuelController');

    Route::get('vehicles/resources/options', 'VehicleController@options')->name('vehicles.options');
    // Route::put('vehicles/{vehicle}/options', 'VehicleController@options')->name('vehicles.options');
    Route::resource('vehicle-dispersal', 'VehicleDispersalController');
    Route::resource('vehicle-services', 'VehicleServiceController');


    Route::post('vehicle-ticket-card/reset', 'VehicleTicketCardController@resetBalance')->name('vehcile.ticker-cards.reset');
    Route::post('vehicle-dispersal/{id}/estatus', 'VehicleDispersalController@changeEstatus')->name('dispersal.change.estatus');
    Route::post('vehicle-services/{id}/estatus', 'VehicleServiceController@changeEstatus')->name('dispersal.change.estatus');
});
