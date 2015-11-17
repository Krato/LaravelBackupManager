<?php

Route::group(['prefix' => 'admin', 'middleware' => 'auth'], function()
{

	// Backup
	Route::get('backup', 'BackupController@index');
	Route::put('backup/create', 'BackupController@create');
	Route::get('backup/download/{file_name}', 'BackupController@download');
	Route::delete('backup/delete/{file_name}', 'BackupController@delete');

});