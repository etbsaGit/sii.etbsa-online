<?php


Route::prefix('admin')->namespace('Admin')->middleware(['auth'])->group(function () {

    //PURCHASES_ORDERS
    Route::resource('purchase-order', 'PurchaseOrderController');
    Route::get('purchase-order/resources/options', 'PurchaseOrderController@resources')->name('purchase-order.resources');
    Route::get('purchase-order/{purchaseOrder}/resources/print', 'PurchaseOrderController@print')->name('purchase-order.print');

    //PURCHASE_ORDERS_DOCUMENT
    Route::get('purchase-order/{purchaseOrder}/document', 'PurchaseOrderDocumentController@index')->name('purchase-order-document.index');
    Route::post('purchase-order/{purchaseOrder}/document', 'PurchaseOrderDocumentController@store')->name('purchase-order-document.store');

    // PURCHASE_ORDERS_MESSAGES
    Route::get('purchase-order/{purchaseOrder}/message', 'PurchaseOrderMessageController@index')->name('purchase-order-message.index');
    Route::post('purchase-order/{purchaseOrder}/message', 'PurchaseOrderMessageController@store')->name('purchase-order-message.store');

    // SUPPLIER
    Route::resource('suppliers', 'SupplierController');
});
