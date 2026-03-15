<?php

namespace App\Http\Controllers;

use App\Http\Resources\MemberResource;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * ============================================================
 * 認証コントローラー (AuthController)
 * ============================================================
 * 
 * 【このコントローラーの役割】
 * ログイン、ログアウト、会員登録に関する処理を担当します。
 * 
 * 【処理の流れ】
 * 1. ログイン: showLoginForm() → login()
 * 2. 会員登録: showRegisterForm() → register()
 * 3. ログアウト: logout()
 * 
 * 【Laravelの認証機能について】
 * - Auth::login($member) → セッションにユーザー情報を保存
 * - Auth::logout() → セッションからユーザー情報を削除
 * - Auth::check() → ログイン済みかどうかを確認
 * 
 * ============================================================
 */
class AuthController extends Controller
{
    /**
     * ログインフォーム画面を表示
     * 
     * 【URL】GET /login
     * 【ルート名】login
     * 【ビュー】resources/views/auth/login.blade.php
     *
     * @return \Illuminate\View\View ログイン画面
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * ログイン処理を実行
     * 
     * 【URL】POST /login
     * 
     * 【処理の流れ】
     * 1. バリデーション（必須チェック、メール形式チェック）
     * 2. メールアドレスでユーザーを検索
     * 3. パスワードを照合（Hash::check）
     * 4. アカウント凍結チェック
     * 5. ログイン処理（Auth::login）
     * 6. ホーム画面にリダイレクト
     * 
     * 【remember（ログイン状態を保持する）について】
     * - $request->filled('remember') → チェックボックスがオンの場合true
     * - Auth::login($member, true) → Remember Meトークンを発行
     * - ブラウザを閉じても30日間ログイン状態が維持される
     *
     * @param  \Illuminate\Http\Request  $request  HTTPリクエスト
     * @return \Illuminate\Http\RedirectResponse リダイレクトレスポンス
     */
    public function login(Request $request)
    {
        // ============================================================
        // 1. バリデーション（入力値チェック）
        // ============================================================
        $request->validate([
            'email' => 'required|email',     // 必須、メール形式
            'password' => 'required',         // 必須
        ]);

        // ============================================================
        // 2. メールアドレスでユーザーを検索
        // ============================================================
        // MEMBER_TABLEのEMAILカラムで検索し、最初の1件を取得
        // 見つからない場合はnullが返される
        $member = Member::where('EMAIL', $request->email)->first();

        // ============================================================
        // 3. パスワードを照合
        // ============================================================
        // Hash::check(入力値, ハッシュ値) → 一致すればtrue
        // パスワードはHash::makeで保存されているため、同じ方法で照合
    if ($member && Hash::check($request->password, $member->PASSWORD)) {
            
            // ============================================================
            // 4. アカウントステータスのチェック
            // ============================================================
            // ACCOUNT_STATUS = 1 → 凍結されたアカウント
            if ($member->ACCOUNT_STATUS == 1) {
                return response()->json([
                    'message' => 'このアカウントは凍結されています。',
                ], 403);
            }

            // ============================================================
            // 5. ログイン処理
            // ============================================================
            // Auth::login(ユーザー, remember)
            // - 第1引数: ログインするユーザーモデル
            // - 第2引数: Remember Meを有効にするか（true/false）
            // 
            // $request->filled('remember') は、
            // フォームの「remember」チェックボックスがオンの場合にtrueを返す
            Auth::login($member, $request->filled('remember'));
            
            // ============================================================
            // 6. セッションIDの再生成（セキュリティ対策）
            // ============================================================
            // ログイン前後でセッションIDを変更することで、
            // セッション固定攻撃（Session Fixation）を防ぐ
            $request->session()->regenerate();

            // ============================================================
            // 7. リダイレクト（ユーザー種別で分岐）
            // ============================================================
            // ACCOUNT_STATUS == 2 は管理者
            return response()->json([
                'message' => 'ログインしました。',
                'user' => new MemberResource($member),
            ]);
        }

        // ============================================================
        // 認証失敗時の処理
        // ============================================================
        // withErrors: エラーメッセージをセッションに保存
        // onlyInput: 指定したフィールドの入力値だけを保持（パスワードは除外）
        return response()->json([
            'message' => 'メールアドレスまたはパスワードが正しくありません。',
        ], 422);
    }

