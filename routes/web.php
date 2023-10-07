<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CsvUploadController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route for displaying the file upload form
Route::get('/upload', [CsvUploadController::class, 'showUploadForm'])->name('upload.form');

// Route for handling file uploads
Route::post('/upload', [CsvUploadController::class, 'upload'])->name('upload');

Route::get('/get-files', [CsvUploadController::class, 'listUploads']);

Route::get('/redis', [CsvUploadController::class, 'testRedisConnection']);

Route::get('/', function () {
    return view('welcome');
});
