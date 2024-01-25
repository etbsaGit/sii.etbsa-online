<?php

Route::prefix('admin')->namespace('Admin')->middleware(['auth'])->group(function () {

    // resource
    Route::resource('agency-bank-accounts', 'AgencyBankAccountController');
    Route::resource('bank-policy', 'BankPolicyController');

});