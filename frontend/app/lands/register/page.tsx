"use client";

import { useState, useEffect } from 'react';
import { useRouter } from 'next/navigation';
import useAuth from '../../../hooks/useAuth';
import apiClient, { withCsrf } from '../../../lib/axios';

export default function LandRegisterPage() {
  const router = useRouter();
  const { user, fetchUser } = useAuth();
  const [initialized, setInitialized] = useState(false);

  const [title, setTitle] = useState('');
  const [address, setAddress] = useState('');
  const [price, setPrice] = useState<number | ''>('');
  const [priceUnit, setPriceUnit] = useState<'hour' | 'day'>('hour');
  const [area, setArea] = useState('');
  const [description, setDescription] = useState('');
  const [images, setImages] = useState<FileList | null>(null);
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
    if (!title || !address || !price) {
      setError('必須項目を入力してください');
      return;
    }

    setLoading(true);
    try {
      await withCsrf(async () => {
        const form = new FormData();
        form.append('title', title);
        form.append('address', address);
        form.append('price', String(price));
        form.append('price_unit', priceUnit);
        form.append('area', area);
        form.append('description', description);
        if (images) {
          Array.from(images).forEach((f) => form.append('images[]', f));
        }

        // Adjust endpoint name if backend expects /api/lands or /api/land/register
        await apiClient.post('/api/land/register', form, {
          headers: {
            // Let the browser set Content-Type including the boundary
            Accept: 'application/json',
          },
        });
      });

      router.push('/mypage');
    } catch (err: any) {
      setError(err?.response?.data?.message ?? err?.message ?? '登録に失敗しました');
    } finally {
      setLoading(false);
    }
  }

  if (!initialized) return <div className="p-6">読み込み中...</div>;
  if (!user) return null;

  return (
    <div className="max-w-3xl mx-auto px-4 py-6">
      <h1 className="text-2xl font-semibold mb-4">スペースを出品する</h1>
      <form onSubmit={handleSubmit} className="space-y-4 bg-white rounded shadow p-6">
        <div>
          <label className="block text-sm text-gray-700">タイトル</label>
          <input value={title} onChange={(e) => setTitle(e.target.value)} className="mt-1 w-full border rounded px-2 py-1" required />
        </div>

        <div>
          <label className="block text-sm text-gray-700">住所</label>
          <input value={address} onChange={(e) => setAddress(e.target.value)} className="mt-1 w-full border rounded px-2 py-1" required />
        </div>

        <div className="grid grid-cols-2 gap-2">
          <div>
            <label className="block text-sm text-gray-700">価格</label>
            <input type="number" value={price} onChange={(e) => setPrice(e.target.value === '' ? '' : Number(e.target.value))} className="mt-1 w-full border rounded px-2 py-1" required />
          </div>
          <div>
            <label className="block text-sm text-gray-700">単位</label>
            <select value={priceUnit} onChange={(e) => setPriceUnit(e.target.value as any)} className="mt-1 w-full border rounded px-2 py-1">
              <option value="hour">時間</option>
              <option value="day">日</option>
            </select>
          </div>
        </div>

        <div>
          <label className="block text-sm text-gray-700">面積</label>
          <input value={area} onChange={(e) => setArea(e.target.value)} className="mt-1 w-full border rounded px-2 py-1" />
        </div>

        <div>
          <label className="block text-sm text-gray-700">説明</label>
          <textarea value={description} onChange={(e) => setDescription(e.target.value)} className="mt-1 w-full border rounded px-2 py-1" rows={6} />
        </div>

        <div>
          <label className="block text-sm text-gray-700">画像 (複数可)</label>
          <input type="file" accept="image/*" multiple onChange={(e) => setImages(e.target.files)} className="mt-1" />
        </div>

        {error && <div className="text-sm text-red-600">{error}</div>}

        <div>
          <button disabled={loading} className="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 disabled:opacity-60">出品する</button>
        </div>
      </form>
    </div>
  );
}
