<?php

Route::group(['prefix' => config('backupmanager.route-prefix'), 'middleware' => config('backupmanager.middleware')], function () {

    // Backup Routes
    Route::get('backup', ['as' => 'backup.index', 'uses' => 'BackupController@index']);
    Route::post('backup/create', ['as' => 'backup.create', 'uses' => 'BackupController@create']);
    Route::get('backup/download', ['as' => 'backup.download', 'uses' => 'BackupController@download']);
    Route::delete('backup/delete', ['as' => 'backup.delete', 'uses' => 'BackupController@delete']);

});
