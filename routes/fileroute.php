<?php 

use Illuminate\Support\Facades\Route;
use Abianbiya\Filehandler\Controllers\FileHandlerController;

Route::get('/f/{slug}', [FileHandlerController::class, 'fileSlug'])->middleware(['web'])->name('public.file.read');

