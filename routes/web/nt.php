<?php


Route::prefix('admin')->namespace('Admin')->middleware(['auth'])->group(function () {

    // resource
    // Route::resource('ams-comparative', 'AmsComparativeController');
    Route::resource('ams-comparative', 'AmsComparativoController');
    Route::resource('ams-equipment', 'AmsEquipmentController');

    Route::post('ams-comparative/preview', 'AmsComparativoController@preview');
    Route::get('ams-equipment/resource', 'AmsEquipmentController@resource');
});
