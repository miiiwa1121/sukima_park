"use client";

import { useEffect, useState } from 'react';
import { useRouter } from 'next/navigation';
import Link from 'next/link';
import useAuth from '../../hooks/useAuth';
import apiClient from '../../lib/axios';

interface TradeItem {
  id: number | string;
  land?: { id: number | string; title?: string };
  renter?: { id: number | string; name?: string };
  start_at?: string | null;
  end_at?: string | null;
  total_price?: number | null;
  status?: string | null;
}

export default function TradesPage() {
  const router = useRouter();
  const { user, fetchUser } = useAuth();
  const [initialized, setInitialized] = useState(false);
  const [trades, setTrades] = useState<TradeItem[]>([]);
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
        const res = await apiClient.get('/api/trades');
        const data = res.data?.data ?? res.data ?? [];
        const list = Array.isArray(data) ? data : data.data ?? [];
        if (mounted) setTrades(list);
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
    <div className="max-w-7xl mx-auto px-4 py-6">
      <h1 className="text-2xl font-semibold mb-4">出品スペースの取引一覧</h1>
      {loading && <div>読み込み中...</div>}
      {error && <div className="text-sm text-red-600 mb-4">{error}</div>}

      <div className="overflow-x-auto bg-white rounded shadow">
        <table className="min-w-full divide-y">
          <thead className="bg-gray-50">
            <tr>
              <th className="px-4 py-2 text-left text-sm font-medium text-gray-500">スペース</th>
              <th className="px-4 py-2 text-left text-sm font-medium text-gray-500">借り手</th>
              <th className="px-4 py-2 text-left text-sm font-medium text-gray-500">期間</th>
              <th className="px-4 py-2 text-left text-sm font-medium text-gray-500">合計</th>
              <th className="px-4 py-2 text-left text-sm font-medium text-gray-500">状態</th>
              <th className="px-4 py-2" />
            </tr>
          </thead>
          <tbody className="divide-y">
            {trades.length === 0 && !loading && (
              <tr>
                <td colSpan={6} className="px-4 py-6 text-center text-sm text-gray-500">取引はありません</td>
              </tr>
            )}

            {trades.map((t) => (
              <tr key={t.id}>
                <td className="px-4 py-3 text-sm font-medium text-gray-800">{t.land?.title}</td>
                <td className="px-4 py-3 text-sm text-gray-700"><Link href={`/members/${t.renter?.id}`} className="text-blue-600 hover:underline">{t.renter?.name}</Link></td>
                <td className="px-4 py-3 text-sm text-gray-700">{t.start_at ? `${new Date(t.start_at).toLocaleDateString()}〜${new Date(t.end_at ?? '').toLocaleDateString()}` : '-'}</td>
                <td className="px-4 py-3 text-sm text-gray-800">¥{t.total_price ?? '-'}</td>
                <td className="px-4 py-3 text-sm text-gray-700">{t.status}</td>
                <td className="px-4 py-3 text-right"><Link href={`/trades/${t.id}`} className="text-blue-600 hover:underline">詳細</Link></td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}
