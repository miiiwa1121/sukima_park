<?php
/**
 * ============================================================
 * ユーザー詳細コントローラー (UserDetailController.php)
 * ============================================================
 * 
 * 管理者向けユーザー詳細画面を担当するコントローラー
 * 
 * 【対応画面】
 *   - user_detail.blade.php（ユーザー詳細画面）
 * 
 * 【主な機能】
 *   - ユーザー詳細情報の表示
 *   - ステータスの更新
 *   - ユーザーの削除
 * 
 * 【使用テーブル】
 *   - MEMBER_TABLE（会員テーブル）
 * 
 * ============================================================
 */

namespace App\Http\Controllers;

use App\Http\Resources\MemberResource;
use App\Models\Land;
use App\Models\Member;
use Illuminate\Http\Request;

class UserDetailController extends Controller
{
  /**
   * ユーザー詳細を表示
   * 
   * 【処理内容】
   * 1. 指定されたIDのユーザーを取得
   * 2. user_detail.blade.phpにデータを渡して表示
   * 
   * 【取得データ】
   * $user: ユーザー情報（Memberモデル）
   *   - MEMBER_TABLEから取得
   * 
   * @param int $id ユーザーID
   * @return \Illuminate\Contracts\View\View
   */
  public function show($id)
  {
    // 指定IDのユーザーを取得（見つからない場合は404エラー）
    $user = Member::findOrFail($id);

    // user_detail.blade.phpを表示し、データを渡す
    return view('user_detail', compact('user'));
  }

  /**
   * API: 他ユーザーのプロフィール取得
   *
   * @param int $id
   * @return \Illuminate\Http\JsonResponse
   */
  public function showApi($id)
  {
    $user = Member::findOrFail($id);

    $publicLands = Land::where('USER_ID', $id)
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

  /**
   * ユーザーステータスを更新
   * 
   * 【処理内容】
   * 1. 指定されたIDのユーザーを取得
   * 2. ステータスを更新
   * 3. 成功メッセージと共にリダイレクト
   * 
   * @param Request $request
   * @param int $id ユーザーID
   * @return \Illuminate\Http\RedirectResponse
   */
  public function update(Request $request, $id)
  {
    // 指定IDのユーザーを取得
    $user = Member::findOrFail($id);

    // ステータスを更新
    // status: "0" = 有効, "1" = 利用停止中
    $status = $request->input('status');
    $user->ACCOUNT_STATUS = (int) $status;
    $user->save();

    // 成功メッセージと共に詳細画面にリダイレクト
    return redirect()
      ->back()
      ->with('success', 'ユーザー情報を更新しました。');
  }

  /**
   * ユーザーを削除
   * 
   * 【処理内容】
   * 1. 指定されたIDのユーザーを取得
   * 2. ユーザーを削除
   * 3. 成功メッセージと共に一覧画面にリダイレクト
   * 
   * @param int $id ユーザーID
   * @return \Illuminate\Http\RedirectResponse
   */
  public function destroy($id)
  {
    // 指定IDのユーザーを取得
    $user = Member::findOrFail($id);

    // ユーザーを削除
    $user->delete();

    // 成功メッセージと共に一覧画面にリダイレクト
    return redirect()
      ->url('/admin/users')
      ->with('success', 'ユーザーを削除しました。');
  }
}
