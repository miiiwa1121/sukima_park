<?php
/**
 * ============================================================
 * 検索一覧コントローラー (SearchListController)
 * ============================================================
 * 
 * 【このコントローラーの役割】
 * 土地の検索・絞り込み・並び替え・ページネーションを担当します。
 * 
 * 【処理の流れ】
 * 1. 検索フォームからのパラメータを取得
 * 2. 各種フィルタリング条件を適用
 * 3. 並び替え処理
 * 4. ページネーション（20件/ページ）
 * 5. 結果をビューに渡す
 * 
 * 【使用テーブル】
 *   - LAND_TABLE（土地テーブル）
 *   - REVIEW_COMMENT_TABLE（レビューテーブル、評価の集計用）
 * 
 * 【対応画面】
 *   - search_list.blade.php（検索結果一覧）
 * 
 * ============================================================
 */

namespace App\Http\Controllers;

use App\Models\Land;
use Illuminate\Http\Request;

class SearchListController extends Controller
{
    /**
     * API: 検索結果一覧を取得
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexApi(Request $request)
    {
        $lands = $this->buildSearchQuery($request)->paginate(20);

        return response()->json($lands);
    }

    /**
     * 検索結果一覧を表示
     * 
     * 【URL】GET /lands
     * 【ルート名】lands.index
     * 【ビュー】resources/views/search_list.blade.php
     * 
     * 【検索パラメータ】
     * - keyword: フリーワード検索（土地名、住所、説明文）
     * - fuzzy: あいまい検索フラグ（1の場合、LIKE検索を緩和）
     * - prefecture: 都道府県ID
     * - city: 市区町村名
     * - time_start: 利用開始時刻
     * - time_end: 利用終了時刻
     * - price_max: 料金上限
     * - area_min: 面積下限
     * - sort: 並び替え条件
     *
     * @param  \Illuminate\Http\Request  $request  HTTPリクエスト
     * @return \Illuminate\View\View 検索結果画面
     */
    public function index(Request $request)
    {
        // ============================================================
        // 1. クエリビルダーの初期化
        // ============================================================
        // 公開中の土地のみを対象（STATUS = true）
    $query = $this->buildSearchQuery($request);

        // ============================================================
        // 2. フリーワード検索
        // ============================================================
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;

            // あいまい検索: fuzzy=on または fuzzy=1 の場合に有効
            $isFuzzy = $request->fuzzy === 'on' || $request->fuzzy === '1' || $request->fuzzy === 1;

            if ($isFuzzy) {
                // あいまい検索: 各文字の間に%を挿入
                // 例: "駐車場" → "%駐%車%場%"
                $fuzzyKeyword = '%' . implode('%', mb_str_split($keyword)) . '%';
                $query->where(function ($q) use ($fuzzyKeyword) {
                    $q->where('NAME', 'LIKE', $fuzzyKeyword)
                        ->orWhere('CITY', 'LIKE', $fuzzyKeyword)
                        ->orWhere('STREET_ADDRESS', 'LIKE', $fuzzyKeyword)
                        ->orWhere('DESCRIPTION', 'LIKE', $fuzzyKeyword);
                });
            } else {
                // 通常検索: 部分一致
                $likeKeyword = '%' . $keyword . '%';
                $query->where(function ($q) use ($likeKeyword) {
                    $q->where('NAME', 'LIKE', $likeKeyword)
                        ->orWhere('CITY', 'LIKE', $likeKeyword)
                        ->orWhere('STREET_ADDRESS', 'LIKE', $likeKeyword)
                        ->orWhere('DESCRIPTION', 'LIKE', $likeKeyword);
                });
            }
        }

        // ============================================================
        // 3. 都道府県フィルター
        // ============================================================
        if ($request->filled('prefecture')) {
            $query->where('PEREFECTURES', $request->prefecture);
        }

        // ============================================================
        // 4. 市区町村フィルター
        // ============================================================
        if ($request->filled('city')) {
            $query->where('CITY', 'LIKE', '%' . $request->city . '%');
        }

        // ============================================================
        // 5. 利用日フィルター（use_date）
        // ============================================================
        // 指定された日付がレンタル可能期間内にある土地を検索
        if ($request->filled('use_date')) {
            $useDate = $request->use_date;
            $query->where(function ($q) use ($useDate) {
                $q->where(function ($sub) use ($useDate) {
                    // RENTAL_START_DATE と RENTAL_END_DATE の範囲内
                    $sub->where('RENTAL_START_DATE', '<=', $useDate)
                        ->where('RENTAL_END_DATE', '>=', $useDate);
                })->orWhere(function ($sub) {
                    // 日付が設定されていない土地も含める
                    $sub->whereNull('RENTAL_START_DATE')
                        ->whereNull('RENTAL_END_DATE');
                });
            });
        }

