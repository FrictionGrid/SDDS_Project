<?php

use App\Http\Controllers\ApifyWebhookController;
use App\Http\Controllers\LayoutChatbotController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// Apify Webhook
Route::post('/apify/webhook', [ApifyWebhookController::class, 'webhook']);

