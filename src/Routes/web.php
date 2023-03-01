<?php

Route::group(['prefix' => '/lib/panic'], function () {
    Route::get('/view/report', 'Codificar\Panic\Http\Controllers\PanicController@indexSorting')->name('libPanicReport');
	Route::get('/view/fetch', 'Codificar\Panic\Http\Controllers\PanicController@fetch');

    Route::get('/view/settings', function () {
        return view('laravel-panic::settings');
    });
});

Route::group(['prefix' => '/admin/lib/panic'], function() {
    Route::get('/notification', 'Codificar\Panic\Http\Controllers\PanicController@getPanicMessagesNotification')->name('libAdminPanicMessagesNotifications');
    Route::get('/see/{panicId}', 'Codificar\Panic\Http\Controllers\PanicController@adminPanicSee')->name('libPanicSee');
});

/**
 * Rota para permitir utilizar arquivos de traducao do laravel (dessa lib) no vue js
 */
Route::get('/libs/panic/lang.trans/{file}', function () {
    $fileNames = explode(',', Request::segment(4));
    $lang = config('app.locale');
    $files = array();
    foreach ($fileNames as $fileName) {
        array_push($files, __DIR__ . '/../resources/lang/' . $lang . '/' . $fileName . '.php');
    }
    $strings = [];
    foreach ($files as $file) {
        $name = basename($file, '.php');
        $strings[$name] = require $file;
    }

    return response('window.lang = ' . json_encode($strings) . ';')
            ->header('Content-Type', 'text/javascript');

});