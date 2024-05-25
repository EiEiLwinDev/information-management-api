<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['INFM API' => app()->version()];
});

require __DIR__.'/auth.php';
