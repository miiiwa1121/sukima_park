<?php
/**
 * ============================================================
 * プロフィールコントローラー (ProfileController)
 * ============================================================
 * 
 * 【このコントローラーの役割】
 * プロフィール編集に関する処理を担当します。
 * 
 * 【処理の流れ】
 * 1. プロフィール編集画面表示: edit()
 * 2. 編集内容の一時保存と確認画面遷移: update()
 * 3. 確認画面表示: confirm()
 * 4. DB保存とマイページ遷移: store()
 * 
 * 【セッション管理】
 * - profile_edit: 編集データを一時保存
 * - 確認画面で内容を表示後、DB保存時にクリア
 * 
 * ============================================================
 */

namespace App\Http\Controllers;

use App\Http\Resources\MemberResource;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    /**
     * 許可する画像拡張子リスト
     */
    private $allowedExtensions = ['jpeg', 'jpg', 'png', 'heic'];

    /**
     * プロフィール編集画面を表示
     * 
     * 【URL】GET /prof_custom
     * 【ルート名】prof_custom
     * 【ビュー】resources/views/profile_edit_screen.blade.php
     *
     * @return \Illuminate\View\View プロフィール編集画面
     */
    public function edit()
    {
        $user = Auth::user();
        return view('profile_edit_screen', compact('user'));
    }

    /**
     * プロフィール編集内容の一時保存と確認画面への遷移
     * 
     * 【URL】POST /prof_custom
     * 【ルート名】prof_custom.update
     * 
     * 【処理の流れ】
     * 1. バリデーション（必須チェック、形式チェック）
     * 2. 画像ファイルがあれば一時保存
     * 3. 入力データをセッションに格納
     * 4. 確認画面にリダイレクト
     *
     * @param  \Illuminate\Http\Request  $request  HTTPリクエスト
     * @return \Illuminate\Http\RedirectResponse リダイレクトレスポンス
     */
    public function update(Request $request)
    {
        // ============================================================
        // 1. バリデーション（入力値チェック）
        // ============================================================
        $request->validate([
            'username' => 'required|string|max:32',           // ユーザー名: 必須、32文字以内
            'birth' => 'required|date',                       // 生年月日: 必須
            'show_birth' => 'required|boolean',               // 生年月日公開設定: 必須
            'gender' => 'required|integer|in:0,1,2',          // 性別: 必須、0/1/2
            'show_gender' => 'required|boolean',              // 性別公開設定: 必須
            'self_introduction' => 'nullable|string|max:140', // 自己紹介: 任意、140文字以内
            'password' => 'nullable|string|min:8|max:20|regex:/^(?=.*[a-zA-Z])(?=.*[0-9])[a-zA-Z0-9]+$/', // パスワード: 任意、英数混合8-20文字
            'icon_image' => 'nullable|file|max:5120',         // アイコン画像: 任意、5MB以下
        ]);

        // ============================================================
        // 2. アイコン画像の処理
        // ============================================================
        $iconImagePath = null;
        $iconImagePreview = null;
        
        if ($request->hasFile('icon_image')) {
            $file = $request->file('icon_image');
            $originalExtension = strtolower($file->getClientOriginalExtension());

            // 拡張子チェック
            if (!in_array($originalExtension, $this->allowedExtensions)) {
                return back()->withErrors([
                    'icon_image' => '許可されていないファイル形式です。jpeg, jpg, png, heicのみアップロード可能です。',
                ])->withInput();
            }

            // ユニークなファイル名を生成
            $fileName = uniqid('icon_') . '_' . time();

            // HEIC形式の場合はJPGに変換
            if ($originalExtension === 'heic') {
                $iconImagePath = $this->convertHeicToJpg($file, $fileName, 'temp_icons');
            } else {
                // その他の形式はそのまま一時保存（拡張子は小文字に統一）
                $newExtension = ($originalExtension === 'jpeg') ? 'jpg' : $originalExtension;
                $iconImagePath = $file->storeAs('temp_icons', $fileName . '.' . $newExtension, 'public');
            }

            // プレビュー用のURL生成
            $iconImagePreview = asset('storage/' . $iconImagePath);
        }

        // ============================================================
        // 3. 入力された全ての項目をセッションに格納
        // ============================================================
        $sessionData = [
            'email' => Auth::user()->EMAIL,  // メールアドレスは変更不可なので現在値
            'tel' => Auth::user()->TEL,      // 電話番号も変更不可なので現在値
            'username' => $request->username,
            'birth' => $request->birth,
            'show_birth' => $request->show_birth,
            'gender' => $request->gender,
            'show_gender' => $request->show_gender,
            'self_introduction' => $request->self_introduction,
        ];

        // パスワードが入力されている場合のみセッションに保存
        if ($request->filled('password')) {
            $sessionData['password'] = $request->password;
        }

        // アイコン画像が新しくアップロードされた場合
        if ($iconImagePath) {
            $sessionData['icon_image_path'] = $iconImagePath;
            $sessionData['icon_image_preview'] = $iconImagePreview;
        }

        $request->session()->put('profile_edit', $sessionData);

        // ============================================================
        // 4. プロフィール確認画面に遷移
        // ============================================================
        return redirect()->route('prof_check');
    }

    /**
     * プロフィール確認画面を表示
     * 
     * 【URL】GET /prof_check
     * 【ルート名】prof_check
     * 【ビュー】resources/views/profile_comfirmation_screen.blade.php
     *
     * @param  \Illuminate\Http\Request  $request  HTTPリクエスト
     * @return \Illuminate\View\View 確認画面
     */
    public function confirm(Request $request)
    {
        // セッションからデータを取得
        $profileData = $request->session()->get('profile_edit');

        // セッションにデータがない場合は編集画面に戻す
        if (!$profileData) {
            return redirect()->route('prof_custom')->withErrors([
                'session' => '入力データがありません。もう一度入力してください。',
            ]);
        }

        return view('profile_comfirmation_screen', ['profileData' => $profileData]);
    }

    /**
     * プロフィールを実際にデータベースに保存
     * 
     * 【URL】POST /prof_check
     * 【ルート名】prof_check.store
     *
     * @param  \Illuminate\Http\Request  $request  HTTPリクエスト
     * @return \Illuminate\Http\RedirectResponse リダイレクトレスポンス
     */
    public function store(Request $request)
    {
        // セッションからデータを取得
        $profileData = $request->session()->get('profile_edit');

        // セッションにデータがない場合は編集画面に戻す
        if (!$profileData) {
            return redirect()->route('prof_custom')->withErrors([
                'session' => '入力データがありません。もう一度入力してください。',
            ]);
        }

        // ============================================================
        // ログインユーザーの情報を取得
        // ============================================================
        $user = Auth::user();

        // ============================================================
        // 更新するデータを準備
        // ============================================================
        $updateData = [
            'USERNAME' => $profileData['username'],
            'BIRTH' => $profileData['birth'],
            'SHOW_BIRTH' => $profileData['show_birth'],
            'GENDER' => $profileData['gender'],
            'SHOW_GENDER' => $profileData['show_gender'],
            'SELF_INTRODUCTION' => $profileData['self_introduction'],
        ];

        // パスワードが変更されている場合のみ更新
        if (isset($profileData['password']) && !empty($profileData['password'])) {
            $updateData['PASSWORD'] = Hash::make($profileData['password']);
        }

        // アイコン画像が新しくアップロードされた場合
        if (isset($profileData['icon_image_path'])) {
            // 古いアイコン画像を削除（デフォルトアイコンでない場合）
            if ($user->ICON_IMAGE && $user->ICON_IMAGE !== 'default_icon.png') {
                Storage::disk('public')->delete($user->ICON_IMAGE);
            }

            // temp_iconsから本番ディレクトリ(icons)に移動
            $tempPath = $profileData['icon_image_path'];
            $fileName = basename($tempPath);
            $finalPath = 'icons/' . $fileName;

            // ディレクトリが存在しない場合は作成
            if (!Storage::disk('public')->exists('icons')) {
                Storage::disk('public')->makeDirectory('icons');
            }

            // ファイルを移動
            Storage::disk('public')->move($tempPath, $finalPath);

            $updateData['ICON_IMAGE'] = $finalPath;
        }

        // ============================================================
        // データベースを更新
        // ============================================================
        $user->update($updateData);

        // セッションから編集データを削除
        $request->session()->forget('profile_edit');

        // ============================================================
        // 完了後にマイページにリダイレクト
        // ============================================================
        return redirect()->route('mypage')->with('success', 'プロフィールを更新しました！');
    }

    /**
     * API: プロフィール編集用データ取得
     */
    public function editApi()
    {
        $user = Auth::user();

        return response()->json($this->profileResponse($user));
    }

    /**
     * API: プロフィール更新
     */
    public function updateApi(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:32',
            'birth' => 'required|date',
            'show_birth' => 'required|boolean',
            'gender' => 'required|integer|in:0,1,2',
            'show_gender' => 'required|boolean',
            'self_introduction' => 'nullable|string|max:140',
            'password' => 'nullable|string|min:8|max:20|regex:/^(?=.*[a-zA-Z])(?=.*[0-9])[a-zA-Z0-9]+$/',
            'icon_image' => 'nullable|file|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'バリデーションエラーが発生しました。',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        $user = Auth::user();

        $updateData = [
            'USERNAME' => $data['username'],
            'BIRTH' => $data['birth'],
            'SHOW_BIRTH' => $data['show_birth'],
            'GENDER' => $data['gender'],
            'SHOW_GENDER' => $data['show_gender'],
            'SELF_INTRODUCTION' => $data['self_introduction'] ?? null,
        ];

        if (!empty($data['password'] ?? null)) {
            $updateData['PASSWORD'] = Hash::make($data['password']);
        }

        if ($request->hasFile('icon_image')) {
            $file = $request->file('icon_image');
            $originalExtension = strtolower($file->getClientOriginalExtension());

            if (!in_array($originalExtension, $this->allowedExtensions)) {
                return response()->json([
                    'message' => '許可されていないファイル形式です。jpeg, jpg, png, heicのみアップロード可能です。',
                    'errors' => ['icon_image' => ['許可されていないファイル形式です。']],
                ], 422);
            }

            $fileName = uniqid('icon_') . '_' . time();

            if ($originalExtension === 'heic') {
                $iconImagePath = $this->convertHeicToJpg($file, $fileName, 'icons');
            } else {
                $newExtension = ($originalExtension === 'jpeg') ? 'jpg' : $originalExtension;
                if (!Storage::disk('public')->exists('icons')) {
                    Storage::disk('public')->makeDirectory('icons');
                }
                $iconImagePath = $file->storeAs('icons', $fileName . '.' . $newExtension, 'public');
            }

            if ($user->ICON_IMAGE && $user->ICON_IMAGE !== 'default_icon.png') {
                Storage::disk('public')->delete($user->ICON_IMAGE);
            }

            $updateData['ICON_IMAGE'] = $iconImagePath;
        }

        $user->update($updateData);

        return response()->json($this->profileResponse($user->fresh()), 200);
    }

    private function profileResponse(Member $user): array
    {
        return [
            'user' => new MemberResource($user),
            'profile' => [
                'tel' => $user->TEL,
                'birth' => $user->BIRTH,
                'show_birth' => $user->SHOW_BIRTH,
                'gender' => $user->GENDER,
                'show_gender' => $user->SHOW_GENDER,
                'self_introduction' => $user->SELF_INTRODUCTION,
                'icon_image' => $user->ICON_IMAGE,
            ],
        ];
    }

    /**
     * HEIC形式の画像をJPGに変換
     * 
     * 【なぜ必要か】
     * iPhoneで撮影した写真はHEIC形式で保存されることがあります。
     * HEIC形式はウェブブラウザでの表示に対応していないため、
     * JPG形式に変換して保存します。
     *
     * @param \Illuminate\Http\UploadedFile $file アップロードされたファイル
     * @param string $fileName 保存するファイル名（拡張子なし）
     * @param string $folder 保存先フォルダ名
     * @return string 保存されたファイルの相対パス
     * @throws \Exception 変換に失敗した場合
     */
    private function convertHeicToJpg($file, $fileName, $folder)
    {
        // 保存先ディレクトリのパス
        $destinationPath = storage_path('app/public/' . $folder);
        
        // ディレクトリが存在しない場合は作成
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        // 出力ファイルの絶対パス
        $outputPath = $destinationPath . '/' . $fileName . '.jpg';

        // ============================================================
        // ImageMagickを使用してHEICをJPGに変換
        // ============================================================
        if (extension_loaded('imagick')) {
            // PHP拡張版ImageMagick（Imagick）を使用
            $imagick = new \Imagick();
            $imagick->readImage($file->getPathname());
            $imagick->setImageFormat('jpg');
            $imagick->setImageCompressionQuality(90);
            $imagick->writeImage($outputPath);
            $imagick->clear();
            $imagick->destroy();
        } else {
            // コマンドライン版ImageMagickを使用
            $command = sprintf(
                'convert %s -quality 90 %s',
                escapeshellarg($file->getPathname()),
                escapeshellarg($outputPath)
            );
            exec($command, $output, $returnCode);

            if ($returnCode !== 0) {
                throw new \Exception('HEIC形式の変換に失敗しました。ImageMagickがインストールされているか確認してください。');
            }
        }

        // 相対パスを返す（storage/app/public/からの相対パス）
        return $folder . '/' . $fileName . '.jpg';
    }
}