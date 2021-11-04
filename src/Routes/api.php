<?php
Route::group(['prefix' => '/lib/panic'], function () {
    Route::post('/save', 'Codificar\Panic\Http\PanicController@storePanicRequest');
    Route::post('/delete', 'Codificar\Panic\Http\PanicController@deletePanicRequest');

    Route::get('settings/', 'Codificar\Panic\Http\PanicController@getPanicButtonSettings');
    Route::post('settings/save', 'Codificar\Panic\Http\PanicController@savePanicButtonSettings');

    Route::get('settings/segup', 'Codificar\Panic\Http\PanicController@getPanicSegupSettings');
    Route::post('settings/save/segup', 'Codificar\Panic\Http\PanicController@savePanicSegupSettings');

    Route::post("settings/save/admin", 'Codificar\Panic\Http\PanicController@savePanicAdminSettings');
    Route::get('settings/admin', 'Codificar\Panic\Http\PanicController@getPanicAdminSettings');

    Route::get('/', 'PanicController@index');

    //create route to save the admin data that is needed
    //create request validating the needed data 
    //create the needed data in the settings model
    //create a function into the repository then create a function into the controller, call it then and save it into the settings table
    //create resource to return the data after saving it
    //create route to get the settings data
    //create resource to return it

});
