<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Resources\MemberResource;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        $member = Member::where('EMAIL', $credentials['email'])->first();

        if (! $member || ! Hash::check($credentials['password'], $member->PASSWORD)) {
            return response()->json([
                'message' => 'メールアドレスまたはパスワードが正しくありません。',
            ], 422);
        }

        if ($member->ACCOUNT_STATUS === 1) {
            return response()->json([
                'message' => 'このアカウントは凍結されています。',
            ], 403);
        }

        Auth::login($member, $credentials['remember'] ?? false);
        $request->session()->regenerate();

        return response()->json([
            'message' => 'ログインしました。',
            'user' => new MemberResource($request->user()),
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'ログアウトしました。',
        ]);
    }

    public function me(Request $request)
    {
        return response()->json([
            'user' => new MemberResource($request->user()),
        ]);
    }
}
