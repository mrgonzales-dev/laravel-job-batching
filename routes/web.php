<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SalesController;


Route::get('/', [SalesController::class, 'index']);
Route::post('/upload', [SalesController::class, 'upload']);
Route::get('/batch', [SalesController::class, 'batch']);