    /**
     * 会員登録フォーム画面を表示
     * 
     * 【URL】GET /register
     * 【ルート名】register
     * 【ビュー】resources/views/auth/register.blade.php
     *
     * @return \Illuminate\View\View 会員登録画面
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * 会員登録処理を実行
     * 
     * 【URL】POST /register
     * 
     * 【処理の流れ】
     * 1. バリデーション（必須チェック、形式チェック、一意性チェック）
     * 2. 本人確認書類のアップロード処理
     * 3. 会員レコードの作成
     * 4. 自動ログイン
     * 5. ホーム画面にリダイレクト
     *
     * @param  \Illuminate\Http\Request  $request  HTTPリクエスト
     * @return \Illuminate\Http\RedirectResponse リダイレクトレスポンス
     */
    public function register(Request $request)
    {
        // ============================================================
        // 許可する画像拡張子リスト
        // ============================================================
        // 本人確認書類としてアップロード可能な拡張子
        $allowedExtensions = ['jpeg', 'jpg', 'png', 'heic'];

        // ============================================================
        // バリデーション（入力値チェック）
        // ============================================================
        $request->validate([
            'username' => 'required|string|max:255',  // ユーザー名: 必須、文字列、最大255文字
            'email' => 'required|email|unique:MEMBER_TABLE,EMAIL',  // メール: 必須、形式チェック、重複禁止
            'password' => [
                'required',                                 // 必須
                'string',                                  // 文字列
                'min:8',                                   // 最小8文字
                'max:20',                                  // 最大20文字
                'regex:/^(?=.*[a-zA-Z])(?=.*[0-9])[a-zA-Z0-9]+$/',  // 英字と数字を両方含む
                'confirmed',                               // password_confirmationと一致
            ],
            'tel' => 'nullable|string|max:20',        // 電話番号: 任意
            'birth' => 'nullable|date',                // 生年月日: 任意
            'gender' => 'nullable|integer|in:0,1,2',  // 性別: 任意、0/1/2のいずれか
            'identification' => 'required|file|max:5120',  // 本人確認書類: 必須、5MB以下
        ]);

        // ============================================================
        // 本人確認書類の処理
        // ============================================================
        $identityPath = null;
        if ($request->hasFile('identification')) {
            $file = $request->file('identification');
            $originalExtension = strtolower($file->getClientOriginalExtension());

            // 拡張子チェック（許可されていない形式はエラー）
            if (!in_array($originalExtension, $allowedExtensions)) {
                return back()->withErrors([
                    'identification' => '許可されていないファイル形式です。jpeg, jpg, png, heicのみアップロード可能です。',
                ])->withInput();
            }

            // ユニークなファイル名を生成（重複防止）
            // uniqid: ユニークなID、time: タイムスタンプ
            $fileName = uniqid('identity_') . '_' . time();

            // HEIC形式の場合はJPGに変換（iPhoneの写真形式対応）
            if ($originalExtension === 'heic') {
                $identityPath = $this->convertHeicToJpg($file, $fileName);
            } else {
                // その他の形式はそのまま保存（拡張子は小文字に統一）
                $newExtension = ($originalExtension === 'jpeg') ? 'jpg' : $originalExtension;
                // storage/app/public/identifications/ に保存
                $identityPath = $file->storeAs('identifications', $fileName . '.' . $newExtension, 'public');
            }
        }

        // ============================================================
        // 会員レコードの作成
        // ============================================================
        // Model::create() は $fillableで許可されたカラムのみ一括代入
        $member = Member::create([
            'USERNAME' => $request->username,                   // ユーザー名
            'EMAIL' => $request->email,                         // メールアドレス
            'PASSWORD' => Hash::make($request->password),       // パスワード（ハッシュ化して保存）
            'TEL' => $request->tel,                             // 電話番号
            'BIRTH' => $request->birth,                         // 生年月日
            'GENDER' => $request->gender ?? 0,                  // 性別（デフォルト0）
            'SHOW_BIRTH' => false,                              // 生年月日公開（デフォルトfalse）
            'SHOW_GENDER' => false,                             // 性別公開（デフォルトfalse）
            'IDENTITY_IMAGE' => $identityPath,                  // 本人確認書類の画像パス
            'ICON_IMAGE' => 'default_icon.png',                 // デフォルトアイコン画像パス
            'ACCOUNT_STATUS' => 1,                              // アカウント状態（1=審査中）
        ]);

        // ============================================================
        // 登録後に自動ログイン
        // ============================================================
        // Remember Meなしでログイン（新規登録時はrememberなし）
        Auth::login($member);

        // ============================================================
        // ホーム画面にリダイレクト
        // ============================================================
        // with('success', 'メッセージ') でフラッシュメッセージを設定
        return response()->json([
            'message' => '会員登録が完了しました。',
            'user' => new MemberResource($member),
        ], 201);
    }

