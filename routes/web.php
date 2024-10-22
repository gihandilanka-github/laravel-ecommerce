<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json('Access denied', 403);
});
