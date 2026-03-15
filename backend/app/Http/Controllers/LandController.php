<?php

namespace App\Http\Controllers;

use App\Models\Land;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * ============================================================
 * 土地コントローラー (LandController)
 * ============================================================
 * 
 * 【このコントローラーの役割】
 * 土地の登録・編集・削除など、土地に関する処理を担当します。
 * 
 * 【処理の流れ】
 * 1. 土地登録: showRegisterForm() → register() → showConfirm() → store()
 * 2. 土地編集: showEditForm() → update()
 * 3. 土地削除: destroy()
 * 
 * 【認証について】
 * このコントローラーはログイン必須です。
 * ルートでauthミドルウェアを適用して制限します。
 * 
 * ============================================================
 */
class LandController extends Controller
{
    /**
     * 許可する画像拡張子リスト
     */
    private $allowedExtensions = ['jpeg', 'jpg', 'png', 'heic'];

    /**
     * 土地登録フォーム画面を表示
     * 
     * 【URL】GET /land/register
     * 【ルート名】land.register
     * 【ビュー】resources/views/land_register.blade.php
     *
     * @return \Illuminate\View\View 土地登録画面
     */
    public function showRegisterForm()
    {
        return view('land_register');
    }

    /**
     * 土地登録処理（確認画面への遷移）
     * 
     * 【URL】POST /land/register
     * 
     * 【処理の流れ】
     * 1. バリデーション（必須チェック、形式チェック）
     * 2. 拡張子チェック
     * 3. 画像ファイルのアップロード処理（HEIC変換含む）
     * 4. 入力データをセッションに格納
     * 5. 確認画面にリダイレクト
     *
     * @param  \Illuminate\Http\Request  $request  HTTPリクエスト
     * @return \Illuminate\Http\RedirectResponse リダイレクトレスポンス
     */
    public function register(Request $request)
    {
        // ============================================================
        // 1. バリデーション（入力値チェック）
        // ============================================================
        $request->validate([
            'name' => 'required|string|max:255',           // 土地名: 必須
            'prefectures' => 'required|integer|between:1,47', // 都道府県: 必須、1〜47
            'city' => 'required|string|max:255',           // 市区町村: 必須
            'street_address' => 'required|string|max:255', // 住所: 必須
            'area' => 'required|numeric|min:0.1',          // 面積: 必須、数値
            'price' => 'nullable|integer|min:0',           // 料金: 任意
            'description' => 'nullable|string|max:1000',   // 説明: 任意
            'title_deed' => 'required|file|max:5120',      // 権利書: 必須、5MB以下
            'image' => 'required|file|max:5120',           // 画像: 必須、5MB以下
        ]);

        // ============================================================
        // 土地の権利書の処理
        // ============================================================
        $titleDeedPath = null;
        if ($request->hasFile('title_deed')) {
            $file = $request->file('title_deed');
            $originalExtension = strtolower($file->getClientOriginalExtension());

            // 拡張子チェック
            if (!in_array($originalExtension, $this->allowedExtensions)) {
                return back()->withErrors([
                    'title_deed' => '許可されていないファイル形式です。jpeg, jpg, png, heicのみアップロード可能です。',
                ])->withInput();
            }

            // ユニークなファイル名を生成
            $fileName = uniqid('title_deed_') . '_' . time();

            // HEIC形式の場合はJPGに変換
            if ($originalExtension === 'heic') {
                $titleDeedPath = $this->convertHeicToJpg($file, $fileName, 'title_deeds');
            } else {
                // その他の形式はそのまま保存（拡張子は小文字に統一）
                $newExtension = ($originalExtension === 'jpeg') ? 'jpg' : $originalExtension;
                $titleDeedPath = $file->storeAs('title_deeds', $fileName . '.' . $newExtension, 'public');
            }
        }

        // ============================================================
        // 土地画像の処理
        // ============================================================
        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $originalExtension = strtolower($file->getClientOriginalExtension());

            // 拡張子チェック
            if (!in_array($originalExtension, $this->allowedExtensions)) {
                return back()->withErrors([
                    'image' => '許可されていないファイル形式です。jpeg, jpg, png, heicのみアップロード可能です。',
                ])->withInput();
            }

            // ユニークなファイル名を生成
            $fileName = uniqid('land_') . '_' . time();

            // HEIC形式の場合はJPGに変換
            if ($originalExtension === 'heic') {
                $imagePath = $this->convertHeicToJpg($file, $fileName, 'lands');
            } else {
                // その他の形式はそのまま保存（拡張子は小文字に統一）
                $newExtension = ($originalExtension === 'jpeg') ? 'jpg' : $originalExtension;
                $imagePath = $file->storeAs('lands', $fileName . '.' . $newExtension, 'public');
            }
        }

