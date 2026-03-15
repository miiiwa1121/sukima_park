"use client";

import { useEffect, useState } from 'react';
import { useRouter } from 'next/navigation';
import Link from 'next/link';
import useAuth from '../../hooks/useAuth';
import apiClient from '../../lib/axios';

interface RoomItem {
  id: number | string;
  partner?: { id: number | string; name?: string; icon_image?: string | null };
  last_message?: string | null;
  updated_at?: string | null;
}

export default function MessagesPage() {
  const router = useRouter();
  const { user, fetchUser } = useAuth();
  const [initialized, setInitialized] = useState(false);
  const [rooms, setRooms] = useState<RoomItem[]>([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

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
    if (!initialized) return;
    if (!user) {
      router.push('/login');
      return;
    }

    let mounted = true;
    async function load() {
      setLoading(true);
      setError(null);
      try {
        const res = await apiClient.get('/api/messages');
        const data = res.data?.data ?? res.data ?? [];
        const list = Array.isArray(data) ? data : data.data ?? [];
        if (mounted) setRooms(list);
      } catch (e: any) {
        setError(e?.response?.data?.message ?? e?.message ?? '取得に失敗しました');
      } finally {
        if (mounted) setLoading(false);
      }
    }

    load();
    return () => { mounted = false; };
  }, [initialized, user, router]);

  return (
    <div className="max-w-4xl mx-auto px-4 py-6">
      <h1 className="text-2xl font-semibold mb-4">メッセージ</h1>
      {loading && <div>読み込み中...</div>}
      {error && <div className="text-sm text-red-600 mb-4">{error}</div>}

      <div className="space-y-3">
        {rooms.length === 0 && !loading && <div className="text-sm text-gray-500">メッセージルームはまだありません</div>}

        {rooms.map((r) => (
          <Link key={r.id} href={`/messages/${r.id}`} className="block bg-white rounded shadow p-3 flex items-center space-x-3 hover:shadow-md">
            <div className="w-12 h-12 rounded-full bg-gray-100 overflow-hidden">
              {r.partner?.icon_image ? (
                // eslint-disable-next-line @next/next/no-img-element
                <img src={r.partner.icon_image} alt={r.partner?.name} className="w-full h-full object-cover" />
              ) : (
                <div className="w-full h-full flex items-center justify-center text-gray-500">N</div>
              )}
            </div>

            <div className="flex-1">
              <div className="flex justify-between items-center">
                <div className="font-medium text-gray-800">{r.partner?.name}</div>
                <div className="text-xs text-gray-400">{r.updated_at ? new Date(r.updated_at).toLocaleString() : ''}</div>
              </div>
              <div className="text-sm text-gray-600 truncate">{r.last_message ?? ''}</div>
            </div>
          </Link>
        ))}
      </div>
    </div>
  );
}
