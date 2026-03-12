<?php

use Illuminate\Support\Facades\Route;
use App\Http\Api\V1\Task\Controllers\TaskController;

Route::prefix('v1')->group(function () {
    Route::apiResource('tasks', TaskController::class);
});
