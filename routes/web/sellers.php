<?php


Route::prefix('admin')->namespace('Admin')->middleware(['auth'])->group(function () {
    Route::resource('sellers', 'SellerController')->only('index','show','update');


    Route::put('/sellers/{seller}/updateSellerType', 'SellerController@updateSellerType');
    Route::put('/sellers/{seller}/updateSellerAgency', 'SellerController@updateSellerAgency');
    Route::put('/sellers/{seller}/updateSellerCategory', 'SellerController@updateSellerCategory');
    Route::delete('/sellers/{seller}/deleteSellerPhoto', 'SellerController@deleteSellerPhoto');

});