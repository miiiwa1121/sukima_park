<?php

namespace App\Http\Controllers;

use App\Http\Resources\MemberResource;
use App\Models\RentalRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * ============================================================
 * 取引詳細コントローラー (TradeDetailController.php)
 * ============================================================
 * 
 * 【このコントローラーの役割】
 * - 完了した取引の詳細を表示
 * - レンタル記録とレビュー情報を取得
 * 
 * ============================================================
 */
class TradeDetailController extends Controller
{
    /**
     * API: 取引完了一覧
     */
    public function listApi()
    {
        $user = Auth::user();

        $trades = RentalRecord::with(['land.owner', 'review'])
            ->where('USER_ID', $user->USER_ID)
            ->get()
            ->map(fn (RentalRecord $rental) => $this->formatTrade($rental));

        return response()->json([
            'trades' => $trades,
        ]);
    }

    /**
     * API: 取引詳細
     */
    public function showApi($recordId)
    {
        $rental = RentalRecord::with(['land.owner', 'review'])
            ->where('RECORD_ID', $recordId)
            ->firstOrFail();

        if ($rental->USER_ID !== Auth::id()) {
            return response()->json([
                'message' => 'この取引へのアクセス権がありません。',
            ], 403);
        }

        return response()->json([
            'trade' => $this->formatTrade($rental),
        ]);
    }

    /**
     * 取引詳細を表示
     * 
     * @param int $recordId 貸出記録ID
     * @return \Illuminate\View\View
     */
    public function show($recordId)
    {
        // ログインユーザーが借りた記録を取得
        $rental = RentalRecord::with([
            'land.owner',  // 土地と土地のオーナー情報
            'review'       // レビュー情報
        ])
        ->where('RECORD_ID', $recordId)
        ->where('USER_ID', Auth::id())
        ->firstOrFail();

        // レビューデータを整形
        $reviews = collect();
        
        if ($rental->review) {
            $review = $rental->review;
            
            // 土地へのレビュー
            if ($review->LAND_REVIEW && $review->LAND_COMMENT) {
                $reviews->push((object)[
                    'reviewable_type' => 'land',
                    'rating' => $review->LAND_REVIEW,
                    'comment' => $review->LAND_COMMENT,
                    'created_at' => $review->DATE,
                ]);
            }
            
            // ユーザー（貸し手）へのレビュー
            if ($review->USER_REVIEW && $review->USER_COMMENT) {
                $reviews->push((object)[
                    'reviewable_type' => 'user',
                    'rating' => $review->USER_REVIEW,
                    'comment' => $review->USER_COMMENT,
                    'created_at' => $review->DATE,
                ]);
            }
        }

        // レンタル情報に必要なプロパティを追加
        $rental->start_date = $rental->RENTAL_START_DATE;
        $rental->end_date = $rental->RENTAL_END_DATE;
        $rental->total_amount = $this->calculateTotalAmount($rental);
        $rental->status_label = '取引完了';

        return view('trade_detail', compact('rental', 'reviews'));
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

    private function formatTrade(RentalRecord $rental): array
    {
        $reviews = [];

        if ($rental->review) {
            if ($rental->review->LAND_REVIEW && $rental->review->LAND_COMMENT) {
                $reviews[] = [
                    'reviewable_type' => 'land',
                    'rating' => $rental->review->LAND_REVIEW,
                    'comment' => $rental->review->LAND_COMMENT,
                    'created_at' => $rental->review->DATE,
                ];
            }

            if ($rental->review->USER_REVIEW && $rental->review->USER_COMMENT) {
                $reviews[] = [
                    'reviewable_type' => 'user',
                    'rating' => $rental->review->USER_REVIEW,
                    'comment' => $rental->review->USER_COMMENT,
                    'created_at' => $rental->review->DATE,
                ];
            }
        }

        return [
            'id' => $rental->RECORD_ID,
            'price' => $rental->PRICE,
            'price_unit' => $rental->PRICE_UNIT,
            'rental_start_date' => $rental->RENTAL_START_DATE,
            'rental_end_date' => $rental->RENTAL_END_DATE,
            'rental_start_time' => $rental->RENTAL_START_TIME,
            'rental_end_time' => $rental->RENTAL_END_TIME,
            'total_amount' => $this->calculateTotalAmount($rental),
            'status_label' => '取引完了',
            'land' => $rental->land ? [
                'id' => $rental->land->LAND_ID,
                'name' => $rental->land->NAME,
                'prefecture' => $rental->land->PEREFECTURES,
                'city' => $rental->land->CITY,
                'street_address' => $rental->land->STREET_ADDRESS,
                'image' => $rental->land->IMAGE,
            ] : null,
            'owner' => $rental->land && $rental->land->owner
                ? new MemberResource($rental->land->owner)
                : null,
            'reviews' => $reviews,
        ];
    }
}
