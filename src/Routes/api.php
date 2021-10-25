<?php
Route::group(['prefix' => '/lib/panic'], function () {
    Route::post('/save', 'Codificar\Panic\Http\PanicController@storePanicRequest');
    Route::post('/delete', 'Codificar\Panic\Http\PanicController@deletePanicRequest');

    Route::get('settings/', 'Codificar\Panic\Http\PanicController@getPanicButtonSettings');
    Route::post('settings/save', 'Codificar\Panic\Http\PanicController@savePanicButtonSettings');

    Route::get('settings/segup', 'Codificar\Panic\Http\PanicController@getPanicSegupSettings');
    Route::post('settings/save/segup', 'Codificar\Panic\Http\PanicController@savePanicSegupSettings');
});