    /**
     * HEIC形式の画像をJPGに変換
     * 
     * 【なぜ必要か】
     * iPhoneで撮影した写真はHEIC形式で保存されることがあります。
     * HEIC形式はウェブブラウザでの表示に対応していないため、
     * JPG形式に変換して保存します。
     * 
     * 【変換方法】
     * 1. ImageMagickのPHP拡張（Imagick）がある場合はそれを使用
     * 2. なければコマンドラインのImageMagickを使用
     *
     * @param \Illuminate\Http\UploadedFile $file アップロードされたファイル
     * @param string $fileName 保存するファイル名（拡張子なし）
     * @return string 保存されたファイルの相対パス
     * @throws \Exception 変換に失敗した場合
     */
    private function convertHeicToJpg($file, $fileName)
    {
        // 保存先ディレクトリのパス
        $destinationPath = storage_path('app/public/identifications');
        
        // ディレクトリが存在しない場合は作成
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);  // パーミッション755で作成
        }

        // 出力ファイルの絶対パス
        $outputPath = $destinationPath . '/' . $fileName . '.jpg';

        // ============================================================
        // ImageMagickを使用してHEICをJPGに変換
        // ============================================================
        if (extension_loaded('imagick')) {
            // PHP拡張版ImageMagick（Imagick）を使用
            $imagick = new \Imagick();
            $imagick->readImage($file->getPathname());    // ファイルを読み込み
            $imagick->setImageFormat('jpg');               // 出力形式をJPGに設定
            $imagick->setImageCompressionQuality(85);      // 圧縮品質（0-100）
            $imagick->writeImage($outputPath);             // ファイルを書き出し
            $imagick->destroy();                           // メモリを解放
        } else {
            // コマンドライン版ImageMagickを使用
            $inputPath = $file->getPathname();
            $command = "magick convert \"{$inputPath}\" \"{$outputPath}\"";
            exec($command, $output, $returnCode);

            // 変換に失敗した場合はエラー
            if ($returnCode !== 0) {
                throw new \Exception('HEIC画像の変換に失敗しました。ImageMagickがインストールされているか確認してください。');
            }
        }

        // 相対パスを返す（storage/app/publicからの相対パス）
        return 'identifications/' . $fileName . '.jpg';
    }

    /**
     * ログアウト処理を実行
     * 
     * 【URL】POST /logout
     * 【ルート名】logout
     * 
     * 【処理の流れ】
     * 1. Auth::logout() → セッションからユーザー情報を削除
     * 2. invalidate() → セッションを無効化（セッションIDを破棄）
     * 3. regenerateToken() → CSRFトークンを再生成
     * 4. ホーム画面にリダイレクト
     * 
     * 【セキュリティ対策】
     * - セッションの無効化とCSRFトークンの再生成により、
     *   セッションハイジャックやCSRF攻撃を防止
     *
     * @param  \Illuminate\Http\Request  $request  HTTPリクエスト
     * @return \Illuminate\Http\RedirectResponse リダイレクトレスポンス
     */
    public function logout(Request $request)
    {
        // ============================================================
        // 1. ログアウト処理
        // ============================================================
        // セッションからユーザー情報を削除
        Auth::logout();

        // ============================================================
        // 2. セッションの無効化
        // ============================================================
        // 現在のセッションを完全に無効化し、新しいセッションIDを割り当てる
        $request->session()->invalidate();
        
        // ============================================================
        // 3. CSRFトークンの再生成
        // ============================================================
        // クロスサイトリクエストフォージェリ対策として、
        // ログアウト後に新しいトークンを生成
        $request->session()->regenerateToken();

        // ============================================================
        // 4. ホーム画面にリダイレクト
        // ============================================================
        return response()->json([
            'message' => 'ログアウトしました。',
        ]);
    }
}
