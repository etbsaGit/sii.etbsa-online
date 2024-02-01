<?php


Route::prefix('admin')->namespace('Admin')->middleware(['auth'])->group(function () {

    // Estates and Township
    Route::get('estates', 'ResourcesShareController@getEstates')->name('get-estates');
    Route::get('townships/{id}', 'ResourcesShareController@getTownship')->name('get-townships');
});