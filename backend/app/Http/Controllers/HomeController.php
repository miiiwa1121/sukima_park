<?php
/**
 * ============================================================
 * ホームコントローラー (HomeController.php)
 * ============================================================
 * 
 * トップ画面（index.php相当）の表示を担当するコントローラー
 * 
 * 【対応画面】
 *   - index.csv（トップ画面 - index.php）
 * 
 * 【主な機能】
 *   - トップ画面の表示
 *   - ログインユーザーの最近借りた土地の取得
 *   - 検索画面へのリダイレクト
 * 
 * 【使用テーブル】
 *   - RENTAL_RECORD_TABLE（貸し出し記録テーブル）
 *   - LAND_TABLE（土地テーブル）※リレーション経由
 * 
 * ============================================================
 */

namespace App\Http\Controllers;

use App\Models\RentalRecord;  // 貸し出し記録モデル
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;  // 認証ファサード（ログイン状態確認用）

class HomeController extends Controller
{
    /**
     * トップ画面を表示
     * 
     * 【処理内容】
     * 1. ログイン状態を確認
     * 2. ログイン中の場合、そのユーザーの最近借りた土地を5件取得
     * 3. home.blade.phpにデータを渡して表示
     * 
     * 【取得データ】
     * $recentRentals: 最近借りた土地のコレクション（5件まで）
     *   - RENTAL_RECORD_TABLEから取得
     *   - 土地情報（LAND_TABLE）をリレーションで結合
     *   - RECORD_IDの降順（新しい順）でソート
     * 
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        // 空のコレクションで初期化（未ログイン時用）
        $recentRentals = collect();

        // ログイン中のユーザーの最近借りた土地を取得
        // Auth::check() でログイン状態を確認
        if (Auth::check()) {
            $recentRentals = RentalRecord::with('land')  // 土地情報も一緒に取得（Eager Loading）
                ->where('USER_ID', Auth::id())           // ログインユーザーのIDで絞り込み
                ->orderByDesc('RECORD_ID')               // 新しい順にソート
                ->take(5)                                // 5件まで取得
                ->get();
        }

        // home.blade.phpを表示し、$recentRentalsを渡す
        return view('home', compact('recentRentals'));
    }

    /**
     * 検索結果画面へリダイレクト
     * 
     * 【処理内容】
     * 検索フォームからのリクエストを受け取り、
     * 検索結果画面（lands.search）にリダイレクトする
     * 
     * @param Request $request 検索フォームからのリクエスト
     * @return \Illuminate\Http\RedirectResponse
     */
    public function search(Request $request)
    {
        // リクエストパラメータをすべて検索画面に渡してリダイレクト
        return redirect()->route('lands.search', $request->all());
    }
}
