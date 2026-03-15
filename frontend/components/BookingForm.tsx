"use client";

import { useState, useMemo } from 'react';
import { useRouter } from 'next/navigation';
import useAuth from '../hooks/useAuth';
import apiClient, { withCsrf } from '../lib/axios';

interface BookingFormProps {
  landId: number | string;
  price: number; // unit price
  price_unit?: 'hour' | 'day';
}

export default function BookingForm({ landId, price, price_unit = 'hour' }: BookingFormProps) {
  const router = useRouter();
  const { user } = useAuth();
  const [start, setStart] = useState<string>('');
  const [end, setEnd] = useState<string>('');
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const totals = useMemo(() => {
    if (!start || !end) return { valid: false, units: 0, total: 0 };
    const s = new Date(start);
    const e = new Date(end);
    const ms = e.getTime() - s.getTime();
    if (isNaN(ms) || ms <= 0) return { valid: false, units: 0, total: 0 };

    if (price_unit === 'hour') {
      const hours = ms / (1000 * 60 * 60);
      const units = Math.max(1, Math.ceil(hours));
      return { valid: true, units, total: units * price };
    }

    // day unit
    const days = ms / (1000 * 60 * 60 * 24);
    const units = Math.max(1, Math.ceil(days));
    return { valid: true, units, total: units * price };
  }, [start, end, price, price_unit]);

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    setError(null);

    if (!totals.valid) {
      setError('開始日時と終了日時を正しく入力してください');
      return;
    }

    if (!user) {
      // redirect to login
      router.push('/login');
      return;
    }

    setLoading(true);
    try {
      await withCsrf(async () => {
        const payload = {
          start_at: new Date(start).toISOString(),
          end_at: new Date(end).toISOString(),
          total_price: totals.total,
        };

        // Try posting to an endpoint commonly used by the backend.
        // Adjust the path if your API differs.
        const url = `/api/rental/confirm/${landId}`;
        await apiClient.post(url, payload);
      });

      // simple success toast; you can replace with a nicer UI
      alert('予約が完了しました');
      router.push('/rental_list');
    } catch (err: any) {
      setError(err?.response?.data?.message ?? err?.message ?? '予約に失敗しました');
    } finally {
      setLoading(false);
    }
  }

  return (
    <aside className="p-4 bg-white rounded shadow sticky top-20">
      <h3 className="text-lg font-semibold mb-3">予約する</h3>

      <form onSubmit={handleSubmit} className="space-y-3">
        <div>
          <label className="block text-sm text-gray-700">開始日時</label>
          <input type="datetime-local" value={start} onChange={(e) => setStart(e.target.value)} className="mt-1 w-full border rounded px-2 py-1" required />
        </div>

        <div>
          <label className="block text-sm text-gray-700">終了日時</label>
          <input type="datetime-local" value={end} onChange={(e) => setEnd(e.target.value)} className="mt-1 w-full border rounded px-2 py-1" required />
        </div>

        <div className="pt-2 border-t">
          <div className="text-sm text-gray-600">単価: {price_unit === 'hour' ? `¥${price}/時間` : `¥${price}/日`}</div>
          <div className="text-lg font-semibold mt-2">合計: ¥{totals.total}</div>
          <div className="text-xs text-gray-500">選択ユニット: {totals.units}</div>
        </div>

        {error && <div className="text-sm text-red-600">{error}</div>}

        <div>
          <button type="submit" disabled={loading} className="w-full py-2 px-4 bg-blue-600 text-white rounded hover:bg-blue-700 disabled:opacity-60">
            {loading ? '処理中...' : '予約を確定する'}
          </button>
        </div>
      </form>
    </aside>
  );
}
