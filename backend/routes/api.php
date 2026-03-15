<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\LandController;
use App\Http\Controllers\LandDetailController;
use App\Http\Controllers\LandPublicController;
use App\Http\Controllers\LoanDetailController;
use App\Http\Controllers\MyLandListController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RentalController;
use App\Http\Controllers\Rental_ConfirmController;
use App\Http\Controllers\SearchListController;
use App\Http\Controllers\TradeDetailController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserDetailController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ContactListController;
use App\Http\Controllers\ContactDetailController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:api');

Route::get('/lands', [SearchListController::class, 'indexApi'])
    ->middleware('throttle:api');
Route::get('/lands/{id}', [LandDetailController::class, 'showApi'])
    ->middleware('throttle:api');

Route::post('/contact', [ContactController::class, 'storeApi'])
    ->middleware('throttle:api');

Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::get('/mypage', [UserController::class, 'mypageApi']);
    Route::get('/profile', [ProfileController::class, 'editApi']);
    Route::put('/profile', [ProfileController::class, 'updateApi']);
    Route::post('/profile', [ProfileController::class, 'updateApi']);
    Route::get('/my/lands', [MyLandListController::class, 'indexApi']);
    Route::post('/lands/confirm', [LandController::class, 'confirmApi']);
    Route::post('/lands', [LandController::class, 'storeApi']);
    Route::patch('/lands/{id}/toggle-status', [LandPublicController::class, 'toggleStatusApi']);
    Route::get('/rentals', [RentalController::class, 'indexApi']);
    Route::get('/rentals/{id}', [RentalController::class, 'showApi']);
    Route::get('/rental-history', [RentalController::class, 'historyApi']);
    Route::get('/rental/confirm/{id}', [Rental_ConfirmController::class, 'showApi']);
    Route::post('/rental/confirm/{id}', [Rental_ConfirmController::class, 'storeApi']);
    Route::get('/loan-detail/{id}', [LoanDetailController::class, 'showApi']);
    Route::get('/trades', [TradeDetailController::class, 'listApi']);
    Route::get('/trades/{recordId}', [TradeDetailController::class, 'showApi']);
    Route::get('/messages', [MessageController::class, 'indexApi']);
    Route::get('/messages/{partnerId}', [MessageController::class, 'showApi']);
    Route::post('/messages', [MessageController::class, 'storeApi']);
    Route::get('/messages/poll/{partnerId}', [MessageController::class, 'pollApi']);
    Route::get('/messages/search', [MessageController::class, 'search']);
    Route::post('/reviews/{recordId}', [ReviewController::class, 'storeApi']);
});

Route::middleware(['auth:sanctum', 'admin', 'throttle:api'])->group(function () {
    Route::get('/admin/contacts', [ContactListController::class, 'indexApi']);
    Route::get('/admin/contacts/{id}', [ContactListController::class, 'showApi']);
    Route::get('/admin/contact/{id}', [ContactDetailController::class, 'showApi']);
    Route::put('/admin/contact/{id}/status', [ContactDetailController::class, 'updateStatusApi']);
    Route::post('/admin/contact/{id}/reply', [ContactDetailController::class, 'replyApi']);
});

Route::get('/users/{id}', [UserDetailController::class, 'showApi'])
    ->middleware('throttle:api');
