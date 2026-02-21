<?php

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('test', function () {
    echo Hash::make('User@123');
    exit();
});