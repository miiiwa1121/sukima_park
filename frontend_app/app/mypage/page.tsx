"use client";

import { useEffect, useState } from 'react';
import { useRouter } from 'next/navigation';
import useAuth from '../../hooks/useAuth';
import Link from 'next/link';

export default function MyPage() {
  const router = useRouter();
  const { user, fetchUser, loading } = useAuth();
  const [initialized, setInitialized] = useState(false);

  useEffect(() => {
    let mounted = true;
    async function init() {
      await fetchUser();
      if (mounted) setInitialized(true);
    }
    init();
    return () => { mounted = false; };
  }, [fetchUser]);

  useEffect(() => {
    if (initialized && !loading && !user) {
      router.push('/login');
    }
  }, [initialized, loading, user, router]);

  if (!initialized || loading) return <div className="p-6">読み込み中...</div>;
  if (!user) return null; // redirecting

  return (
    <div className="max-w-4xl mx-auto px-4 py-6">
      <div className="bg-white rounded shadow p-6 mb-6">
        <div className="flex items-center space-x-4">
          <div className="w-16 h-16 rounded-full bg-gray-200 overflow-hidden">
            {user.icon_image ? (
              // eslint-disable-next-line @next/next/no-img-element
              <img src={user.icon_image} alt={user.name ?? 'icon'} className="w-full h-full object-cover" />
            ) : (
              <div className="w-full h-full flex items-center justify-center text-gray-500">N</div>
            )}
          </div>
          <div>
            <h2 className="text-xl font-semibold">{user.name ?? user.email}</h2>
            <div className="text-sm text-gray-600">{user.email}</div>
          </div>
        </div>
      </div>

      <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <Link href="/profile/edit" className="block p-4 bg-white rounded shadow hover:shadow-md">
          <h3 className="font-semibold">プロフィール編集</h3>
          <p className="text-sm text-gray-500">名前やプロフィール画像を変更します</p>
        </Link>

        <Link href="/lands/my" className="block p-4 bg-white rounded shadow hover:shadow-md">
          <h3 className="font-semibold">登録したスペース一覧</h3>
          <p className="text-sm text-gray-500">あなたが出品したスペースの管理</p>
        </Link>

        <Link href="/rental_list" className="block p-4 bg-white rounded shadow hover:shadow-md">
          <h3 className="font-semibold">予約履歴</h3>
          <p className="text-sm text-gray-500">これまでの予約・取引履歴を見る</p>
        </Link>

        <Link href="/messages" className="block p-4 bg-white rounded shadow hover:shadow-md">
          <h3 className="font-semibold">メッセージ</h3>
          <p className="text-sm text-gray-500">やりとり一覧に移動</p>
        </Link>
      </div>
    </div>
  );
}
