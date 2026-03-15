<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * ============================================================
 * 管理者認証ミドルウェア (AdminMiddleware)
 * ============================================================
 * 
 * ACCOUNT_STATUS = 2 のユーザー（管理者）のみアクセスを許可する
 * 
 * 【使用方法】
 * Route::middleware(['auth', 'admin'])->group(function () { ... });
 * 
 * ============================================================
 */
class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // ログイン済み かつ ACCOUNT_STATUS == 2（管理者）のみ許可
        if (Auth::check() && Auth::user()->ACCOUNT_STATUS == 2) {
            return $next($request);
        }
        
        // 管理者でない場合はホームへリダイレクト
        return redirect('/')->with('error', 'このページへのアクセス権限がありません。');
    }
}
