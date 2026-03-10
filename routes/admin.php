<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\TaskController;

Route::middleware('log.request')->group(function () {
    Route::apiResource('tasks', TaskController::class);
});