        // ============================================================
        // 2. 入力された全ての項目をセッションに格納
        // ============================================================
        $request->session()->put('land_register', [
            'name' => $request->name,
            'prefectures' => $request->prefectures,
            'city' => $request->city,
            'street_address' => $request->street_address,
            'area' => $request->area,
            'price' => $request->price,
            'description' => $request->description,
            'title_deed_path' => $titleDeedPath,
            'image_path' => $imagePath,
        ]);

        // ============================================================
        // 3. 土地登録確認画面に遷移
        // ============================================================
        return redirect()->route('land.register.confirm');
    }

    /**
     * 土地登録確認画面を表示
     * 
     * 【URL】GET /land/register/confirm
     * 【ルート名】land.register.confirm
     * 【ビュー】resources/views/land_register_confirm.blade.php
     *
     * @param  \Illuminate\Http\Request  $request  HTTPリクエスト
     * @return \Illuminate\View\View 確認画面
     */
    public function showConfirm(Request $request)
    {
        // セッションからデータを取得
        $landData = $request->session()->get('land_register');

        // セッションにデータがない場合は登録画面に戻す
        if (!$landData) {
            return redirect()->route('land.register')->withErrors([
                'session' => '入力データがありません。もう一度入力してください。',
            ]);
        }

        return view('land_register_confirm', ['land' => $landData]);
    }

    /**
     * 土地を実際にデータベースに登録
     * 
     * 【URL】POST /land/register/store
     * 【ルート名】land.register.store
     *
     * @param  \Illuminate\Http\Request  $request  HTTPリクエスト
     * @return \Illuminate\Http\RedirectResponse リダイレクトレスポンス
     */
    public function store(Request $request)
    {
        // セッションからデータを取得
        $landData = $request->session()->get('land_register');

        // セッションにデータがない場合は登録画面に戻す
        if (!$landData) {
            return redirect()->route('land.register')->withErrors([
                'session' => '入力データがありません。もう一度入力してください。',
            ]);
        }

        // ============================================================
        // 土地レコードの作成
        // ============================================================
        Land::create([
            'NAME' => $landData['name'],
            'PEREFECTURES' => $landData['prefectures'],
            'CITY' => $landData['city'],
            'STREET_ADDRESS' => $landData['street_address'],
            'AREA' => $landData['area'],
            'IMAGE' => $landData['image_path'],
            'TITLE_DEED' => $landData['title_deed_path'],
            'DESCRIPTION' => $landData['description'],
            'RENTAL_START_DATE' => null,  
            'RENTAL_END_DATE' => null,    
            'RENTAL_START_TIME' => null,  
            'RENTAL_END_TIME' => null,    
            'PRICE' => $landData['price'] ?? 0,
            'PRICE_UNIT' => 0,            // デフォルト: 日単位（0:日 1:時間 2:15分）
            'USER_ID' => Auth::id(),      // ログインユーザーのID
            'STATUS' => false,            // 初期状態: 非公開
        ]);

        // セッションから登録データを削除
        $request->session()->forget('land_register');

        // ============================================================
        // 完了後にリダイレクト
        // ============================================================
        return redirect()->route('home')->with('success', '土地を登録しました！');
    }

    /**
     * API: 土地登録のバリデーション（確認用）
     */
    public function confirmApi(Request $request)
    {
        $validated = $this->validateLandRequest($request);

        $uploadResult = $this->handleUploads($request);

        return response()->json([
            'data' => array_merge($validated, $uploadResult),
        ]);
    }

    /**
     * API: 土地を登録
     */
    public function storeApi(Request $request)
    {
        $validated = $this->validateLandRequest($request);
        $uploadResult = $this->handleUploads($request);

        $land = Land::create([
            'NAME' => $validated['name'],
            'PEREFECTURES' => $validated['prefectures'],
            'CITY' => $validated['city'],
            'STREET_ADDRESS' => $validated['street_address'],
            'AREA' => $validated['area'],
            'IMAGE' => $uploadResult['image_path'],
            'TITLE_DEED' => $uploadResult['title_deed_path'],
            'DESCRIPTION' => $validated['description'] ?? null,
            'RENTAL_START_DATE' => null,
            'RENTAL_END_DATE' => null,
            'RENTAL_START_TIME' => null,
            'RENTAL_END_TIME' => null,
            'PRICE' => $validated['price'] ?? 0,
            'PRICE_UNIT' => 0,
            'USER_ID' => Auth::id(),
            'STATUS' => false,
        ]);

        return response()->json([
            'message' => '土地を登録しました。',
            'land' => [
                'id' => $land->LAND_ID,
                'name' => $land->NAME,
                'prefecture' => $land->PEREFECTURES,
                'city' => $land->CITY,
                'street_address' => $land->STREET_ADDRESS,
                'area' => $land->AREA,
                'image' => $land->IMAGE,
                'title_deed' => $land->TITLE_DEED,
                'description' => $land->DESCRIPTION,
                'price' => $land->PRICE,
                'price_unit' => $land->PRICE_UNIT,
                'status' => $land->STATUS,
            ],
        ], 200);
    }

    private function validateLandRequest(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'prefectures' => 'required|integer|between:1,47',
            'city' => 'required|string|max:255',
            'street_address' => 'required|string|max:255',
            'area' => 'required|numeric|min:0.1',
            'price' => 'nullable|integer|min:0',
            'description' => 'nullable|string|max:1000',
            'title_deed' => 'required|file|max:5120',
            'image' => 'required|file|max:5120',
        ]);
    }

    private function handleUploads(Request $request): array
    {
        $titleDeedPath = null;
        if ($request->hasFile('title_deed')) {
            $file = $request->file('title_deed');
            $originalExtension = strtolower($file->getClientOriginalExtension());

            if (!in_array($originalExtension, $this->allowedExtensions)) {
                abort(422, '許可されていないファイル形式です。');
            }

            $fileName = uniqid('title_deed_') . '_' . time();

            if ($originalExtension === 'heic') {
                $titleDeedPath = $this->convertHeicToJpg($file, $fileName, 'title_deeds');
            } else {
                $newExtension = ($originalExtension === 'jpeg') ? 'jpg' : $originalExtension;
                $titleDeedPath = $file->storeAs('title_deeds', $fileName . '.' . $newExtension, 'public');
            }
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $originalExtension = strtolower($file->getClientOriginalExtension());

            if (!in_array($originalExtension, $this->allowedExtensions)) {
                abort(422, '許可されていないファイル形式です。');
            }

            $fileName = uniqid('land_') . '_' . time();

            if ($originalExtension === 'heic') {
                $imagePath = $this->convertHeicToJpg($file, $fileName, 'lands');
            } else {
                $newExtension = ($originalExtension === 'jpeg') ? 'jpg' : $originalExtension;
                $imagePath = $file->storeAs('lands', $fileName . '.' . $newExtension, 'public');
            }
        }

        return [
            'title_deed_path' => $titleDeedPath,
            'image_path' => $imagePath,
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

        // ImageMagickを使用してHEICをJPGに変換
        if (extension_loaded('imagick')) {
            // PHP拡張版ImageMagick（Imagick）を使用
            $imagick = new \Imagick();
            $imagick->readImage($file->getPathname());
            $imagick->setImageFormat('jpg');
            $imagick->setImageCompressionQuality(85);
            $imagick->writeImage($outputPath);
            $imagick->destroy();
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

        // 相対パスを返す
        return $folder . '/' . $fileName . '.jpg';
    }
}
