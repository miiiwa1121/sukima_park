<?php
/**
 * ============================================================
 * 土地貸出設定コントローラー (LandPublicController.php)
 * ============================================================
 * 
 * 【対応画面】
 *   land_public.blade.php（土地貸出設定）
 * 
 * 【画面定義】
 *   context/画面一覧/land_public.csv
 *   context/画面レイアウト/listed_lands_screen.html
 * 
 * 【このコントローラーの役割】
 *   土地情報の編集フォームを表示
 *   公開ステータスの切り替え（将来実装）
 * 
 * ============================================================
 */

namespace App\Http\Controllers;

use App\Models\Land;
use Illuminate\Support\Facades\Auth;

class LandPublicController extends Controller
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
     * 土地貸出設定フォームを表示
     * 
     * URL: /land_public/{id}
     * ルート名: land_public
     * 
     * @param int $id 土地ID
     * @return \Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        // 土地を取得
        $land = Land::findOrFail($id);

        // 自分の土地かチェック
        if ($land->USER_ID !== Auth::id()) {
            abort(403, 'この土地へのアクセス権がありません');
        }

        // ビューに渡す
        return view('land_public', [
            'land' => $land,
            'prefectures' => self::PREFECTURES,
        ]);
    }

    /**
     * 土地の公開ステータスを切り替え
     * 
     * URL: /land_public/{id}/toggle_status (POST)
     * ルート名: land_public.toggle_status
     * 
     * ステータス変更後のリダイレクト:
     * - 非公開→公開 (0→1): loan_detail画面へ
     * - 公開→非公開 (1→0): land_public画面へ(編集継続)
     * 
     * @param int $id 土地ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleStatus($id)
    {
        // 土地を取得
        $land = Land::findOrFail($id);

        // 自分の土地かチェック
        if ($land->USER_ID !== Auth::id()) {
            abort(403, 'この土地へのアクセス権がありません');
        }

        // ステータスを切り替え (0→1 または 1→0)
        $newStatus = $land->STATUS == 0 ? 1 : 0;
        $land->STATUS = $newStatus;
        $land->save();

        // ステータスに応じてリダイレクト先を変更
        if ($newStatus == 1) {
            // 非公開→公開: 貸出中詳細画面へ
            return redirect()->route('loan_detail', $land->LAND_ID)
                ->with('success', '土地を公開しました。募集を開始します。');
        } else {
            // 公開→非公開: 編集画面に戻る
            return redirect()->route('land_public', $land->LAND_ID)
                ->with('success', '土地を非公開にしました。');
        }
    }

    /**
     * API: 土地の公開ステータスを切り替え
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleStatusApi($id)
    {
        $land = Land::findOrFail($id);

        if ($land->USER_ID !== Auth::id()) {
            return response()->json([
                'message' => 'この土地へのアクセス権がありません。',
            ], 403);
        }

        $land->STATUS = $land->STATUS == 0 ? 1 : 0;
        $land->save();

        return response()->json([
            'message' => $land->STATUS ? '土地を公開しました。' : '土地を非公開にしました。',
            'land' => [
                'id' => $land->LAND_ID,
                'status' => $land->STATUS,
            ],
        ]);
    }
}

