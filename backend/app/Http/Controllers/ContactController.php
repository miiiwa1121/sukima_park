<?php
/**
 * ============================================================
 * お問い合わせコントローラー (ContactController)
 * ============================================================
 * 
 * 【このコントローラーの役割】
 * お問い合わせの表示と送信処理を担当します。
 * 
 * 【処理の流れ】
 * 1. お問い合わせフォーム表示: showForm()
 * 2. お問い合わせ送信: store()
 * 
 * 【使用テーブル】
 *   - CONTACT_TABLE（お問い合わせテーブル）
 *   - MEMBER_TABLE（会員テーブル）
 * 
 * ============================================================
 */

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    /**
     * お問い合わせフォーム画面を表示
     * 
     * 【URL】GET /contact
     * 【ルート名】contact
     * 【ビュー】resources/views/contact.blade.php
     *
     * @return \Illuminate\View\View お問い合わせ画面
     */
    public function showForm()
    {
        return view('contact');
    }

    /**
     * お問い合わせ送信処理を実行
     * 
     * 【URL】POST /contact
     * 
     * 【処理の流れ】
     * 1. バリデーション（必須チェック、最大文字数チェック）
     * 2. お問い合わせレコードの作成
     * 3. 成功メッセージとともにフォームにリダイレクト
     * 
     * 【DBカラムとフォームの対応】
     * - TITLE ← subject（主題）
     * - MESSAGE ← body（問い合わせ内容）
     * - USER_ID ← ログイン中のユーザーID
     * - DATE ← 現在の日付
     * - STATUS ← 0（未対応）
     *
     * @param  \Illuminate\Http\Request  $request  HTTPリクエスト
     * @return \Illuminate\Http\RedirectResponse リダイレクトレスポンス
     */
    public function store(Request $request)
    {
        // ============================================================
        // 1. バリデーション（入力値チェック）
        // ============================================================
        $request->validate([
            'subject' => 'required|string|max:128',   // 主題: 必須、最大128文字
            'body' => 'required|string|max:1024',  // 問い合わせ内容: 必須、最大1024文字
        ]);

        // ============================================================
        // 2. ログインユーザーの取得
        // ============================================================
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // ============================================================
        // 3. お問い合わせレコードの作成
        // ============================================================
        // Model::create() は $fillableで許可されたカラムのみ一括代入
        Contact::create([
            'TITLE' => $request->subject,      // 主題
            'MESSAGE' => $request->body,         // 問い合わせ内容
            'USER_ID' => $user->USER_ID,         // ログイン中のユーザーID
            'DATE' => now()->toDateString(),  // 現在の日付
            'STATUS' => 0,                      // 未対応（デフォルト）
        ]);

        // ============================================================
        // 4. 成功メッセージとともにリダイレクト
        // ============================================================
        // with('success', 'メッセージ') でフラッシュメッセージを設定
        return redirect()->route('contact')
            ->with('success', 'お問い合わせを送信しました。ありがとうございます。');
    }

    /**
     * API: お問い合わせ送信（未ログイン可）
     */
    public function storeApi(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:128',
            'body' => 'required|string|max:1024',
            'user_id' => 'nullable|integer|exists:MEMBER_TABLE,USER_ID',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'バリデーションエラーが発生しました。',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = Auth::user();
        $payload = $validator->validated();
        $userId = $user ? $user->USER_ID : ($payload['user_id'] ?? null);

        if (!$userId) {
            return response()->json([
                'message' => 'user_id が必要です。',
                'errors' => ['user_id' => ['user_id を指定してください。']],
            ], 422);
        }

        $contact = Contact::create([
            'TITLE' => $payload['subject'],
            'MESSAGE' => $payload['body'],
            'USER_ID' => $userId,
            'DATE' => now()->toDateString(),
            'STATUS' => 0,
        ]);

        return response()->json([
            'message' => 'お問い合わせを送信しました。',
            'contact' => [
                'id' => $contact->CONTACT_ID,
                'title' => $contact->TITLE,
                'message' => $contact->MESSAGE,
                'status' => $contact->STATUS,
            ],
        ], 201);
    }
}