        // ============================================================
        // 6. 利用時間帯フィルター
        // ============================================================
        // ホームページ: start_time/end_time
        // 検索結果ページ: time_start/time_end
        // 両方のパラメータ名に対応
        $timeStart = $request->input('time_start') ?? $request->input('start_time');
        $timeEnd = $request->input('time_end') ?? $request->input('end_time');

        if ($timeStart) {
            $query->where(function ($q) use ($timeStart) {
                $q->whereNull('RENTAL_START_TIME')
                    ->orWhere('RENTAL_START_TIME', '<=', $timeStart);
            });
        }
        if ($timeEnd) {
            $query->where(function ($q) use ($timeEnd) {
                $q->whereNull('RENTAL_END_TIME')
                    ->orWhere('RENTAL_END_TIME', '>=', $timeEnd);
            });
        }

        // ============================================================
        // 7. 料金上限フィルター
        // ============================================================
        if ($request->filled('price_max')) {
            $query->where('PRICE', '<=', $request->price_max);
        }

        // ============================================================
        // 8. 料金単位フィルター（price_unit）
        // ============================================================
        // ホームページから送られてくる料金単位で絞り込み
        // 値: day → 0, hour → 1, 15min → 2
        if ($request->filled('price_unit')) {
            $priceUnitMap = [
                'day' => 0,
                'hour' => 1,
                '15min' => 2,
            ];
            $priceUnitValue = $priceUnitMap[$request->price_unit] ?? null;
            if ($priceUnitValue !== null) {
                $query->where('PRICE_UNIT', $priceUnitValue);
            }
        }

        // ============================================================
        // 9. 面積下限フィルター
        // ============================================================
        if ($request->filled('area_min')) {
            $query->where('AREA', '>=', $request->area_min);
        }

        // ============================================================
        // 8. 並び替え処理
        // ============================================================
        $sort = $request->get('sort', 'recommend');

        switch ($sort) {
            case 'rating_desc':
                // 評価（高い順）: レビューテーブルとJOINして平均評価で並び替え
                $query->leftJoin('REVIEW_COMMENT_TABLE', 'LAND_TABLE.LAND_ID', '=', 'REVIEW_COMMENT_TABLE.LAND_ID')
                    ->select('LAND_TABLE.*')
                    ->groupBy('LAND_TABLE.LAND_ID')
                    ->orderByRaw('AVG(REVIEW_COMMENT_TABLE.RATING) DESC NULLS LAST');
                break;
            case 'rating_asc':
                // 評価（低い順）
                $query->leftJoin('REVIEW_COMMENT_TABLE', 'LAND_TABLE.LAND_ID', '=', 'REVIEW_COMMENT_TABLE.LAND_ID')
                    ->select('LAND_TABLE.*')
                    ->groupBy('LAND_TABLE.LAND_ID')
                    ->orderByRaw('AVG(REVIEW_COMMENT_TABLE.RATING) ASC NULLS LAST');
                break;
            case 'price_asc':
                // 料金（安い順）
                $query->orderBy('PRICE', 'asc');
                break;
            case 'price_desc':
                // 料金（高い順）
                $query->orderBy('PRICE', 'desc');
                break;
            case 'area_desc':
                // 面積（広い順）
                $query->orderBy('AREA', 'desc');
                break;
            case 'area_asc':
                // 面積（狭い順）
                $query->orderBy('AREA', 'asc');
                break;
            case 'recommend':
            default:
                // おすすめ順: 新しい順（LAND_IDが大きいほど新しい）
                $query->orderBy('LAND_ID', 'desc');
                break;
        }

        // ============================================================
        // 9. ページネーション（20件/ページ）
        // ============================================================
    $lands = $query->paginate(20);

