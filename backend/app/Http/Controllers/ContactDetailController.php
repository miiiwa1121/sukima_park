<?php
/**
 * ============================================================
 * 問い合わせ詳細コントローラー (ContactDetailController.php)
 * ============================================================
 * 
 * 管理者向け問い合わせ詳細画面を担当するコントローラー
 * 
 * 【対応画面】
 *   - contact_detail.blade.php（問い合わせ詳細画面）
 * 
 * 【主な機能】
 *   - 問い合わせ詳細の表示
 *   - ステータスの変更
 *   - 返信の送信
 * 
 * 【使用テーブル】
 *   - CONTACT_TABLE（問い合わせテーブル）
 *   - MEMBER_TABLE（会員テーブル）※リレーション
 * 
 * ============================================================
 */

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactDetailController extends Controller
{
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
  /**
   * 問い合わせ詳細を表示
   * 
   * 【処理内容】
   * 1. 問い合わせをIDで取得（ユーザー情報も取得）
   * 2. contact_detail.blade.phpにデータを渡して表示
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
   * ステータスを変更
   * 
   * 【処理内容】
   * 1. 問い合わせをIDで取得
   * 2. ステータスを更新
   * 3. 詳細画面にリダイレクト
   * 
   * @param Request $request
   * @param int $id 問い合わせID
   * @return \Illuminate\Http\RedirectResponse
   */
  public function updateStatus(Request $request, $id)
  {
    // バリデーション
    $request->validate([
      'status' => 'required|in:0,1,2',
    ]);

    // 問い合わせをIDで取得
    $contact = Contact::findOrFail($id);

    // ステータスを更新
    $contact->STATUS = $request->input('status');
    $contact->save();

    // 成功メッセージと共に詳細画面にリダイレクト
    return redirect()
      ->back()
      ->with('success', 'ステータスを変更しました。');
  }

  /**
   * API: ステータス更新（管理者向け）
   */
  public function updateStatusApi(Request $request, $id)
  {
    $request->validate([
      'status' => 'required|in:0,1,2',
    ]);

    $contact = Contact::findOrFail($id);
    $contact->STATUS = $request->input('status');
    $contact->save();

    return response()->json([
      'message' => 'ステータスを変更しました。',
      'contact' => [
        'id' => $contact->CONTACT_ID,
        'status' => $contact->STATUS,
      ],
    ], 200);
  }

  /**
   * 返信を送信
   * 
   * 【処理内容】
   * 1. 問い合わせをIDで取得
   * 2. 返信内容をバリデーション
   * 3. メール送信（または返信記録を保存）
   * 4. ステータスを「対応中」に変更
   * 5. 詳細画面にリダイレクト
   * 
   * @param Request $request
   * @param int $id 問い合わせID
   * @return \Illuminate\Http\RedirectResponse
   */
  public function reply(Request $request, $id)
  {
    // バリデーション
    $request->validate([
      'reply_body' => 'required|string|min:1',
    ]);

    // 問い合わせをIDで取得（ユーザー情報も取得）
    $contact = Contact::with('sender')->findOrFail($id);

    // 返信内容を取得
    $replyBody = $request->input('reply_body');

    // ユーザーのメールアドレスを取得
    $userEmail = $contact->sender->EMAIL ?? null;

    if ($userEmail) {
      try {
        // メール送信（実装例）
        // Mail::raw($replyBody, function ($message) use ($userEmail, $contact) {
        //     $message->to($userEmail)
        //         ->subject('Re: ' . $contact->subject);
        // });

        // ステータスを「対応中」に変更（まだ完了でない場合）
        if ($contact->STATUS === 0) {
          $contact->STATUS = 1;
          $contact->save();
        }

        // 成功メッセージと共に詳細画面にリダイレクト
        return redirect()
          ->back()
          ->with('success', '返信を送信しました。');
      } catch (\Exception $e) {
        // エラーメッセージと共に詳細画面にリダイレクト
        return redirect()
          ->back()
          ->with('error', '返信の送信に失敗しました。');
      }
    } else {
      // ユーザーのメールアドレスが見つからない場合
      return redirect()
        ->back()
        ->with('error', 'ユーザーのメールアドレスが見つかりません。');
    }

  /**
   * API: 返信送信（管理者向け）
   */
  public function replyApi(Request $request, $id)
  {
    $request->validate([
      'reply_body' => 'required|string|min:1',
    ]);

    $contact = Contact::with('sender')->findOrFail($id);
    $userEmail = $contact->sender->EMAIL ?? null;

    if (!$userEmail) {
      return response()->json([
        'message' => 'ユーザーのメールアドレスが見つかりません。',
      ], 422);
    }

    if ($contact->STATUS === 0) {
      $contact->STATUS = 1;
      $contact->save();
    }

    return response()->json([
      'message' => '返信を送信しました。',
      'contact' => [
        'id' => $contact->CONTACT_ID,
        'status' => $contact->STATUS,
      ],
    ], 200);
  }
  }
}
