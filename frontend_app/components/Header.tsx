"use client";

import Link from 'next/link';
import { useRouter } from 'next/navigation';
import useAuth from '../hooks/useAuth';

export default function Header() {
  const router = useRouter();
  const { user, loading, logout } = useAuth();

  async function handleLogout() {
    try {
      await logout();
      router.push('/');
    } catch (e) {
      // ignore — hook exposes error state for UI if needed
    }
  }

  return (
    <header className="w-full bg-white shadow">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex justify-between h-16 items-center">
          <div className="flex items-center">
            <Link href="/" className="text-xl font-bold text-blue-600">SukimaPark</Link>
          </div>

          <nav className="flex items-center space-x-4">
            <Link href="/" className="text-gray-700 hover:text-gray-900">トップ</Link>
            <Link href="/search" className="text-gray-700 hover:text-gray-900">検索</Link>

            {!user && (
              <>
                <Link href="/login" className="px-3 py-1 text-sm text-blue-600 border border-blue-600 rounded hover:bg-blue-50">ログイン</Link>
                <Link href="/register" className="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">会員登録</Link>
              </>
            )}

            {user && (
              <>
                <Link href="/mypage" className="text-gray-700 hover:text-gray-900">マイページ</Link>
                <button onClick={handleLogout} disabled={loading} className="px-3 py-1 text-sm bg-red-600 text-white rounded hover:bg-red-700 disabled:opacity-60">ログアウト</button>
              </>
            )}
          </nav>
        </div>
      </div>
    </header>
  );
}
