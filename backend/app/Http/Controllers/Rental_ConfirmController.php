<?php
/**
 * ============================================================
 * レンタル確認コントローラー (Rental_ConfirmController)
 * ============================================================
 * 
 * 【このコントローラーの役割】
 * レンタル確認画面の表示と予約処理を担当します。
 * 
 * 【処理の流れ】
 * 1. 土地詳細画面からの遷移を受け取る
 * 2. 土地情報と予約情報を表示
 * 3. 予約の確定処理（store）
 * 
 * 【使用テーブル】
 *   - LAND_TABLE（土地テーブル）
 *   - RENTAL_RECORD_TABLE（貸し出し記録テーブル）
 *   - MEMBER_TABLE（会員テーブル）
 * 
 * 【対応画面】
 *   - rental_confirm.blade.php（レンタル確認画面）
 * 
 * ============================================================
 */

namespace App\Http\Controllers;

use App\Http\Resources\MemberResource;
use App\Models\Land;
use App\Models\RentalRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class Rental_ConfirmController extends Controller
{
    /**
     * API: レンタル確認情報を取得
     */
    public function showApi($id, Request $request)
    {
        $land = Land::with('owner')
            ->where('LAND_ID', $id)
            ->firstOrFail();

        $timeStart = $request->input('time_start');
        $timeEnd = $request->input('time_end');
        $totalPrice = $this->calculatePrice($land, $timeStart, $timeEnd);

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
            ],
            'owner' => $land->owner ? new MemberResource($land->owner) : null,
            'time_start' => $timeStart,
            'time_end' => $timeEnd,
            'total_price' => $totalPrice,
        ]);
    }

    /**
     * API: レンタル予約を確定
     */
    public function storeApi($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'time_start' => 'required|date',
            'time_end' => 'required|date|after:time_start',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'バリデーションエラーが発生しました。',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = Auth::user();
        $land = Land::findOrFail($id);

        $startDateTime = Carbon::parse($request->time_start);
        $endDateTime = Carbon::parse($request->time_end);

        $record = RentalRecord::create([
            'PRICE' => $land->PRICE,
            'PRICE_UNIT' => $land->PRICE_UNIT ?? 0,
            'RENTAL_START_DATE' => $startDateTime->toDateString(),
            'RENTAL_END_DATE' => $endDateTime->toDateString(),
            'RENTAL_START_TIME' => $startDateTime->toTimeString(),
            'RENTAL_END_TIME' => $endDateTime->toTimeString(),
            'LAND_ID' => $land->LAND_ID,
            'USER_ID' => $user->USER_ID,
        ]);

        return response()->json([
            'message' => 'レンタル予約が完了しました。',
            'rental' => [
                'id' => $record->RECORD_ID,
                'land_id' => $record->LAND_ID,
                'user_id' => $record->USER_ID,
                'price' => $record->PRICE,
                'price_unit' => $record->PRICE_UNIT,
                'rental_start_date' => $record->RENTAL_START_DATE,
                'rental_end_date' => $record->RENTAL_END_DATE,
                'rental_start_time' => $record->RENTAL_START_TIME,
                'rental_end_time' => $record->RENTAL_END_TIME,
            ],
        ], 201);
    }
    /**
     * レンタル確認画面を表示
     * 
     * 【URL】GET /rental/confirm/{id}
     * 【ルート名】rental.confirm
     * 【ビュー】resources/views/rental_confirm.blade.php
     * 
     * 【受け取るパラメータ】
     * - id: 土地ID
     * - time_start: 利用開始日時
     * - time_end: 利用終了日時
     *
     * @param  int  $id  土地ID
     * @param  \Illuminate\Http\Request  $request  HTTPリクエスト
     * @return \Illuminate\View\View レンタル確認画面
     */
    public function show($id, Request $request)
    {
        // ============================================================
        // 1. 土地情報を取得（オーナー情報も一緒に）
        // ============================================================
        $land = Land::with('owner')
            ->where('LAND_ID', $id)
            ->firstOrFail();

        // ============================================================
        // 2. 利用日時の取得
        // ============================================================
        $timeStart = $request->input('time_start');
        $timeEnd = $request->input('time_end');

        // ============================================================
        // 3. 料金計算
        // ============================================================
        $totalPrice = $this->calculatePrice($land, $timeStart, $timeEnd);

        // ============================================================
        // 4. 都道府県の一覧（表示用）
        // ============================================================
        $prefectures = $this->getPrefectures();

        // ============================================================
        // 5. ビューにデータを渡す
        // ============================================================
        return view('rental_confirm', [
            'land' => $land,
            'time_start' => $timeStart,
            'time_end' => $timeEnd,
            'total_price' => $totalPrice,
            'prefectures' => $prefectures,
        ]);
    }

    /**
     * レンタル予約を確定
     * 
     * 【URL】POST /rental/confirm/{id}
     * 【ルート名】rental.store
     * 
     * 【処理の流れ】
     * 1. バリデーション
     * 2. 貸し出し記録の作成
     * 3. 成功メッセージとともにリダイレクト
     *
     * @param  int  $id  土地ID
     * @param  \Illuminate\Http\Request  $request  HTTPリクエスト
     * @return \Illuminate\Http\RedirectResponse リダイレクトレスポンス
     */
    public function store($id, Request $request)
    {
        // ============================================================
        // 1. バリデーション
        // ============================================================
        $request->validate([
            'time_start' => 'required|date',
            'time_end' => 'required|date|after:time_start',
        ]);

        // ============================================================
        // 2. ログインユーザーの取得
        // ============================================================
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // ============================================================
        // 3. 土地情報を取得
        // ============================================================
        $land = Land::findOrFail($id);

        // ============================================================
        // 4. 日時のパース
        // ============================================================
        $startDateTime = Carbon::parse($request->time_start);
        $endDateTime = Carbon::parse($request->time_end);

        // ============================================================
        // 5. 貸し出し記録の作成
        // ============================================================
        RentalRecord::create([
            'PRICE' => $land->PRICE,
            'PRICE_UNIT' => $land->PRICE_UNIT ?? 0,
            'RENTAL_START_DATE' => $startDateTime->toDateString(),
            'RENTAL_END_DATE' => $endDateTime->toDateString(),
            'RENTAL_START_TIME' => $startDateTime->toTimeString(),
            'RENTAL_END_TIME' => $endDateTime->toTimeString(),
            'LAND_ID' => $land->LAND_ID,
            'USER_ID' => $user->USER_ID,
        ]);

        // ============================================================
        // 6. 成功メッセージとともにリダイレクト
        // ============================================================
        return redirect()->route('rental_list')
            ->with('success', 'レンタル予約が完了しました。');
    }

    /**
     * 料金を計算
     *
     * @param  \App\Models\Land  $land  土地情報
     * @param  string|null  $timeStart  利用開始日時
     * @param  string|null  $timeEnd  利用終了日時
     * @return int 合計金額
     */
    private function calculatePrice($land, $timeStart, $timeEnd)
    {
        if (!$timeStart || !$timeEnd) {
            return $land->PRICE;
        }

        $start = Carbon::parse($timeStart);
        $end = Carbon::parse($timeEnd);

        // 料金単位に応じて計算
        switch ($land->PRICE_UNIT) {
            case 0: // 日額
                $days = $start->diffInDays($end);
                return $land->PRICE * max(1, $days);
            case 1: // 時間
                $hours = $start->diffInHours($end);
                return $land->PRICE * max(1, $hours);
            case 2: // 15分
                $minutes = $start->diffInMinutes($end);
                $units = ceil($minutes / 15);
                return $land->PRICE * max(1, $units);
            default:
                return $land->PRICE;
        }
    }

    /**
     * 都道府県の一覧を取得
     *
     * @return array 都道府県の配列
     */
    private function getPrefectures()
    {
        return [
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
    }
}
