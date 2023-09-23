<?php

Route::prefix('admin')->namespace('Admin')->middleware(['auth'])->group(function () {

    // resource
    Route::resource('customers', 'CustomersController');
    Route::post('customer/{customer}/updateMeta', 'CustomersController@updateMeta')->name('customer.updateMeta');

    Route::get('customers/reports/portfolio', 'CustomerPortfolioHistoryController@index')->name('customers.reports.portfolio');

    Route::get('customers/reports/resources', 'CustomerPortfolioHistoryController@getOptions')->name('customers.reports.resources');
});