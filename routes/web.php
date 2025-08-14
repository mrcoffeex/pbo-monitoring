<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectPdfController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/admin/projects/{project}/pdf', [ProjectPdfController::class, 'single'])
        ->name('projects.pdf.single');
});
