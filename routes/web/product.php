<?php


Route::prefix('admin')->namespace('Admin')->middleware(['auth'])->group(function () {

    // resource
    Route::resource('products/category', 'ProductCategoryController');
    Route::resource('products/model', 'ProductModelController');
    Route::resource('products/suppliers', 'ProductSuppliersController');
    Route::resource('products/brands', 'ProductBrandsController');
    Route::resource('products/exchanges', 'ExchangeRatesController');
    Route::resource('products', 'ProductController');

    Route::get('product-export', 'ExportController@exportProducts')->name('product.export');
});
