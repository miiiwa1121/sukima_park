"use client";

import { useEffect, useState } from 'react';
import dynamic from 'next/dynamic';
import apiClient from '../lib/axios';
import LandCard from '../components/LandCard';
import 'leaflet/dist/leaflet.css';
import L from 'leaflet';

// Fix leaflet's default icon paths when bundlers can't resolve images
delete (L.Icon.Default.prototype as any)._getIconUrl;
L.Icon.Default.mergeOptions({
  iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',
  iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
  shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
});

const MapContainer = dynamic(() => import('react-leaflet').then((m) => m.MapContainer), { ssr: false });
const TileLayer = dynamic(() => import('react-leaflet').then((m) => m.TileLayer), { ssr: false });
const Marker = dynamic(() => import('react-leaflet').then((m) => m.Marker), { ssr: false });
const Popup = dynamic(() => import('react-leaflet').then((m) => m.Popup), { ssr: false });

interface LandSummary {
  id: number | string;
  title: string;
  price?: number | null;
  thumbnail_url?: string | null;
  lat?: number | null;
  lng?: number | null;
}

export default function HomePage() {
  const [lands, setLands] = useState<LandSummary[]>([]);
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    async function load() {
      setLoading(true);
      try {
        const res = await apiClient.get('/api/search');
        // expect an array or a paginated response; try several shapes
        const data = res.data?.data ?? res.data ?? [];
        // if paginated, data could be object with data property
        const list = Array.isArray(data) ? data : data.data ?? [];
        setLands(list);
      } catch (e) {
        console.error('Failed to load lands', e);
      } finally {
        setLoading(false);
      }
    }

    load();
  }, []);

  // center map based on first land or fallback
  const center: [number, number] = lands.length && lands[0].lat && lands[0].lng ? [lands[0].lat as number, lands[0].lng as number] : [35.681236, 139.767125];

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Left column: list */}
        <div className="lg:col-span-1">
          <h2 className="text-lg font-semibold mb-4">スペース一覧</h2>
          {loading && <div>読み込み中...</div>}
          <div className="space-y-3">
            {lands.map((land) => (
              <LandCard key={land.id} land={land} />
            ))}
            {!loading && lands.length === 0 && <div className="text-sm text-gray-500">該当するスペースが見つかりません</div>}
          </div>
        </div>

        {/* Right column: map spanning 2 cols on large */}
        <div className="lg:col-span-2">
          <div className="w-full h-[70vh] rounded shadow overflow-hidden">
            <MapContainer center={center} zoom={13} style={{ height: '100%', width: '100%' }}>
              <TileLayer url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png" attribution="&copy; OpenStreetMap contributors" />
              {lands.map((land) => (
                land.lat && land.lng ? (
                  <Marker key={land.id} position={[land.lat as number, land.lng as number]}>
                    <Popup>
                      <div className="text-sm">
                        <strong>{land.title}</strong>
                        <div>{land.price ? `¥${land.price}` : '価格情報なし'}</div>
                      </div>
                    </Popup>
                  </Marker>
                ) : null
              ))}
            </MapContainer>
          </div>
        </div>
      </div>
    </div>
  );
}
