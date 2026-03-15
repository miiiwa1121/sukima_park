<?php
/**
 * ============================================================
 * レンタルコントローラー (RentalController.php)
 * ============================================================
 * 
 * レンタル管理（土地の貸し借り）を担当するコントローラー
 * 
 * 【対応画面】
 *   - rental_list.csv（レンタル中の土地一覧）
 *   - rental_detail.csv（レンタル詳細）
 *   - completed_list.csv（取引完了一覧）
 *   - completed_detail.csv（取引完了詳細）
 *   - rental_manage.csv（貸出管理）
 *   - rental_manage_detail.csv（貸出中詳細）
 * 
 * 【主な機能】
 *   - レンタル中の土地一覧表示
 *   - レンタル詳細情報表示
 *   - 取引完了一覧表示
 *   - 取引完了詳細表示
 *   - 貸出管理一覧表示
 *   - 貸出詳細情報表示
 * 
 * 【使用テーブル】
 *   - RENTAL_RECORD_TABLE（貸し出し記録テーブル）
 *   - LAND_TABLE（土地テーブル）
 *   - MEMBER_TABLE（会員テーブル）
 * 
 * ============================================================
 */

namespace App\Http\Controllers;

use App\Http\Resources\MemberResource;
use App\Models\RentalRecord;
use App\Models\Land;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RentalController extends Controller
{
    /**
     * API: レンタル中の土地一覧
     */
    public function indexApi()
    {
        $user = Auth::user();

        $rentals = RentalRecord::where('USER_ID', $user->USER_ID)
            ->with('land.owner')
            ->get()
            ->map(fn (RentalRecord $rental) => $this->formatRental($rental));

        return response()->json([
            'rentals' => $rentals,
        ]);
    }

    /**
     * API: レンタル詳細
     */
    public function showApi($id)
    {
        $user = Auth::user();

        $rental = RentalRecord::with('land.owner')
            ->where('RECORD_ID', $id)
            ->firstOrFail();

        if ($rental->USER_ID !== $user->USER_ID) {
            return response()->json([
                'message' => 'この取引へのアクセス権がありません。',
            ], 403);
        }

        return response()->json([
            'rental' => $this->formatRental($rental),
        ]);
    }

    /**
     * API: 取引完了一覧（履歴）
     */
    public function historyApi()
    {
        $user = Auth::user();

        $trades = RentalRecord::where('USER_ID', $user->USER_ID)
            ->with(['land.owner', 'review'])
            ->get()
            ->map(function (RentalRecord $rental) {
                $data = $this->formatRental($rental);
                $data['review'] = $rental->review ? [
                    'land_review' => $rental->review->LAND_REVIEW,
                    'land_comment' => $rental->review->LAND_COMMENT,
                    'user_review' => $rental->review->USER_REVIEW,
                    'user_comment' => $rental->review->USER_COMMENT,
                    'date' => $rental->review->DATE,
                ] : null;

                return $data;
            });

        return response()->json([
            'trades' => $trades,
        ]);
    }

    /**
     * レンタル中の土地一覧を表示
     * 
     * 【処理内容】
     * 1. ログイン中のユーザーIDを取得
     * 2. そのユーザーが現在借りている土地を取得
     * 3. rental_list.blade.phpにデータを渡して表示
     * 
     * 【取得データ】
     * $rentals: 現在のレンタル記録
     *   - RENTAL_RECORD_TABLEから取得
     *   - 土地情報（LAND_TABLE）をリレーションで結合
     * 
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // ユーザーが借りている土地のレンタル記録を取得
        $rentals = RentalRecord::where('USER_ID', $user->USER_ID)
            ->with('land')
            ->get();

        return view('rental_list', ['rentals' => $rentals]);
    }

    /**
     * レンタル詳細を表示
     * 
     * 【処理内容】
     * 1. レンタル記録IDを元にレンタル情報を取得
     * 2. 自分のレンタル記録かチェック
     * 3. rental_detail.blade.phpにデータを渡して表示
     * 
     * 【取得データ】
     * $rental: レンタル記録の詳細
     *   - RENTAL_RECORD_TABLEから取得
     *   - 土地情報（LAND_TABLE）をリレーションで結合
     * 
     * @param int $id レンタル記録ID
     * @return \Illuminate\Contracts\View\View
     */
    public function show($id)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // レンタル記録を取得（自分のレンタルのみ）
        $rental = RentalRecord::where('RECORD_ID', $id)
            ->where('USER_ID', $user->USER_ID)
            ->with('land.owner')
            ->firstOrFail();

        // 合計金額を計算
        $totalAmount = $this->calculateTotalAmount($rental);

        return view('rental_detail', [
            'rental' => $rental,
            'totalAmount' => $totalAmount
        ]);
    }

    /**
     * 合計金額を計算
     * 
     * @param RentalRecord $rental
     * @return int
     */
    private function calculateTotalAmount(RentalRecord $rental): int
    {
        $days = $rental->RENTAL_START_DATE->diffInDays($rental->RENTAL_END_DATE) + 1;
        
        return match($rental->PRICE_UNIT) {
            'day' => $rental->PRICE * $days,
            'month' => $rental->PRICE,
            'year' => $rental->PRICE,
            default => $rental->PRICE * $days,
        };
    }

    /**
     * 取引完了一覧を表示
     * 
     * 【処理内容】
     * 1. ログイン中のユーザーIDを取得
     * 2. ユーザーが完了した取引記録を取得
     * 3. 土地情報とレビュー情報を結合
     * 4. trade_list.blade.phpにデータを渡して表示
     * 
     * 【取得データ】
     * $trades: 完了した取引記録
     *   - RENTAL_RECORD_TABLEから取得
     *   - 土地情報、レビューをリレーションで結合
     * 
     * @return \Illuminate\Contracts\View\View
     */
    public function completedList()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // ユーザーが完了した取引記録を取得
        $trades = RentalRecord::where('USER_ID', $user->USER_ID)
            ->with(['land', 'review'])
            ->get();

        return view('trade_list', ['trades' => $trades]);
    }

    private function formatRental(RentalRecord $rental): array
    {
        return [
            'id' => $rental->RECORD_ID,
            'price' => $rental->PRICE,
            'price_unit' => $rental->PRICE_UNIT,
            'rental_start_date' => $rental->RENTAL_START_DATE,
            'rental_end_date' => $rental->RENTAL_END_DATE,
            'rental_start_time' => $rental->RENTAL_START_TIME,
            'rental_end_time' => $rental->RENTAL_END_TIME,
            'total_amount' => $this->calculateTotalAmount($rental),
            'land' => $rental->land ? [
                'id' => $rental->land->LAND_ID,
                'name' => $rental->land->NAME,
                'prefecture' => $rental->land->PEREFECTURES,
                'city' => $rental->land->CITY,
                'street_address' => $rental->land->STREET_ADDRESS,
                'image' => $rental->land->IMAGE,
                'price' => $rental->land->PRICE,
                'price_unit' => $rental->land->PRICE_UNIT,
            ] : null,
            'owner' => $rental->land && $rental->land->owner
                ? new MemberResource($rental->land->owner)
                : null,
        ];
    }
}