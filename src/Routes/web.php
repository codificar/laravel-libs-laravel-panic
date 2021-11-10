<?php

Route::group(['prefix' => '/lib/panic'], function () {
    Route::get('/view/report', function () {
        return view('laravel-panic::report');
    });

    Route::get('/view/settings', function () {
        return view('laravel-panic::settings');
    });
});
