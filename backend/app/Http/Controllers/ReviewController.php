<?php

namespace App\Http\Controllers;

use App\Models\ReviewComment;
use App\Models\RentalRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * ============================================================
 * レビューコントローラー (ReviewController.php)
 * ============================================================
 * 
 * 【このコントローラーの役割】
 * - レビュー・コメントの投稿を処理
 * 
 * 【主な機能】
 *   - レビューの投稿
 *   - レビュー情報の保存
 * 
 * 【使用テーブル】
 *   - REVIEW_COMMENT_TABLE（レビュー・コメント）
 *   - RENTAL_RECORD_TABLE（貸出記録）
 * 
 * ============================================================
 */
class ReviewController extends Controller
{
    /**
     * API: レビュー投稿
     */
    public function storeApi(Request $request, $recordId)
    {
        $user = Auth::user();

        $rental = RentalRecord::where('RECORD_ID', $recordId)->first();

        if (!$rental) {
            return response()->json([
                'message' => '取引情報が見つかりません。',
            ], 404);
        }

        if ($rental->USER_ID !== $user->USER_ID) {
            return response()->json([
                'message' => 'この取引へのアクセス権がありません。',
            ], 403);
        }

        $validated = $request->validate([
            'land_rating' => 'required|integer|between:1,5',
            'land_comment' => 'nullable|string|max:500',
            'owner_rating' => 'required|integer|between:1,5',
            'owner_comment' => 'nullable|string|max:500',
        ], [
            'land_rating.required' => '土地の評価は必須です',
            'land_rating.between' => '土地の評価は1から5の間で選択してください',
            'owner_rating.required' => '貸し手の評価は必須です',
            'owner_rating.between' => '貸し手の評価は1から5の間で選択してください',
            'land_comment.max' => 'コメントは500文字以内です',
            'owner_comment.max' => 'コメントは500文字以内です',
        ]);

        $existingReview = ReviewComment::where('RECORD_ID', $recordId)->first();

        if ($existingReview) {
            $existingReview->update([
                'LAND_REVIEW' => $validated['land_rating'],
                'LAND_COMMENT' => $validated['land_comment'] ?? null,
                'USER_REVIEW' => $validated['owner_rating'],
                'USER_COMMENT' => $validated['owner_comment'] ?? null,
                'DATE' => now()->toDateString(),
            ]);
            $review = $existingReview;
            $status = 200;
        } else {
            $review = ReviewComment::create([
                'LAND_REVIEW' => $validated['land_rating'],
                'LAND_COMMENT' => $validated['land_comment'] ?? null,
                'USER_REVIEW' => $validated['owner_rating'],
                'USER_COMMENT' => $validated['owner_comment'] ?? null,
                'DATE' => now()->toDateString(),
                'USER_ID' => $user->USER_ID,
                'LAND_ID' => $rental->LAND_ID,
                'RECORD_ID' => $recordId,
            ]);
            $status = 201;
        }

        return response()->json([
            'message' => 'レビューを投稿しました。',
            'review' => [
                'id' => $review->REVIEW_COMMENT_ID,
                'record_id' => $review->RECORD_ID,
                'land_review' => $review->LAND_REVIEW,
                'land_comment' => $review->LAND_COMMENT,
                'user_review' => $review->USER_REVIEW,
                'user_comment' => $review->USER_COMMENT,
                'date' => $review->DATE,
            ],
        ], $status);
    }
    /**
     * レビュー投稿画面を表示
     * 
     * @param int $recordId 貸出記録ID
     * @return \Illuminate\Contracts\View\View
     */
    public function create($recordId)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // レンタル記録を取得（自分のレンタルのみ）
        $rental = RentalRecord::where('RECORD_ID', $recordId)
            ->where('USER_ID', $user->USER_ID)
            ->with(['land', 'land.owner'])
            ->firstOrFail();

        // 既存のレビューがあれば取得
        $existingReview = ReviewComment::where('RECORD_ID', $recordId)->first();

        return view('submit_review_screen', [
            'rental' => $rental,
            'existingReview' => $existingReview,
        ]);
    }

    /**
     * レビューを投稿
     * 
     * @param Request $request
     * @param int $recordId 貸出記録ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, $recordId)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // レンタル記録を取得（自分のレンタルのみ）
        $rental = RentalRecord::where('RECORD_ID', $recordId)
            ->where('USER_ID', $user->USER_ID)
            ->with('land')
            ->firstOrFail();

        // バリデーション（ビューのフォームフィールド名に合わせる）
        $validated = $request->validate([
            'land_rating' => 'required|integer|between:1,5',
            'land_comment' => 'nullable|string|max:500',
            'owner_rating' => 'required|integer|between:1,5',
            'owner_comment' => 'nullable|string|max:500',
        ], [
            'land_rating.required' => '土地の評価は必須です',
            'land_rating.between' => '土地の評価は1から5の間で選択してください',
            'owner_rating.required' => '貸し手の評価は必須です',
            'owner_rating.between' => '貸し手の評価は1から5の間で選択してください',
            'land_comment.max' => 'コメントは500文字以内です',
            'owner_comment.max' => 'コメントは500文字以内です',
        ]);

        // 既存のレビューをチェック
        $existingReview = ReviewComment::where('RECORD_ID', $recordId)->first();

        if ($existingReview) {
            // 既存のレビューを更新
            $existingReview->update([
                'LAND_REVIEW' => $validated['land_rating'],
                'LAND_COMMENT' => $validated['land_comment'] ?? null,
                'USER_REVIEW' => $validated['owner_rating'],
                'USER_COMMENT' => $validated['owner_comment'] ?? null,
                'DATE' => now()->toDateString(),
            ]);
        } else {
            // 新しいレビューを作成
            ReviewComment::create([
                'LAND_REVIEW' => $validated['land_rating'],
                'LAND_COMMENT' => $validated['land_comment'] ?? null,
                'USER_REVIEW' => $validated['owner_rating'],
                'USER_COMMENT' => $validated['owner_comment'] ?? null,
                'DATE' => now()->toDateString(),
                'USER_ID' => $user->USER_ID,
                'LAND_ID' => $rental->LAND_ID,
                'RECORD_ID' => $recordId,
            ]);
        }

        return redirect()
            ->route('trade.detail', $recordId)
            ->with('success', 'レビューを投稿しました');
    }
}
