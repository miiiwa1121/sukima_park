"use client";

import { useEffect, useState } from 'react';
import { useParams } from 'next/navigation';
import apiClient from '../../../lib/axios';
import BookingForm from '../../../components/BookingForm';

interface LandDetail {
  id: number | string;
  title: string;
  description?: string | null;
  price?: number | null;
  price_unit?: 'hour' | 'day';
  images?: string[];
  address?: string;
  owner?: { id: number | string; name?: string; icon_image?: string | null };
  reviews?: Array<{ id: number | string; user_name?: string; comment?: string; rating?: number }>;
  lat?: number | null;
  lng?: number | null;
}

export default function LandDetailPage() {
  const params = useParams();
  const id = params?.id as string | undefined;
  const [land, setLand] = useState<LandDetail | null>(null);
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    if (!id) return;
    async function load() {
      setLoading(true);
      try {
        const res = await apiClient.get(`/api/lands/${id}`);
        const data = res.data?.data ?? res.data ?? null;
        setLand(data);
      } catch (e) {
        console.error('failed load land', e);
      } finally {
        setLoading(false);
      }
    }
    load();
  }, [id]);

  if (loading) return <div className="p-6">読み込み中...</div>;
  if (!land) return <div className="p-6">スペースが見つかりません</div>;

  return (
    <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Main content */}
        <div className="lg:col-span-2">
          {/* Images */}
          <div className="bg-white rounded shadow overflow-hidden mb-4">
            {land.images && land.images.length > 0 ? (
              // show first image prominently
              // eslint-disable-next-line @next/next/no-img-element
              <img src={land.images[0]} alt={land.title} className="w-full h-96 object-cover" />
            ) : (
              <div className="w-full h-96 bg-gray-100 flex items-center justify-center text-gray-400">写真なし</div>
            )}
          </div>

          <div className="bg-white rounded shadow p-6">
            <h1 className="text-2xl font-semibold mb-2">{land.title}</h1>
            <div className="text-sm text-gray-600 mb-4">{land.address}</div>
            <div className="text-lg font-bold mb-4">{land.price ? `¥${land.price}` : '価格情報なし'}{land.price_unit === 'hour' ? '/時間' : '/日'}</div>

            <div className="prose mb-6">{land.description}</div>

            <div className="border-t pt-4">
              <h3 className="text-lg font-semibold mb-2">オーナー</h3>
              <div className="flex items-center space-x-3">
                {land.owner?.icon_image ? (
                  // eslint-disable-next-line @next/next/no-img-element
                  <img src={land.owner.icon_image} alt={land.owner.name} className="w-12 h-12 rounded-full object-cover" />
                ) : (
                  <div className="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center text-gray-500">N</div>
                )}
                <div>
                  <div className="text-sm font-medium">{land.owner?.name}</div>
                </div>
              </div>
            </div>

            <div className="mt-6 border-t pt-4">
              <h3 className="text-lg font-semibold mb-3">レビュー</h3>
              {land.reviews && land.reviews.length > 0 ? (
                <ul className="space-y-3">
                  {land.reviews.map((r) => (
                    <li key={r.id} className="p-3 bg-gray-50 rounded">
                      <div className="text-sm font-medium">{r.user_name}</div>
                      <div className="text-sm text-gray-700">{r.comment}</div>
                    </li>
                  ))}
                </ul>
              ) : (
                <div className="text-sm text-gray-500">レビューはまだありません</div>
              )}
            </div>
          </div>
        </div>

        {/* Sticky booking form */}
        <div className="lg:col-span-1">
          <BookingForm landId={land.id} price={land.price ?? 0} price_unit={land.price_unit ?? 'hour'} />
        </div>
      </div>
    </div>
  );
}
