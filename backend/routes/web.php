<?php
/**
 * ============================================================
 * Webルート定義 (web.php)
 * ============================================================
 * 
 * 【このファイルの役割】
 * URLとコントローラーのアクションを紐づける「ルーティング定義」
 * 
 * 【基本的な書き方】
 * Route::get('/URL', [コントローラー::class, 'メソッド名'])->name('ルート名');
 * 
 * 【HTTPメソッド】
 * - get:  データの取得（画面表示など）
 * - post: データの送信（フォーム送信など）
 * 
 * 【ミドルウェア】
 * - guest: 未ログインユーザーのみアクセス可能
 * - auth:  ログインユーザーのみアクセス可能
 * 
 * ============================================================
 */

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactDetailController;
use App\Http\Controllers\ContactListController;
use App\Http\Controllers\UserDetailController;
use App\Http\Controllers\UserListController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes (Admin UI only)
|--------------------------------------------------------------------------
*/

// JSON認証エンドポイント（SPA/Next.js向け）
Route::middleware('guest')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');

// 管理画面（Blade）
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/contact_list', [ContactListController::class, 'index'])->name('admin.contact_list');
    Route::get('/admin/contact/{id}', [ContactDetailController::class, 'show'])->name('admin.contact.detail');
    Route::put('/admin/contact/{id}/status', [ContactDetailController::class, 'updateStatus'])->name('admin.contact.status');
    Route::post('/admin/contact/{id}/reply', [ContactDetailController::class, 'reply'])->name('admin.contact.reply');

    Route::get('/admin/users', [UserListController::class, 'index'])->name('admin.user_list');
    Route::get('/admin/users/{id}', [UserDetailController::class, 'show'])->name('admin.user.detail');
    Route::put('/admin/users/{id}', [UserDetailController::class, 'update'])->name('admin.user.update');
    Route::delete('/admin/users/{id}', [UserDetailController::class, 'destroy'])->name('admin.user.destroy');
});
    return view('user_detail', compact('user'));
});
