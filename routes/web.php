<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SalesController;


Route::get('/', [SalesController::class, 'index']);
//name route 'upload'
Route::post('/upload', [SalesController::class, 'upload'])->name('upload');
//name route 'batch'
Route::get('/batch', [SalesController::class, 'batch'])->name('batch');
Route::get('/batch/view/{id}', [SalesController::class, 'viewProgress'])->name('viewProgress');
Route::get('/last', [SalesController::class, 'showLastBranch'])->name('showLastBranch');
