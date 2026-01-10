<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerDetailController;
use App\Http\Controllers\LayoutChatbotController;


Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
// โชวแบบ realtime ในการแปลงข้อมูล
Route::get('/customers/clear-cache', [CustomerController::class, 'clearCache'])->name('customers.clearCache');
Route::get('/customers/{id}', [CustomerController::class, 'show'])->name('customers.show');

// ประวัติการติดต่อ (Contact History)
Route::post('/contacts', [CustomerDetailController::class, 'storeContact'])->name('contacts.store');
Route::put('/contacts/{id}', [CustomerDetailController::class, 'updateContact'])->name('contacts.update');
Route::delete('/contacts/{id}', [CustomerDetailController::class, 'destroyContact'])->name('contacts.destroy');

// โปรเจกต์ (Projects)
Route::post('/projects', [CustomerDetailController::class, 'storeProject'])->name('projects.store');
Route::put('/projects/{id}', [CustomerDetailController::class, 'updateProject'])->name('projects.update');
Route::delete('/projects/{id}', [CustomerDetailController::class, 'destroyProject'])->name('projects.destroy');

// เอกสารที่เกี่ยวข้อง (Documents)
Route::post('/documents', [CustomerDetailController::class, 'uploadDocument'])->name('documents.upload');
Route::get('/documents/{id}/download', [CustomerDetailController::class, 'downloadDocument'])->name('documents.download');
Route::delete('/documents/{id}', [CustomerDetailController::class, 'destroyDocument'])->name('documents.destroy');


Route::get('/email_ai', function () {
    return view('email_ai');
});

Route::get('/document', function () {
    return view('document');
});

Route::get('/login', function () {
    return view('login');
});

// หน้า chatbot
Route::post('/chatbot/chat', [LayoutChatbotController::class, 'chat'])
    ->name('chatbot.chat');