<?php
Route::group(['prefix' => '/lib/panic'], function () {
    Route::post('/save', 'Codificar\Panic\Http\Controllers\PanicController@storePanicRequest');
    Route::post('/delete', 'Codificar\Panic\Http\Controllers\PanicController@deletePanicRequest');

    Route::get('settings/', 'Codificar\Panic\Http\Controllers\PanicController@getPanicButtonSettings');
    Route::post('settings/save', 'Codificar\Panic\Http\Controllers\PanicController@savePanicButtonSettings');

    Route::get('settings/segup', 'Codificar\Panic\Http\Controllers\PanicController@getPanicSegupSettings');
    Route::post('settings/save/segup', 'Codificar\Panic\Http\Controllers\PanicController@savePanicSegupSettings');

    Route::post("settings/save/admin", 'Codificar\Panic\Http\Controllers\PanicController@savePanicAdminSettings');
    Route::get('settings/admin', 'Codificar\Panic\Http\Controllers\PanicController@getPanicAdminSettings');

	Route::get('/admins', 'Codificar\Panic\Http\Controllers\PanicController@getAdminsToSettingsPage');

    //create route to save the admin data that is needed
    //create request validating the needed data 
    //create the needed data in the settings model
    //create a function into the repository then create a function into the controller, call it then and save it into the settings table
    //create resource to return the data after saving it
    //create route to get the settings data
    //create resource to return it

});
