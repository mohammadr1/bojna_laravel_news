<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController; // مطمئن شوید از این خط استفاده کنید

Route::get('/daily-report', [ReportController::class, 'dailyReport']);
// Route::get('/daily-report', [App\Http\Controllers\ReportController::class, 'dailyReport']);
