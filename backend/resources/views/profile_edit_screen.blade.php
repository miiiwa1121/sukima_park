@extends('layouts.app')

@section('title', 'プロフィール編集')

@push('styles')
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI",
          "Hiragino Sans", sans-serif;
        line-height: 1.6;
        color: #333;
        background: #fafafa;
    }

    .container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 0 20px;
    }

    header {
        background: #fff;
        border-bottom: 1px solid #e0e0e0;
        position: sticky;
        top: 0;
        z-index: 100;
    }

    .header-inner {
        display: flex;
        justify-content: space-between;
        align-items: center;
        height: 60px;
    }

    .logo {
        font-size: 18px;
        font-weight: 600;
        color: #2e7d32;
        text-decoration: none;
    }

    .header-nav {
        display: flex;
        gap: 12px;
        align-items: center;
    }

    .btn {
        padding: 8px 16px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    .btn-primary {
        background: #2e7d32;
        color: #fff;
    }

    .btn-primary:hover {
        background: #1b5e20;
    }

    .btn-secondary {
        background: #f5f5f5;
        color: #333;
    }

    .btn-secondary:hover {
        background: #e0e0e0;
    }

    .icon-btn {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #f5f5f5;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #666;
    }

    .icon-btn:hover {
        background: #e0e0e0;
    }

    .main-content {
        padding: 40px 0;
    }

    h1 {
        color: #2e7d32;
        text-align: center;
        margin-bottom: 30px;
        font-size: 28px;
    }

    h2 {
        color: #2e7d32;
        text-align: center;
        margin-bottom: 30px;
        font-size: 28px;
    }

    .profile-image-section {
        text-align: center;
        margin-bottom: 30px;
    }

    .profile-image-container {
        position: relative;
        display: inline-block;
    }

    .profile-image {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #4caf50;
        background: #e8f5e9;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 48px;
        color: #66bb6a;
    }

    .upload-btn {
        position: absolute;
        bottom: 0;
        right: 0;
        background: #4caf50;
        color: white;
        border: none;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        cursor: pointer;
        font-size: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        transition: background 0.3s;
    }

    .upload-btn:hover {
        background: #388e3c;
    }

    #imageInput {
        display: none;
    }

    .form-group {
        margin-bottom: 25px;
    }

    label {
        display: block;
        color: #2e7d32;
        font-weight: 600;
        margin-bottom: 8px;
        font-size: 14px;
    }

    input[type="text"],
    input[type="email"],
    input[type="tel"],
    input[type="password"],
    input[type="date"],
    select,
    textarea {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #c8e6c9;
        border-radius: 10px;
        font-size: 16px;
        transition: all 0.3s;
        background: #fafafa;
    }

    input:focus,
    select:focus,
    textarea:focus {
        outline: none;
        border-color: #4caf50;
        background: white;
        box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
    }

    input::placeholder {
        color: #a5d6a7;
    }

    .button-group {
        display: flex;
        gap: 15px;
        margin-top: 35px;
    }

    button[type="submit"],
    button[type="button"] {
        flex: 1;
        padding: 14px;
        border: none;
        border-radius: 10px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }

    button[type="submit"] {
        background: linear-gradient(135deg, #4caf50 0%, #66bb6a 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
    }

    button[type="submit"]:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(76, 175, 80, 0.4);
    }

    button[type="button"] {
        background: #e0e0e0;
        color: #666;
    }

    button[type="button"]:hover {
        background: #d0d0d0;
    }

    .file-name {
        margin-top: 10px;
        color: #66bb6a;
        font-size: 14px;
        text-align: center;
    }

    footer {
        background: #fff;
        border-top: 1px solid #e0e0e0;
        padding: 40px 0;
        margin-top: 60px;
    }

    .footer-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .footer-logo {
        font-size: 16px;
        font-weight: 600;
        color: #2e7d32;
    }

    .footer-links {
        display: flex;
        gap: 20px;
    }

    .footer-link {
        color: #666;
        text-decoration: none;
        font-size: 14px;
    }

    .footer-link:hover {
        color: #2e7d32;
        text-decoration: underline;
    }

    .footer-copyright {
        text-align: center;
        margin-top: 20px;
        font-size: 12px;
        color: #999;
    }

    @media (max-width: 768px) {
        .footer-content {
            flex-direction: column;
            gap: 16px;
        }
    }
</style>
@endpush

