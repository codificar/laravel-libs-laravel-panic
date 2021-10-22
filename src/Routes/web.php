<?php



Route::group(['prefix' => '/lib/panic'], function () {
    Route::get('/', function () {
        return 'contact';
    });
});
