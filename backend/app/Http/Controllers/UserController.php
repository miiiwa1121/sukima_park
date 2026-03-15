<?php
/**
 * ============================================================
 * ユーザーコントローラー (UserController.php)
 * ============================================================
 * 
 * ユーザー関連の画面を担当するコントローラー
 * 
 * 【対応画面】
 *   - user_my.csv（マイページ画面）
 *   - user_other.csv（他ユーザープロフィール画面）※将来実装
 *   - prof_custom.csv（プロフィール編集画面）※将来実装
 * 
 * 【主な機能】
 *   - マイページの表示
 *   - 公開中の土地一覧の取得
 * 
 * 【使用テーブル】
 *   - MEMBER_TABLE（会員テーブル）
 *   - LAND_TABLE（土地テーブル）
 * 
 * ============================================================
 */

namespace App\Http\Controllers;

use App\Http\Resources\MemberResource;
use App\Models\Land;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * マイページを表示
     * 
     * 【処理内容】
     * 1. ログインユーザーの情報を取得
     * 2. ログインユーザーの公開中の土地を取得
     * 3. user_my.blade.phpにデータを渡して表示
     * 
     * 【取得データ】
     * $user: ログインユーザー（Memberモデル）
     *   - Auth::user()で取得
     * 
     * $publicLands: 公開中の土地のコレクション
     *   - LAND_TABLEから取得
     *   - USER_ID = ログインユーザーのID
     *   - STATUS = 1（公開中）
     * 
     * @return \Illuminate\Contracts\View\View
     */
    public function mypage($id = null)
    {
        // IDが指定されている場合、かつ自分自身のIDでない場合は、他ユーザーのプロフィールを表示
        if ($id && $id != Auth::id()) {
            $user = \App\Models\Member::findOrFail($id);
            
            // 公開中の土地を取得
            $publicLands = Land::where('USER_ID', $id)
                ->where('STATUS', 1)
                ->orderByDesc('LAND_ID')
                ->get();

            // user_other.blade.php（他ユーザー用ビュー）を表示
            return view('user_other', compact('user', 'publicLands'));
        }

        // 自分のマイページ
        $user = Auth::user();

        // ログインユーザーの公開中の土地を取得
        $publicLands = Land::where('USER_ID', Auth::id())
            ->where('STATUS', 1)  // STATUS=1 は公開中
            ->orderByDesc('LAND_ID')
            ->get();

        // user_my.blade.phpを表示し、データを渡す
        return view('user_my', compact('user', 'publicLands'));
    }

    /**
     * API: マイページ情報を取得
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function mypageApi()
    {
        $user = Auth::user();

        $publicLands = Land::where('USER_ID', $user->USER_ID)
            ->where('STATUS', 1)
            ->orderByDesc('LAND_ID')
            ->get()
            ->map(fn (Land $land) => [
                'id' => $land->LAND_ID,
                'name' => $land->NAME,
                'prefecture' => $land->PEREFECTURES,
                'city' => $land->CITY,
                'street_address' => $land->STREET_ADDRESS,
                'area' => $land->AREA,
                'image' => $land->IMAGE,
                'price' => $land->PRICE,
                'price_unit' => $land->PRICE_UNIT,
                'status' => $land->STATUS,
            ]);

        return response()->json([
            'user' => new MemberResource($user),
            'public_lands' => $publicLands,
        ]);
    }
}