@section('content')
<main class="main-content">
    <div class="container">
        <h2>プロフィール編集</h2>
        
        <form id="profileForm" action="{{ route('prof_custom.update') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- アイコン画像 -->
            <div class="profile-image-section">
                <label style="text-align: center;">アイコン画像</label>
                <div class="profile-image-container">
                    <div class="profile-image" id="profileImg">
                        @if(isset($user) && $user->ICON_IMAGE)
                            <img src="{{ asset('storage/' . $user->ICON_IMAGE) }}" alt="プロフィール画像" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                        @else
                            👤
                        @endif
                    </div>
                    <button type="button" class="upload-btn" onclick="document.getElementById('imageInput').click()">📷</button>
                    <input type="file" id="imageInput" name="icon_image" accept="image/*">
                </div>
                <div class="file-name" id="fileName"></div>
                @error('icon_image')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <!-- 非公開情報 -->
            <h3 style="color: #2e7d32; margin-bottom: 20px; border-bottom: 2px solid #c8e6c9; padding-bottom: 10px;">非公開情報</h3>
            
            <div class="form-group">
                <label for="email">メールアドレス（変更不可）</label>
                <input type="email" id="email" name="email" 
                       value="{{ old('email', $user->EMAIL ?? '') }}" readonly 
                       style="background-color: #f5f5f5; cursor: not-allowed;">
                @error('email')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">パスワード（変更する場合のみ入力）</label>
                <input type="password" id="password" name="password" placeholder="変更しない場合は空欄のまま">
                @error('password')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="tel">電話番号（変更不可）</label>
                <input type="tel" id="tel" name="tel" 
                       value="{{ old('tel', $user->TEL ?? '') }}" readonly
                       style="background-color: #f5f5f5; cursor: not-allowed;">
                @error('tel')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <!-- 公開情報 -->
            <h3 style="color: #2e7d32; margin: 40px 0 20px; border-bottom: 2px solid #c8e6c9; padding-bottom: 10px;">公開情報</h3>

            <div class="form-group">
                <label for="birth">生年月日</label>
                <input type="date" id="birth" name="birth" 
                       value="{{ old('birth', $user->BIRTH ? $user->BIRTH->format('Y-m-d') : '') }}" required>
                @error('birth')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="showBirth">生年月日の公開設定</label>
                <select id="showBirth" name="show_birth" 
                        style="width: 100%; padding: 12px 16px; border: 2px solid #c8e6c9; border-radius: 10px; font-size: 16px; background: #fafafa;">
                    <option value="1" {{ old('show_birth', $user->SHOW_BIRTH ?? 0) == 1 ? 'selected' : '' }}>公開</option>
                    <option value="0" {{ old('show_birth', $user->SHOW_BIRTH ?? 0) == 0 ? 'selected' : '' }}>非公開</option>
                </select>
                @error('show_birth')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="gender">性別</label>
                <select id="gender" name="gender" 
                        style="width: 100%; padding: 12px 16px; border: 2px solid #c8e6c9; border-radius: 10px; font-size: 16px; background: #fafafa;" required>
                    <option value="0" {{ old('gender', $user->GENDER ?? 0) == 0 ? 'selected' : '' }}>未設定</option>
                    <option value="1" {{ old('gender', $user->GENDER ?? 0) == 1 ? 'selected' : '' }}>男性</option>
                    <option value="2" {{ old('gender', $user->GENDER ?? 0) == 2 ? 'selected' : '' }}>女性</option>
                </select>
                @error('gender')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="showGender">性別の公開設定</label>
                <select id="showGender" name="show_gender" 
                        style="width: 100%; padding: 12px 16px; border: 2px solid #c8e6c9; border-radius: 10px; font-size: 16px; background: #fafafa;">
                    <option value="1" {{ old('show_gender', $user->SHOW_GENDER ?? 0) == 1 ? 'selected' : '' }}>公開</option>
                    <option value="0" {{ old('show_gender', $user->SHOW_GENDER ?? 0) == 0 ? 'selected' : '' }}>非公開</option>
                </select>
                @error('show_gender')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="username">ユーザ名</label>
                <input type="text" id="username" name="username" placeholder="例: 山田 太郎" 
                       value="{{ old('username', $user->USERNAME ?? '') }}" required>
                @error('username')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="selfIntroduction">自己紹介</label>
                <textarea id="selfIntroduction" name="self_introduction" rows="5" placeholder="あなたについて教えてください（140字以内）" 
                          style="width: 100%; padding: 12px 16px; border: 2px solid #c8e6c9; border-radius: 10px; font-size: 16px; background: #fafafa; font-family: inherit; resize: vertical;">{{ old('self_introduction', $user->SELF_INTRODUCTION ?? '') }}</textarea>
                @error('self_introduction')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="button-group">
                <button type="button" onclick="if(confirm('編集内容を破棄してもよろしいですか？')) { window.location.href = '{{ route('mypage') }}'; }">キャンセル</button>
                <button type="submit">確認する</button>
            </div>
        </form>
    </div>
</main>

@push('scripts')
<script>
    const imageInput = document.getElementById('imageInput');
    const profileImg = document.getElementById('profileImg');
    const fileName = document.getElementById('fileName');

    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                profileImg.innerHTML = '';
                profileImg.style.backgroundImage = `url(${e.target.result})`;
                profileImg.style.backgroundSize = 'cover';
                profileImg.style.backgroundPosition = 'center';
            };
            reader.readAsDataURL(file);
            fileName.textContent = file.name;
        }
    });

    @if(session('success'))
        alert('{{ session('success') }}');
    @endif
</script>
@endpush
@endsection