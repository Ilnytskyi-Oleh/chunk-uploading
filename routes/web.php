<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileUploadController;

Route::get('/', function () {
    return view('welcome');
});


Route::post('/upload', [FileUploadController::class, 'upload']);
