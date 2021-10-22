<?php
Route::group(['prefix' => '/lib/panic'], function () {
    Route::post('/save', 'Codificar\Panic\Http\PanicController@storePanicRequest');
    Route::post('/delete', 'Codificar\Panic\Http\PanicController@deletePanicRequest');
    Route::post('settings/save', 'Codificar\Panic\Http\PanicController@savePanicSettings');
    Route::get('settings/', 'Codificar\Panic\Http\PanicController@getPanicSettings');
});
