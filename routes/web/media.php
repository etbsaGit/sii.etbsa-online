<?php

Route::prefix('admin')->namespace('Admin')->middleware(['auth'])->group(function () {

    // Route::get('/', 'VoyagerMediaController@index')->name('index');
    Route::post('media/files', 'VoyagerMediaController@files')->name('files');
    Route::get('media/get_file', 'VoyagerMediaController@get_file')->name('get_file');
    Route::post('media/new_folder', 'VoyagerMediaController@new_folder')->name('new_folder');
    Route::post('media/delete_file_folder', 'VoyagerMediaController@delete')->name('delete');
    Route::post('media/move_file', 'VoyagerMediaController@move')->name('move');
    Route::post('media/rename_file', 'VoyagerMediaController@rename')->name('rename');
    Route::post('media/upload', 'VoyagerMediaController@upload')->name('upload');
    Route::post('media/download-files-zip', 'VoyagerMediaController@downloadFilesZip')->name('downloadFilesZip');
    Route::post('media/crop', 'VoyagerMediaController@crop')->name('crop');
});
