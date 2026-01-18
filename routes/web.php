<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LayoutChatbotController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerDetailController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\EmailAIController;


Route::get('dashboard_sale_task', function () {
    return view('dashboard_sale_task');
});

Route::get('dashboard_sale_customer', function () {
    return view('dashboard_sale_customer');
});

Route::get('dashboard_sale_KPI', function () {
    return view('dashboard_sale_KPI');
});


// Root redirect
Route::get('/', function () {
    return redirect('/login');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes (Require Authentication)
Route::middleware(['auth'])->group(function () {

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
Route::post('/email_ai/{id}/send', [EmailAIController::class, 'send'])
    ->name('email_ai.send');

// Email AI
Route::get('/email_ai', [EmailAIController::class, 'index'])->name('email_ai.index');
Route::get('/email_ai/{id}', [EmailAIController::class, 'show'])->name('email_ai.show');
Route::delete('/email_ai/{id}', [EmailAIController::class, 'destroy'])->name('email_ai.destroy');
Route::put('/email_ai/{id}', [EmailAIController::class, 'update'])->name('email_ai.update');

// หน้า Document
Route::get('/document', [DocumentController::class, 'index'])->name('document.index');
Route::post('/document/upload', [DocumentController::class, 'upload'])->name('document.upload');
Route::delete('/document/{uuid}', [DocumentController::class, 'destroy'])->name('document.destroy');



// หน้า chatbot
Route::post('/chatbot/chat', [LayoutChatbotController::class, 'chat'])
    ->name('chatbot.chat');

Route::get('/dashboard/sale', function () {
    return view('dashboard_sale');
});

}); // End of auth middleware group
