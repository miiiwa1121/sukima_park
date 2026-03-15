"use client";

import Link from 'next/link';

export interface LandSummary {
  id: number | string;
  title: string;
  price?: number | null;
  thumbnail_url?: string | null;
  lat?: number | null;
  lng?: number | null;
}

export default function LandCard({ land }: { land: LandSummary }) {
  return (
    <article className="flex bg-white rounded shadow overflow-hidden">
      <div className="w-32 h-32 flex-shrink-0 bg-gray-100">
        {land.thumbnail_url ? (
          // eslint-disable-next-line @next/next/no-img-element
          <img src={land.thumbnail_url} alt={land.title} className="object-cover w-full h-full" />
        ) : (
          <div className="w-full h-full flex items-center justify-center text-gray-400">写真なし</div>
        )}
      </div>

      <div className="p-3 flex-1">
        <h3 className="text-sm font-semibold text-gray-800">{land.title}</h3>
        <p className="text-sm text-gray-500 mt-1">{land.price ? `¥${land.price}` : '価格情報なし'}</p>
        <div className="mt-3">
          <Link href={`/lands/${land.id}`} className="text-sm text-blue-600 hover:underline">詳細を見る</Link>
        </div>
      </div>
    </article>
  );
}
