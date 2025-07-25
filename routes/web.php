<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Faker\Factory as Faker;
use App\Http\Controllers\SalesController;

//dont need this no more
// Route::get('/', function () {
//     return view('welcome');
// });


Route::get('/', [SalesController::class, 'index']);

Route::post('/upload', [SalesController::class, 'upload']);

Route::get('/store-data' , [SalesController::class, 'store']);
