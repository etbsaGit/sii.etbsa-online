<?php

Route::prefix('admin')->namespace('Admin')->middleware(['auth'])->group(function () {

    // resource
    Route::resource('recruitment', 'EmployeeRecruitmentController');
    Route::resource('employees', 'EmployeeController');
    Route::post('employees/{employee}/user/{user}', 'EmployeeController@assignedUser')->name('employee.assigned-user');

    Route::get('recruitment-options', 'EmployeeRecruitmentController@options')->name('recruitment.options');

    Route::get('employees/resources/options', 'EmployeeController@options')->name('employee.options');
});
