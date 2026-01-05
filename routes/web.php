<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;

Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
Route::get('/customers/{id}', [CustomerController::class, 'show'])->name('customers.show');


Route::get('/email_ai', function () {
    return view('email_ai');
});

Route::get('/document', function () {
    return view('document');
});

Route::get('/login', function () {
    return view('login');
});