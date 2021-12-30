<?php

Route::prefix('admin')->namespace('Admin')->middleware(['auth'])->group(function () {

    // resource
    Route::resource('customers', 'CustomersController');
    Route::post('customer/{customer}/updateMeta', 'CustomersController@updateMeta')->name('customer.updateMeta');
});
