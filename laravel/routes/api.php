<?php

use App\Http\Controllers\Api\PateintController;
use Illuminate\Support\Facades\Route;

Route::post('/patients', [PateintController::class, 'index'])->name('api-patients-search');
