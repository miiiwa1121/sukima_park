<?php
/**
 * ============================================================
 * 問い合わせ一覧コントローラー (ContactListController.php)
 * ============================================================
 * 
 * 管理者向け問い合わせ一覧画面を担当するコントローラー
 * 
 * 【対応画面】
 *   - contact_list.blade.php（問い合わせ一覧画面）
 * 
 * 【主な機能】
 *   - 問い合わせ一覧の表示
 *   - キーワード検索（件名・内容）
 *   - ユーザーEメールによる絞り込み
 *   - ステータスによる絞り込み
 * 
 * 【使用テーブル】
 *   - CONTACT_TABLE（問い合わせテーブル）
 *     - CONTACT_ID: 主キー
 *     - TITLE: 件名
 *     - MESSAGE: 問い合わせ内容
 *     - USER_ID: 送信者のユーザーID
 *     - DATE: 問い合わせ日
 *     - STATUS: ステータス (0=新規, 1=対応中, 2=完了)
 *   - MEMBER_TABLE（会員テーブル）※リレーション
 * 
 * ============================================================
 */

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

class ContactListController extends Controller
{
  /**
   * API: 問い合わせ一覧（管理者向け）
   */
  public function indexApi(Request $request)
  {
    $query = Contact::with('sender');

    if ($keyword = $request->input('keyword')) {
      $query->where(function ($q) use ($keyword) {
        $q->where('TITLE', 'like', "%{$keyword}%")
          ->orWhere('MESSAGE', 'like', "%{$keyword}%");
      });
    }

    if ($userEmail = $request->input('user_email')) {
      $query->whereHas('sender', function ($q) use ($userEmail) {
        $q->where('EMAIL', 'like', "%{$userEmail}%");
      });
    }

    if ($status = $request->input('status')) {
      $statusMap = [
        'new' => 0,
        'open' => 1,
        'closed' => 2,
      ];
      if (isset($statusMap[$status])) {
        $query->where('STATUS', $statusMap[$status]);
      }
    }

    $contacts = $query->orderByDesc('DATE')->paginate(20);

    return response()->json($contacts);
  }
  /**
   * 問い合わせ一覧を表示（管理者向け）
   * 
   * 【処理内容】
   * 1. 検索キーワード（件名・内容）でフィルタリング
   * 2. ユーザーEメールでフィルタリング
   * 3. ステータスでフィルタリング
   * 4. ページネーション付きで一覧を取得
   * 5. contact_list.blade.phpにデータを渡して表示
   * 
   * 【取得データ】
   * $contacts: 問い合わせのページネーションコレクション
   *   - CONTACT_TABLEから取得
   *   - キーワード検索：TITLE または MESSAGE に部分一致
   *   - Eメール検索：関連するユーザーのEMAILで部分一致
   *   - ステータス検索：STATUS カラムでフィルタ
   * 
   * @param Request $request
   * @return \Illuminate\Contracts\View\View
   */
  public function index(Request $request)
  {
    // クエリビルダーを初期化（ユーザー情報もリレーションで取得）
    $query = Contact::with('sender');

    // キーワード検索（件名 or 内容）
    if ($keyword = $request->input('keyword')) {
      $query->where(function ($q) use ($keyword) {
        $q->where('TITLE', 'like', "%{$keyword}%")
          ->orWhere('MESSAGE', 'like', "%{$keyword}%");
      });
    }

    // ユーザーEメールでフィルタ
    if ($userEmail = $request->input('user_email')) {
      $query->whereHas('sender', function ($q) use ($userEmail) {
        $q->where('EMAIL', 'like', "%{$userEmail}%");
      });
    }

    // ステータスフィルタ（文字列を数値に変換）
    if ($status = $request->input('status')) {
      $statusMap = [
        'new' => 0,
        'open' => 1,
        'closed' => 2,
      ];
      if (isset($statusMap[$status])) {
        $query->where('STATUS', $statusMap[$status]);
      }
    }

    // 日付の降順でソート、ページネーション付きで取得
    $contacts = $query->orderByDesc('DATE')->paginate(20);

    // contact_list.blade.phpを表示し、データを渡す
    return view('contact_list', compact('contacts'));
  }

  /**
   * 問い合わせ詳細を表示
   * 
   * @param int $id 問い合わせID
   * @return \Illuminate\Contracts\View\View
   */
  public function show($id)
  {
    // 問い合わせをIDで取得（ユーザー情報も取得）
    $contact = Contact::with('sender')->findOrFail($id);

    // contact_detail.blade.phpを表示し、データを渡す
    return view('contact_detail', compact('contact'));
  }

  /**
   * API: 問い合わせ詳細（管理者向け）
   */
  public function showApi($id)
  {
    $contact = Contact::with('sender')->findOrFail($id);

    return response()->json([
      'contact' => [
        'id' => $contact->CONTACT_ID,
        'title' => $contact->TITLE,
        'message' => $contact->MESSAGE,
        'status' => $contact->STATUS,
        'date' => $contact->DATE,
        'sender' => $contact->sender ? [
          'id' => $contact->sender->USER_ID,
          'name' => $contact->sender->USERNAME,
          'email' => $contact->sender->EMAIL,
        ] : null,
      ],
    ]);
  }
}
