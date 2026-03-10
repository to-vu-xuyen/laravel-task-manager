<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\web\TaskController;

Route::get('/', function () {
    return view('welcome');
});
