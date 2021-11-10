<?php

Route::group(['prefix' => '/lib/panic'], function () {
    Route::get('/view/report', 'Codificar\Panic\Http\PanicController@indexSorting');

    Route::get('/view/settings', function () {
        return view('laravel-panic::settings');
    });
});
