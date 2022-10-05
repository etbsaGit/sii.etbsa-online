<?php


Route::prefix('admin')->namespace('Admin')->middleware(['auth'])->group(function () {

    // resource
    Route::resource('ams-comparative', 'AmsComparativeController');
    Route::resource('ams-equipment', 'AmsEquipmentController');

    Route::post('ams-comparative/preview', 'AmsComparativeController@preview');
    Route::get('ams-equipment/resource', 'AmsEquipmentController@resource');
});
