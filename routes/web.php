<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectPdfController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/admin/projects/pdf-download', [ProjectPdfController::class, 'download'])
        ->name('projects.pdf.download');
});
