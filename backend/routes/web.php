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
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ContactDetailController;
use App\Http\Controllers\ContactListController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LandController;
use App\Http\Controllers\LandDetailController;
use App\Http\Controllers\LandPublicController;
use App\Http\Controllers\LoanDetailController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\MyLandListController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RentalController;
use App\Http\Controllers\Rental_ConfirmController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SearchListController;
use App\Http\Controllers\TradeDetailController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserDetailController;
use App\Http\Controllers\UserListController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ============================================================
// トップ画面
// ============================================================

Route::get('/', [HomeController::class, 'index'])->name('home');


// ============================================================
// 認証ルート（ログイン・会員登録・ログアウト）
// ============================================================

// ゲスト（未ログイン）のみアクセス可能
Route::middleware('guest')->group(function () {
    // ログイン
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // 会員登録
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// ログアウト
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');


// ============================================================
// ユーザー関連ルート（ログイン必須）
// ============================================================

Route::middleware('auth')->group(function () {

    // --- マイページ ---
    Route::get('/mypage', [UserController::class, 'mypage'])->name('mypage');

    // --- プロフィール編集 ---
    Route::get('/prof_custom', [ProfileController::class, 'edit'])->name('prof_custom');
    Route::post('/prof_custom', [ProfileController::class, 'update'])->name('prof_custom.update');
    Route::get('/prof_check', [ProfileController::class, 'confirm'])->name('prof_check');
    Route::post('/prof_check', [ProfileController::class, 'store'])->name('prof_check.store');

    // --- 土地管理 ---
    Route::get('/my_land_list', [MyLandListController::class, 'index'])->name('my_land_list');
    Route::get('/loan_detail/{id}', [LoanDetailController::class, 'show'])->name('loan_detail');
    Route::get('/land_public/{id}', [LandPublicController::class, 'edit'])->name('land_public');
    Route::post('/land_public/{id}/toggle_status', [LandPublicController::class, 'toggleStatus'])->name('land_public.toggle_status');

    // --- 土地登録 ---
    Route::get('/land/register', [LandController::class, 'showRegisterForm'])->name('land.register');
    Route::post('/land/register', [LandController::class, 'register']);
    Route::get('/land/register/confirm', [LandController::class, 'showConfirm'])->name('land.register.confirm');
    Route::post('/land/register/store', [LandController::class, 'store'])->name('land.register.store');

    // --- 土地詳細・予約 ---
    Route::get('/land/{id}', [LandDetailController::class, 'show'])->name('land.detail');
    Route::get('/rental/confirm/{id}', [Rental_ConfirmController::class, 'show'])->name('rental.confirm');
    Route::post('/rental/confirm/{id}', [Rental_ConfirmController::class, 'store'])->name('rental.store');

    // --- レンタル一覧（借りている土地一覧） ---
    Route::get('/rental_list', [RentalController::class, 'index'])->name('rental_list');
    Route::get('/rental_list/{id}', [RentalController::class, 'show'])->name('rental_list.show');

    // --- 取引完了一覧 ---
    Route::get('/trade_fin_list', [RentalController::class, 'completedList'])->name('trade_fin_list');
    Route::get('/rental/history', [RentalController::class, 'completedList'])->name('rental.history'); // trade_fin_list のエイリアス

    // --- 取引完了詳細 ---
    Route::get('/trade/{id}', [TradeDetailController::class, 'show'])->name('trade.detail');

    // --- メッセージ（DM）---
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/create', [MessageController::class, 'create'])->name('messages.create');
    Route::get('/messages/{userId}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
    Route::get('/messages/poll/{userId}', [MessageController::class, 'poll'])->name('messages.poll');
    Route::post('/messages/search', [MessageController::class, 'search'])->name('messages.search');

    // --- レビュー ---
    Route::get('/review/create/{recordId}', [ReviewController::class, 'create'])->name('review.create');
    Route::post('/review/{recordId}', [ReviewController::class, 'store'])->name('review.store');

    // --- 管理画面（問い合わせ） ---
    Route::get('/admin/contact_list', [ContactListController::class, 'index'])->name('admin.contact_list');
    Route::get('/admin/contact/{id}', [ContactDetailController::class, 'show'])->name('admin.contact.detail');
    Route::put('/admin/contact/{id}/status', [ContactDetailController::class, 'updateStatus'])->name('admin.contact.status');
    Route::post('/admin/contact/{id}/reply', [ContactDetailController::class, 'reply'])->name('admin.contact.reply');

    // --- 管理画面（ユーザー管理） ---
    Route::get('/admin/users', [UserListController::class, 'index'])->name('admin.user_list');
    Route::get('/admin/users/{id}', [UserDetailController::class, 'show'])->name('admin.user.detail');
    Route::put('/admin/users/{id}', [UserDetailController::class, 'update'])->name('admin.user.update');
    Route::delete('/admin/users/{id}', [UserDetailController::class, 'destroy'])->name('admin.user.destroy');
});


// ============================================================
// 公開ビュー確認用ルート（認証不要）
// ============================================================

// 土地検索結果
Route::get('/search', [SearchListController::class, 'index'])->name('search');
Route::get('/lands', [SearchListController::class, 'index'])->name('lands.index'); // search のエイリアス
Route::get('/lands/{id}', [LandDetailController::class, 'show'])->name('lands.show'); // land.detail のエイリアス

// お問い合わせフォーム
Route::get('/contact', function () {
    return view('contact');
})->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

// ユーザー詳細（他ユーザープロフィール）
Route::get('/users/{id}', function ($id) {
    $user = \App\Models\Member::findOrFail($id);
    return view('user_detail', compact('user'));
})->name('user.show');


// ============================================================
// 開発用ルート（本番前に削除）
// ============================================================

// テストログイン
Route::get('/test-login', function () {
    $user = \App\Models\Member::first();
    if ($user) {
        \Illuminate\Support\Facades\Auth::login($user);
        return redirect('/mypage')->with('success', 'テストログインしました: ' . $user->USERNAME);
    }
    return 'ユーザーが存在しません。php artisan db:seed --class=TestUserSeeder を実行してください。';
});

// レイアウト確認
Route::get('/test-layout', function () {
    return view('test-layout');
});

// レンタル一覧テスト
Route::get('/test-rentals', function () {
    $rentals = collect([
        (object) [
            'RECORD_ID' => 1,
            'PRICE' => 3000,
            'PRICE_UNIT' => 0,
            'RENTAL_START_DATE' => now()->addDays(2),
            'RENTAL_END_DATE' => now()->addDays(9),
            'land' => (object) [
                'LAND_ID' => 1,
                'CITY' => '渋谷区',
                'STREET_ADDRESS' => '神南1-2-3',
                'AREA' => 25.50,
                'IMAGE' => null,
            ]
        ],
    ]);
    return view('rental_list', ['rentals' => $rentals, 'detailRoute' => 'dev.rental-detail']);
})->name('test-rentals');

// レンタル詳細テスト
Route::get('/dev/rental-detail', function () {
    $rental = (object) [
        'RECORD_ID' => 1,
        'PRICE' => 3000,
        'PRICE_UNIT' => 0,
        'RENTAL_START_DATE' => now()->addDays(2),
        'RENTAL_END_DATE' => now()->addDays(7),
        'RENTAL_START_TIME' => now()->setTime(8, 0),
        'RENTAL_END_TIME' => now()->setTime(20, 0),
        'land' => (object) [
            'CITY' => '渋谷区',
            'STREET_ADDRESS' => '神南1-2-3',
            'AREA' => 25.5,
            'IMAGE' => null,
        ],
        'review' => (object) [
            'RATING' => 5,
            'COMMENT' => 'テストレビューです。',
            'created_at' => now()->subDay(),
        ],
    ];
    return view('rental_detail', ['rental' => $rental, 'backRoute' => 'test-rentals']);
})->name('dev.rental-detail');

// ユーザー一覧テスト
Route::get('/test-users', function () {
    $users = collect([
        (object) ['id' => 1, 'name' => '田中 太郎', 'email' => 'tanaka.taro@example.com', 'created_at' => now()->subDays(30)],
        (object) ['id' => 2, 'name' => '佐藤 花子', 'email' => 'sato.hanako@example.com', 'created_at' => now()->subDays(60)],
    ]);
    return view('user_list', compact('users'));
});

// ユーザー詳細テスト
Route::get('/test-user-detail', function () {
    $user = (object) [
        'id' => 1,
        'login_id' => 'tanaka_taro',
        'name' => '田中 太郎',
        'email' => 'tanaka.taro@example.com',
        'phone' => '090-1234-5678',
        'birthday' => '1990-04-15',
        'gender' => 'male',
        'birthday_public' => 'private',
        'gender_public' => 'public',
        'status' => 'active',
        'bio' => '都内在住のフリーランスエンジニアです。',
        'avatar' => null,
        'created_at' => now()->subDays(30),
        'updated_at' => now()->subDays(5),
    ];
    return view('user_detail', compact('user'));
});
