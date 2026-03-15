<?php
/**
 * ============================================================
 * 土地詳細コントローラー (LandDetailController)
 * ============================================================
 * 
 * 【このコントローラーの役割】
 * 土地の詳細情報を表示します。
 * 
 * 【処理の流れ】
 * 1. URLパラメータから土地IDを取得
 * 2. 土地情報とオーナー情報、レビュー情報を取得
 * 3. 結果をビューに渡す
 * 
 * 【使用テーブル】
 *   - LAND_TABLE（土地テーブル）
 *   - MEMBER_TABLE（会員テーブル、オーナー情報）
 *   - REVIEW_COMMENT_TABLE（レビューテーブル）
 * 
 * 【対応画面】
 *   - land_detail.blade.php（土地詳細画面）
 * 
 * ============================================================
 */

namespace App\Http\Controllers;

use App\Http\Resources\MemberResource;
use App\Models\Land;
use Illuminate\Http\Request;

class LandDetailController extends Controller
{
    /**
     * API: 土地詳細情報を取得
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function showApi($id)
    {
        $land = Land::with(['owner', 'reviews.reviewer'])
            ->where('LAND_ID', $id)
            ->firstOrFail();

        $reviews = $land->reviews->map(function ($review) {
            return [
                'id' => $review->REVIEW_COMMENT_ID ?? $review->id ?? null,
                'rating' => $review->RATING ?? null,
                'comment' => $review->COMMENT ?? null,
                'created_at' => $review->created_at ?? null,
                'reviewer' => $review->reviewer
                    ? new MemberResource($review->reviewer)
                    : null,
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
                'title_deed' => $land->TITLE_DEED,
                'description' => $land->DESCRIPTION,
                'rental_start_date' => $land->RENTAL_START_DATE,
                'rental_end_date' => $land->RENTAL_END_DATE,
                'rental_start_time' => $land->RENTAL_START_TIME,
                'rental_end_time' => $land->RENTAL_END_TIME,
                'price' => $land->PRICE,
                'price_unit' => $land->PRICE_UNIT,
                'status' => $land->STATUS,
            ],
            'owner' => $land->owner ? new MemberResource($land->owner) : null,
            'reviews' => $reviews,
        ]);
    }
    /**
     * 土地詳細画面を表示
     * 
     * 【URL】GET /land/{id}
     * 【ルート名】land.detail
     * 【ビュー】resources/views/land_detail.blade.php
     * 
     * 【取得データ】
     * - $land: 土地の詳細情報
     * - オーナー情報、レビュー情報をリレーションで取得
     *
     * @param  int  $id  土地ID
     * @return \Illuminate\View\View 土地詳細画面
     */
    public function show($id)
    {
        // ============================================================
        // 土地情報を取得（オーナーとレビュー情報も一緒に）
        // ============================================================
        $land = Land::with(['owner', 'reviews.reviewer'])
            ->where('LAND_ID', $id)
            ->firstOrFail();

        // ============================================================
        // 都道府県の一覧（表示用）
        // ============================================================
        $prefectures = [
            1 => '北海道',
            2 => '青森県',
            3 => '岩手県',
            4 => '宮城県',
            5 => '秋田県',
            6 => '山形県',
            7 => '福島県',
            8 => '茨城県',
            9 => '栃木県',
            10 => '群馬県',
            11 => '埼玉県',
            12 => '千葉県',
            13 => '東京都',
            14 => '神奈川県',
            15 => '新潟県',
            16 => '富山県',
            17 => '石川県',
            18 => '福井県',
            19 => '山梨県',
            20 => '長野県',
            21 => '岐阜県',
            22 => '静岡県',
            23 => '愛知県',
            24 => '三重県',
            25 => '滋賀県',
            26 => '京都府',
            27 => '大阪府',
            28 => '兵庫県',
            29 => '奈良県',
            30 => '和歌山県',
            31 => '鳥取県',
            32 => '島根県',
            33 => '岡山県',
            34 => '広島県',
            35 => '山口県',
            36 => '徳島県',
            37 => '香川県',
            38 => '愛媛県',
            39 => '高知県',
            40 => '福岡県',
            41 => '佐賀県',
            42 => '長崎県',
            43 => '熊本県',
            44 => '大分県',
            45 => '宮崎県',
            46 => '鹿児島県',
            47 => '沖縄県',
        ];

        // ============================================================
        // ビューにデータを渡す
        // ============================================================
        return view('land_detail', [
            'land' => $land,
            'prefectures' => $prefectures,
        ]);
    }
}
