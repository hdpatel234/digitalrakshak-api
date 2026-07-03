<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use Illuminate\Support\Facades\Hash;

Route::get('password', function () {
    $plainPassword = 'User@123';
    $hashedPassword = Hash::make($plainPassword);
    echo $hashedPassword;
});
