<?php
/**
 * ============================================================
 * 自己保持土地一覧コントローラー (MyLandListController.php)
 * ============================================================
 * 
 * 【対応画面】
 *   my_land_list.blade.php（自己保持土地一覧）
 * 
 * 【画面定義】
 *   context/画面一覧/my_land_list.csv
 * 
 * 【このコントローラーの役割】
 *   ログインユーザーの全土地（公開・非公開両方）を表示
 *   ステータスでフィルタリング可能
 * 
 * ============================================================
 */

namespace App\Http\Controllers;

use App\Models\Land;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyLandListController extends Controller
{
    /**
     * 都道府県コードと名前の対応配列
     */
    private const PREFECTURES = [
        1 => '北海道', 2 => '青森県', 3 => '岩手県', 4 => '宮城県',
        5 => '秋田県', 6 => '山形県', 7 => '福島県', 8 => '茨城県',
        9 => '栃木県', 10 => '群馬県', 11 => '埼玉県', 12 => '千葉県',
        13 => '東京都', 14 => '神奈川県', 15 => '新潟県', 16 => '富山県',
        17 => '石川県', 18 => '福井県', 19 => '山梨県', 20 => '長野県',
        21 => '岐阜県', 22 => '静岡県', 23 => '愛知県', 24 => '三重県',
        25 => '滋賀県', 26 => '京都府', 27 => '大阪府', 28 => '兵庫県',
        29 => '奈良県', 30 => '和歌山県', 31 => '鳥取県', 32 => '島根県',
        33 => '岡山県', 34 => '広島県', 35 => '山口県', 36 => '徳島県',
        37 => '香川県', 38 => '愛媛県', 39 => '高知県', 40 => '福岡県',
        41 => '佐賀県', 42 => '長崎県', 43 => '熊本県', 44 => '大分県',
        45 => '宮崎県', 46 => '鹿児島県', 47 => '沖縄県',
    ];

    /**
     * 自己保持土地一覧を表示
     * 
     * URL: /my_land_list
     * ルート名: my_land_list
     * 
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        // クエリパラメータからstatusを取得（デフォルトはall）
        $status = $request->query('status', 'all');

        // ログインユーザーの土地を取得するクエリを構築
        $query = Land::where('USER_ID', Auth::id());

        // statusでフィルタリング
        if ($status !== 'all') {
            $query->where('STATUS', $status);
        }

        // 新しい順にソートして取得
        $lands = $query->orderByDesc('LAND_ID')->get();

        // ビューに渡す
        return view('my_land_list', [
            'lands' => $lands,
            'currentStatus' => $status,
            'prefectures' => self::PREFECTURES,
        ]);
    }

    /**
     * API: 自己保持土地一覧
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexApi(Request $request)
    {
        $status = $request->query('status', 'all');

        $query = Land::where('USER_ID', Auth::id());

        if ($status !== 'all') {
            $query->where('STATUS', $status);
        }

        $lands = $query->orderByDesc('LAND_ID')->get()->map(fn (Land $land) => [
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
            'lands' => $lands,
            'current_status' => $status,
        ]);
    }
}
