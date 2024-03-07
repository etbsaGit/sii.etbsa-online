<?php


Route::prefix('admin')->namespace('Admin')->middleware(['auth'])->group(function () {

    Route::resource('tracking', 'TrackingProspectController');
    Route::get('tracking/sales_history/resources', 'TrackingProspectController@resources')->name('trackingResources');
    Route::put('tracking/historical/{id}', 'TrackingProspectController@addHistoricalTracking')->name('tracking.addHistoricalTracking');
    Route::put('tracking/resetToActive/{id}', 'TrackingProspectController@resetToActive')->name('tracking.resetToActive');
    Route::put('tracking/associateCustomer/{id}', 'TrackingProspectController@associateCustomer')->name('tracking.associateCustomer');
    Route::get('tracking/historical/diary', 'TrackingProspectController@diaryTrackings')->name('tracking.diary');
    Route::get('tracking-export', 'ExportController@exportTracking')->name('trackingExport');
    Route::get('tracking/{trackingProspect}/resources/print', 'TrackingProspectController@print')->name('trackingProspect.print');
    // Route::put('tracking/assignSeller/{id}', 'TrackingProspectController@assignSeller')->name('trackingAssignSeller');
    // Route::post('tracking/message', 'MessageTrackingController@store')->name('message.tracking.store');
    // Route::get('tracking/messages/{tracking}', 'MessageTrackingController@getMessagesTracking')->name('message.tracking.getMessagesTracking');


    Route::get('tracking/dashboard/stats', 'TrackingDashboardController@dashboard')->name('tracking.dashboard');

    // TRACKING_HISTORICAL
    Route::post('tracking/{tracking}/historical', 'TrackingProspectHistoricalController@store')->name('tracking.historical.store');

    // SALE_ORDERS
    Route::resource('sale-order', 'SaleOrderController');

    // TRACKING_MESSAGES
    Route::get('tracking/{tracking}/message', 'TrackingProspectMessageController@index')->name('tracking-message.index');
    Route::post('tracking/{tracking}/message', 'TrackingProspectMessageController@store')->name('tracking-message.store');

    Route::get('tracking/{tracking}/notes', 'TrackingProspectNotesController@index')->name('tracking-notes.index');
    Route::post('tracking/{tracking}/notes', 'TrackingProspectNotesController@store')->name('tracking-notes.store');
    Route::delete('notes/{note}', 'TrackingProspectNotesController@destroy')->name('tracking-notes.destroy');

    // TRACKING QUOTES
    Route::resource('quotes', 'TrackingQuoteController');
    Route::get('quote/options', 'TrackingQuoteController@getOptions')->name('tracking-quote.getOptions');
    Route::get('quote/products_by_category/{product_category}', 'TrackingQuoteController@getProductsByCategory')->name('tracking-quote.getProductsByCategory');
    Route::get('quote/{quote}/print', 'TrackingQuoteController@printQuote')->name('tracking-quote.print');

    // FILES
    Route::delete('tracking-file/{file}', 'TrackingProspectController@destroyFile')->name('tracking-file.destroy');
    Route::post('tracking-file/attach/{tracking}', 'TrackingProspectController@attachFiles')->name('tracking-file.attach');



});
