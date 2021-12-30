<?php

Route::prefix('admin')->namespace('Admin')->middleware(['auth'])->group(function () {

    Route::resource('tracking', 'TrackingProspectController');
    Route::get('tracking/sales_history/resources', 'TrackingProspectController@resources')->name('trackingResources');
    Route::put('tracking/historical/{id}', 'TrackingProspectController@addHistoricalTracking')->name('tracking.addHistoricalTracking');
    Route::put('tracking/resetToActive/{id}', 'TrackingProspectController@resetToActive')->name('tracking.resetToActive');
    Route::get('tracking/historical/diary', 'TrackingProspectController@diaryTrackings')->name('tracking.diary');
    Route::get('tracking-export', 'ExportController@exportTracking')->name('trackingExport');
    // Route::put('tracking/assignSeller/{id}', 'TrackingProspectController@assignSeller')->name('trackingAssignSeller');
    // Route::post('tracking/message', 'MessageTrackingController@store')->name('message.tracking.store');
    // Route::get('tracking/messages/{tracking}', 'MessageTrackingController@getMessagesTracking')->name('message.tracking.getMessagesTracking');

    // TRACKING_HISTORICAL
    Route::post('tracking/{tracking}/historical', 'TrackingProspectHistoricalController@store')->name('tracking.historical.store');

    // SALE_ORDERS
    Route::resource('sale-order', 'SaleOrderController');

    // TRACKING_MESSAGES
    Route::get('tracking/{tracking}/message', 'TrackingProspectMessageController@index')->name('tracking-message.index');
    Route::post('tracking/{tracking}/message', 'TrackingProspectMessageController@store')->name('tracking-message.store');
});
