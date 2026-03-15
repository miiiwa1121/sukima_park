"use client";

import { useEffect, useState } from 'react';
import { useParams, useRouter } from 'next/navigation';
import useAuth from '../../../hooks/useAuth';
import apiClient from '../../../lib/axios';
import Link from 'next/link';

interface TradeDetail {
  id: number | string;
  land?: { id: number | string; title?: string; thumbnail_url?: string | null };
  renter?: { id: number | string; name?: string; icon_image?: string | null };
  start_at?: string | null;
  end_at?: string | null;
  total_price?: number | null;
  breakdown?: Array<{ label: string; amount: number }>;
  status?: string | null;
}

export default function TradeDetailPage() {
  const params = useParams();
  const id = params?.id as string | undefined;
  const router = useRouter();
  const { user, fetchUser } = useAuth();
  const [initialized, setInitialized] = useState(false);
  const [trade, setTrade] = useState<TradeDetail | null>(null);
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
    if (!id) return;

    let mounted = true;
    async function load() {
      setLoading(true);
      try {
        const res = await apiClient.get(`/api/trades/${id}`);
        const data = res.data?.data ?? res.data ?? null;
        if (mounted) setTrade(data);
      } catch (e: any) {
        setError(e?.response?.data?.message ?? e?.message ?? '取得に失敗しました');
      } finally {
        if (mounted) setLoading(false);
      }
    }
    load();
    return () => { mounted = false; };
  }, [initialized, user, id, router]);

  if (!initialized || loading) return <div className="p-6">読み込み中...</div>;
  if (error) return <div className="p-6 text-red-600">{error}</div>;
  if (!trade) return <div className="p-6">取引が見つかりません</div>;

  return (
    <div className="max-w-4xl mx-auto px-4 py-6">
      <div className="bg-white rounded shadow p-6">
        <div className="flex items-start space-x-6">
          <div className="w-36 h-28 bg-gray-100">
            {trade.land?.thumbnail_url ? (
              // eslint-disable-next-line @next/next/no-img-element
              <img src={trade.land.thumbnail_url} alt={trade.land?.title} className="w-full h-full object-cover" />
            ) : (
              <div className="w-full h-full flex items-center justify-center text-gray-400">写真なし</div>
            )}
          </div>

          <div className="flex-1">
            <h2 className="text-xl font-semibold">{trade.land?.title}</h2>
            <div className="text-sm text-gray-600">{trade.start_at ? `${new Date(trade.start_at).toLocaleString()} 〜 ${new Date(trade.end_at ?? '').toLocaleString()}` : ''}</div>
            <div className="mt-3">
              <strong>合計:</strong> ¥{trade.total_price ?? '-'}
            </div>

            <div className="mt-4">
              <h3 className="font-medium">内訳</h3>
              <ul className="mt-2">
                {trade.breakdown && trade.breakdown.length > 0 ? (
                  trade.breakdown.map((b, i) => (
                    <li key={i} className="text-sm text-gray-700">{b.label}: ¥{b.amount}</li>
                  ))
                ) : (
                  <li className="text-sm text-gray-500">内訳情報はありません</li>
                )}
              </ul>
            </div>

            <div className="mt-4 border-t pt-4">
              <div className="flex items-center space-x-3">
                {trade.renter?.icon_image ? (
                  // eslint-disable-next-line @next/next/no-img-element
                  <img src={trade.renter.icon_image} alt={trade.renter.name} className="w-10 h-10 rounded-full object-cover" />
                ) : (
                  <div className="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500">N</div>
                )}

                <div>
                  <div className="text-sm font-medium">{trade.renter?.name}</div>
                  <Link href={`/members/${trade.renter?.id}`} className="text-sm text-blue-600 hover:underline">プロフィールを見る</Link>
                </div>
              </div>
            </div>

            <div className="mt-6 flex space-x-3">
              <button className="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">レビューを書く</button>
              <button className="px-4 py-2 bg-gray-100 text-gray-800 rounded hover:bg-gray-200">メッセージを送る</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
