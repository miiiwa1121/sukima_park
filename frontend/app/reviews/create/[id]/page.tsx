"use client";

import { useEffect, useState } from 'react';
import { useParams, useRouter } from 'next/navigation';
import useAuth from '../../../../hooks/useAuth';
import apiClient, { withCsrf } from '../../../../lib/axios';

export default function ReviewCreatePage() {
  const params = useParams();
  const id = params?.id as string | undefined;
  const router = useRouter();
  const { user, fetchUser } = useAuth();
  const [initialized, setInitialized] = useState(false);

  const [rating, setRating] = useState<number>(5);
  const [comment, setComment] = useState('');
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
    if (initialized && !user) {
      router.push('/login');
    }
  }, [initialized, user, router]);

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    setError(null);
    if (!id) {
      setError('対象の取引が見つかりません');
      return;
    }
    setLoading(true);
    try {
      await withCsrf(async () => {
        const payload = { trade_id: id, rating, comment };
        await apiClient.post('/api/review', payload);
      });

      // after success, redirect to rentals (or trades)
      router.push('/rentals');
    } catch (err: any) {
      setError(err?.response?.data?.message ?? err?.message ?? '送信に失敗しました');
    } finally {
      setLoading(false);
    }
  }

  return (
    <div className="max-w-3xl mx-auto px-4 py-6">
      <h1 className="text-2xl font-semibold mb-4">レビューを投稿する</h1>

      <form onSubmit={handleSubmit} className="bg-white rounded shadow p-6 space-y-4">
        <div>
          <label className="block text-sm text-gray-700 mb-2">評価</label>
          <div className="flex items-center space-x-2">
            {[1,2,3,4,5].map((s) => (
              <button key={s} type="button" onClick={() => setRating(s)} className={`px-3 py-1 rounded ${s <= rating ? 'bg-yellow-400 text-white' : 'bg-gray-100 text-gray-700'}`}>
                {s}★
              </button>
            ))}
          </div>
        </div>

        <div>
          <label className="block text-sm text-gray-700 mb-2">コメント</label>
          <textarea value={comment} onChange={(e) => setComment(e.target.value)} rows={6} className="w-full border rounded p-2" />
        </div>

        {error && <div className="text-sm text-red-600">{error}</div>}

        <div>
          <button disabled={loading} className="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 disabled:opacity-60">
            {loading ? '送信中...' : 'レビューを投稿する'}
          </button>
        </div>
      </form>
    </div>
  );
}
