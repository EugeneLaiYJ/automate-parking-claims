<?php

use App\Http\Controllers\fileController;
use Illuminate\Support\Facades\Route;

Route::controller(fileController::class)->group(function(){
    Route::get('/', 'index');
});

Route::controller(fileController::class)->group(function(){
    Route::post('/', 'store');
});


