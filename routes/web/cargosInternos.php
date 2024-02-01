<?php

Route::prefix('admin')->namespace('Admin')->middleware(['auth'])->group(function () {

    // resource
    Route::resource('cargos-internos', 'CargosInternosController');

    Route::post('cargos-internos/update-status', 'CargosInternosController@updateStatus')->name('cargos_internos.updateStatus');
});