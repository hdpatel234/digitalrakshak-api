<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/sentry-test', function () { throw new \Exception('Sentry test!'); });
