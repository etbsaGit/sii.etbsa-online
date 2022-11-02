<?php


Route::prefix('admin')->namespace('Admin')->middleware(['auth'])->group(function () {

    // resource
    Route::resource('products/category', 'ProductCategoryController');
    Route::resource('products/model', 'ProductModelController');
    Route::resource('products', 'ProductController');
});
