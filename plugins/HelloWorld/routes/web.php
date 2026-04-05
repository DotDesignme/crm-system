<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'web'])->group(function () {
    Route::get('/plugins/hello-world', function () {
        return view('hello-world::index');
    })->name('plugins.hello-world.index');
});