        // ============================================================
        // 10. ビューにデータを渡す
        // ============================================================
        return view('search_list', [
            'lands' => $lands,
        ]);
    }

    private function buildSearchQuery(Request $request)
    {
        // ============================================================
        // 1. クエリビルダーの初期化
        // ============================================================
        $query = Land::where('STATUS', true);

        // ============================================================
        // 2. フリーワード検索
        // ============================================================
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;

            $isFuzzy = $request->fuzzy === 'on' || $request->fuzzy === '1' || $request->fuzzy === 1;

            if ($isFuzzy) {
                $fuzzyKeyword = '%' . implode('%', mb_str_split($keyword)) . '%';
                $query->where(function ($q) use ($fuzzyKeyword) {
                    $q->where('NAME', 'LIKE', $fuzzyKeyword)
                        ->orWhere('CITY', 'LIKE', $fuzzyKeyword)
                        ->orWhere('STREET_ADDRESS', 'LIKE', $fuzzyKeyword)
                        ->orWhere('DESCRIPTION', 'LIKE', $fuzzyKeyword);
                });
            } else {
                $likeKeyword = '%' . $keyword . '%';
                $query->where(function ($q) use ($likeKeyword) {
                    $q->where('NAME', 'LIKE', $likeKeyword)
                        ->orWhere('CITY', 'LIKE', $likeKeyword)
                        ->orWhere('STREET_ADDRESS', 'LIKE', $likeKeyword)
                        ->orWhere('DESCRIPTION', 'LIKE', $likeKeyword);
                });
            }
        }

        // ============================================================
        // 3. 都道府県フィルター
        // ============================================================
        if ($request->filled('prefecture')) {
            $query->where('PEREFECTURES', $request->prefecture);
        }

        // ============================================================
        // 4. 市区町村フィルター
        // ============================================================
        if ($request->filled('city')) {
            $query->where('CITY', 'LIKE', '%' . $request->city . '%');
        }

        // ============================================================
        // 5. 利用日フィルター（use_date）
        // ============================================================
        if ($request->filled('use_date')) {
            $useDate = $request->use_date;
            $query->where(function ($q) use ($useDate) {
                $q->where(function ($sub) use ($useDate) {
                    $sub->where('RENTAL_START_DATE', '<=', $useDate)
                        ->where('RENTAL_END_DATE', '>=', $useDate);
                })->orWhere(function ($sub) {
                    $sub->whereNull('RENTAL_START_DATE')
                        ->whereNull('RENTAL_END_DATE');
                });
            });
        }

        // ============================================================
        // 6. 利用時間帯フィルター
        // ============================================================
        $timeStart = $request->input('time_start') ?? $request->input('start_time');
        $timeEnd = $request->input('time_end') ?? $request->input('end_time');

        if ($timeStart) {
            $query->where(function ($q) use ($timeStart) {
                $q->whereNull('RENTAL_START_TIME')
                    ->orWhere('RENTAL_START_TIME', '<=', $timeStart);
            });
        }
        if ($timeEnd) {
            $query->where(function ($q) use ($timeEnd) {
                $q->whereNull('RENTAL_END_TIME')
                    ->orWhere('RENTAL_END_TIME', '>=', $timeEnd);
            });
        }

        // ============================================================
        // 7. 料金上限フィルター
        // ============================================================
        if ($request->filled('price_max')) {
            $query->where('PRICE', '<=', $request->price_max);
        }

        // ============================================================
        // 8. 料金単位フィルター（price_unit）
        // ============================================================
        if ($request->filled('price_unit')) {
            $priceUnitMap = [
                'day' => 0,
                'hour' => 1,
                '15min' => 2,
            ];
            $priceUnitValue = $priceUnitMap[$request->price_unit] ?? null;
            if ($priceUnitValue !== null) {
                $query->where('PRICE_UNIT', $priceUnitValue);
            }
        }

        // ============================================================
        // 9. 面積下限フィルター
        // ============================================================
        if ($request->filled('area_min')) {
            $query->where('AREA', '>=', $request->area_min);
        }

        // ============================================================
        // 10. 並び替え処理
        // ============================================================
        $sort = $request->get('sort', 'recommend');

        switch ($sort) {
            case 'rating_desc':
                $query->leftJoin('REVIEW_COMMENT_TABLE', 'LAND_TABLE.LAND_ID', '=', 'REVIEW_COMMENT_TABLE.LAND_ID')
                    ->select('LAND_TABLE.*')
                    ->groupBy('LAND_TABLE.LAND_ID')
                    ->orderByRaw('AVG(REVIEW_COMMENT_TABLE.RATING) DESC NULLS LAST');
                break;
            case 'rating_asc':
                $query->leftJoin('REVIEW_COMMENT_TABLE', 'LAND_TABLE.LAND_ID', '=', 'REVIEW_COMMENT_TABLE.LAND_ID')
                    ->select('LAND_TABLE.*')
                    ->groupBy('LAND_TABLE.LAND_ID')
                    ->orderByRaw('AVG(REVIEW_COMMENT_TABLE.RATING) ASC NULLS LAST');
                break;
            case 'price_asc':
                $query->orderBy('PRICE', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('PRICE', 'desc');
                break;
            case 'area_desc':
                $query->orderBy('AREA', 'desc');
                break;
            case 'area_asc':
                $query->orderBy('AREA', 'asc');
                break;
            case 'recommend':
            default:
                $query->orderBy('LAND_ID', 'desc');
                break;
        }

        return $query;
    }

    /**
     * 土地詳細画面を表示
     * 
     * 【URL】GET /lands/{id}
     * 【ルート名】lands.show
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
        // 土地情報を取得（オーナーとレビュー情報も一緒に）
        $land = Land::with(['owner', 'reviews.reviewer'])
            ->where('LAND_ID', $id)
            ->firstOrFail();

        return view('land_detail', [
            'land' => $land,
        ]);
    }
}
