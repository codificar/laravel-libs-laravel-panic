<?php



Route::group(['prefix' => '/lib/panic'], function () {
    Route::get('/view/report', 'report');
    Route::get('/view/settings', 'settings');
});
