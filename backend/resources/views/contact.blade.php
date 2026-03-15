@extends('layouts.app')

@section('title', 'お問い合わせ')

@push('styles')
    <style>
        /* 問い合わせフォーム専用のスタイル */
        .title-wrapper {
            position: relative;
            margin-bottom: 24px;
        }

        .back-link {
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            font-size: 24px;
            font-weight: 700;
            color: #333;
            cursor: pointer;
            padding: 0;
            line-height: 1;
            text-decoration: none;
        }

        .back-link:hover {
            color: #2e7d32;
        }

        .page-title {
            font-size: 24px;
            font-weight: 700;
            color: #222;
            text-align: center;
        }

        .form-wrapper {
            background: #fff;
            border-radius: 8px;
            padding: 32px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            max-width: 700px;
            margin: 40px auto;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 6px;
            color: #555;
        }

        .form-control {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            font-family: inherit;
        }

        .form-control:focus {
            outline: none;
            border-color: #2e7d32;
        }

        .form-control.is-invalid {
            border-color: #dc3545;
        }

        textarea.form-control {
            min-height: 200px;
            resize: vertical;
        }

        .form-actions {
            display: flex;
            justify-content: center;
            margin-top: 24px;
        }

        .form-actions .btn {
            flex: none;
            padding: 12px 32px;
            font-size: 16px;
            font-weight: 600;
        }

        .invalid-feedback {
            color: #dc3545;
            font-size: 12px;
            margin-top: 4px;
        }

        .alert-validation {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .alert-validation ul {
            margin: 0;
            padding-left: 20px;
        }

        /* レスポンシブ対応 */
        @media (max-width: 768px) {
            .form-wrapper {
                padding: 24px;
                margin: 20px auto;
            }

            .form-actions {
                flex-direction: column;
            }

            .form-actions .btn {
                width: 100%;
                padding: 12px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="form-wrapper">
        <div class="title-wrapper">
            <a href="javascript:history.back()" class="back-link" title="戻る">&lt;</a>
            <h1 class="page-title">お問い合わせ</h1>
        </div>

        {{-- バリデーションエラーの表示 --}}
        @if ($errors->any())
            <div class="alert-validation">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('contact.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="subject">主題</label>
                <input type="text" id="subject" name="subject" class="form-control @error('subject') is-invalid @enderror"
                    placeholder="Hint：〇〇の土地について、不具合の報告" value="{{ old('subject') }}" required maxlength="128">
                @error('subject')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="body">問い合わせ内容</label>
                <textarea id="body" name="body" class="form-control @error('body') is-invalid @enderror"
                    placeholder="Hint：お問い合わせ内容を具体的にご記入ください。" required maxlength="1024">{{ old('body') }}</textarea>
                @error('body')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">入力内容の送信</button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        // 送信成功時のアラート表示
        @if (session('success'))
            alert('{{ session('success') }}');
        @endif
    </script>
@endpush