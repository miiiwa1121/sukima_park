"use client";

import { useEffect, useState } from 'react';
import { useRouter } from 'next/navigation';
import Link from 'next/link';
import useAuth from '../../hooks/useAuth';
import apiClient from '../../lib/axios';

interface RentalItem {
  id: number | string;
  land?: { id: number | string; title?: string; thumbnail_url?: string | null };
  start_at?: string | null;
  end_at?: string | null;
  total_price?: number | null;
  status?: string | null;
}

function statusClass(status?: string) {
  switch ((status || '').toLowerCase()) {
    case 'pending':
    case '承認待ち':
      return 'bg-yellow-100 text-yellow-800';
    case 'confirmed':
    case '確定':
      return 'bg-green-100 text-green-800';
    case 'completed':
    case '完了':
      return 'bg-gray-100 text-gray-800';
    case 'cancelled':
    case 'キャンセル':
      return 'bg-red-100 text-red-800';
    default:
      return 'bg-blue-100 text-blue-800';
  }
}

export default function RentalsPage() {
  const router = useRouter();
  const { user, fetchUser } = useAuth();
  const [initialized, setInitialized] = useState(false);
  const [rentals, setRentals] = useState<RentalItem[]>([]);
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
        const res = await apiClient.get('/api/rentals');
        const data = res.data?.data ?? res.data ?? [];
        const list = Array.isArray(data) ? data : data.data ?? [];
        if (mounted) setRentals(list);
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
    <div className="max-w-6xl mx-auto px-4 py-6">
      <h1 className="text-2xl font-semibold mb-4">予約中のスペース</h1>
      {loading && <div>読み込み中...</div>}
      {error && <div className="text-sm text-red-600 mb-4">{error}</div>}

      <div className="grid grid-cols-1 gap-4">
        {rentals.length === 0 && !loading && <div className="text-sm text-gray-500">現在予約中のスペースはありません</div>}

        {rentals.map((r) => (
          <div key={r.id} className="bg-white rounded shadow p-4 flex items-center justify-between">
            <div className="flex items-center space-x-4">
              <div className="w-20 h-16 bg-gray-100 flex-shrink-0">
                {r.land?.thumbnail_url ? (
                  // eslint-disable-next-line @next/next/no-img-element
                  <img src={r.land.thumbnail_url} alt={r.land?.title} className="w-full h-full object-cover" />
                ) : (
                  <div className="w-full h-full flex items-center justify-center text-gray-400">画像なし</div>
                )}
              </div>

              <div>
                <Link href={`/lands/${r.land?.id}`} className="font-medium text-gray-800">{r.land?.title}</Link>
                <div className="text-sm text-gray-500">{r.start_at ? `${new Date(r.start_at).toLocaleString()} 〜 ${new Date(r.end_at ?? '').toLocaleString()}` : ''}</div>
                <div className="text-sm text-gray-700 mt-1">合計: ¥{r.total_price ?? '-'}</div>
              </div>
            </div>

            <div className="flex flex-col items-end space-y-2">
              <span className={`inline-flex px-3 py-1 rounded-full text-sm ${statusClass(r.status)}`}>{r.status ?? '不明'}</span>
              <Link href={`/trades/${r.id}`} className="text-sm text-blue-600 hover:underline">取引詳細</Link>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}
