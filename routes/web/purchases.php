<?php

use App\Http\Controllers\Admin\SupplierProductController;

Route::prefix('admin')->namespace('Admin')->middleware(['auth'])->group(function () {

    //PURCHASES_ORDERS
    Route::resource('purchase-order', 'PurchaseOrderController');
    Route::get('purchase-order/resources/options', 'PurchaseOrderController@resources')->name('purchase-order.options');
    Route::get('purchase-order/{purchaseOrder}/resources/print', 'PurchaseOrderController@print')->name('purchase-order.print');
    Route::post('purchase-order/update-estatus/{purchase_order}', 'PurchaseOrderController@updateEstatus')->name('purchase-order.updateEstatus');
    //    Purchase Invoice
    Route::post('purchase-order/valid-invoice/{purchase_order}', 'PurchaseInvoiceController@validInvoicePurchaseOrder')->name('purchase-order.validInvoicePurchaseOrder');
    Route::post('purchase-order/store-invoice/{purchase_order}', 'PurchaseInvoiceController@storeInvoicePurchase')->name('purchase-order.validInvoicePurchaseOrder');

    //PURCHASE_ORDERS_DOCUMENT
    Route::get('purchase-order/{purchaseOrder}/document', 'PurchaseOrderDocumentController@index')->name('purchase-order-document.index');
    Route::post('purchase-order/{purchaseOrder}/document', 'PurchaseOrderDocumentController@store')->name('purchase-order-document.store');

    // PURCHASE_ORDERS_MESSAGES
    Route::get('purchase-order/{purchaseOrder}/message', 'PurchaseOrderMessageController@index')->name('purchase-order-message.index');
    Route::post('purchase-order/{purchaseOrder}/message', 'PurchaseOrderMessageController@store')->name('purchase-order-message.store');

    // SUPPLIER
    Route::resource('suppliers', 'SupplierController');
    Route::get('supplier-export', 'ExportController@exportSupplier')->name('supplierExport');
    // SUPPLIER-PRODUCTS
    Route::resource('supplier-products', 'SupplierProductController');
    // Route::get('supplier-export', 'ExportController@exportSupplier')->name('supplierExport');

    // PURCHASE INVOICE
    Route::get('purchase-invoice', 'PurchaseInvoiceController@index')->name('purchase-invoice.index');
    Route::post('purchase-invoice/date_to_payment/{invoice}', 'PurchaseInvoiceController@updateDateToPayment')->name('purchase-invoice.updateDateToPayment');
    Route::post('purchase-invoice/date_payment/{invoice}', 'PurchaseInvoiceController@updateDatePayment')->name('purchase-invoice.updateDatePayment');
    Route::get('purchase-invoice-export', 'ExportController@exportInvoice')->name('purchase-invoice.export');

    Route::post('purchase-invoice/reset_date_to_payment/{invoice}', 'PurchaseInvoiceController@resetDateToPayment')->name('purchase-invoice.resetDateToPayment');
    Route::post('purchase-invoice/reset_date_payment/{invoice}', 'PurchaseInvoiceController@resetDatePayment')->name('purchase-invoice.resetDatePayment');


    // PURCHASE CONCEPTS
    Route::resource('purchase-concept', 'PurchaseConceptController');
    Route::put('purchase-concept/{purchaseConcept}/update-uso-cfdi', 'PurchaseConceptController@updateUsoCfdi')->name('purchase-concept.updateUsoCfdi');
    // PURCHASE CONCEPT PRODUCT
    Route::resource('purchase-concept-products', 'PurchaseConceptProductController');
    // EXPORT
    Route::get('purchase-order/export', 'ExportController@exportPurchaseInvoice')->name('purchase.export');
    Route::get('purchase-invoice/export-report', 'PurchaseInvoiceController@printReport')->name('purchase.export-report');
});
