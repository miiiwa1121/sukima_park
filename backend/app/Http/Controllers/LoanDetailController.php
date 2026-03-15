<?php
/**
 * ============================================================
 * 貸出中詳細コントローラー (LoanDetailController.php)
 * ============================================================
 * 
 * 【対応画面】
 *   loan_detail.blade.php（貸出中詳細）
 * 
 * 【画面定義】
 *   context/画面レイアウト/my_land_detail_screen.html
 * 
 * 【このコントローラーの役割】
 *   公開中の土地に対する予約状況や利用者情報を表示
 *   土地オーナーのみがアクセス可能
 * 
 * ============================================================
 */

namespace App\Http\Controllers;

use App\Models\Land;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\MemberResource;

class LoanDetailController extends Controller
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
     * 貸出中詳細を表示
     * 
     * URL: /loan_detail/{id}
     * ルート名: loan_detail
     * 
     * @param int $id 土地ID
     * @return \Illuminate\Contracts\View\View
     */
    public function show($id)
    {
        // 土地を取得
        $land = Land::findOrFail($id);

        // 自分の土地かチェック
        if ($land->USER_ID !== Auth::id()) {
            abort(403, 'この土地へのアクセス権がありません');
        }

        // ビューに渡す
        return view('loan_detail', [
            'land' => $land,
            'prefectures' => self::PREFECTURES,
        ]);
    }

    /**
     * API: 貸出中詳細を取得
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function showApi($id)
    {
        $land = Land::with(['rentalRecords.renter'])
            ->where('LAND_ID', $id)
            ->firstOrFail();

        if ($land->USER_ID !== Auth::id()) {
            return response()->json([
                'message' => 'この土地へのアクセス権がありません。',
            ], 403);
        }

        $rentals = $land->rentalRecords->map(function ($record) {
            return [
                'id' => $record->RECORD_ID,
                'price' => $record->PRICE,
                'price_unit' => $record->PRICE_UNIT,
                'rental_start_date' => $record->RENTAL_START_DATE,
                'rental_end_date' => $record->RENTAL_END_DATE,
                'rental_start_time' => $record->RENTAL_START_TIME,
                'rental_end_time' => $record->RENTAL_END_TIME,
                'renter' => $record->renter ? new MemberResource($record->renter) : null,
            ];
        });

        return response()->json([
            'land' => [
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
            ],
            'rentals' => $rentals,
        ]);
    }
}
