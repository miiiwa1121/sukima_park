"use client";

import { useEffect, useState } from 'react';
import { useRouter } from 'next/navigation';
import useAuth from '../../../hooks/useAuth';
import apiClient, { withCsrf } from '../../../lib/axios';

export default function ProfileEditPage() {
  const router = useRouter();
  const { user, fetchUser } = useAuth();
  const [initialized, setInitialized] = useState(false);

  const [name, setName] = useState('');
  const [bio, setBio] = useState('');
  const [iconFile, setIconFile] = useState<File | null>(null);
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
    if (user) {
      setName(user.name ?? '');
      // if user has bio property, set it (adjust if backend uses different key)
      // @ts-ignore
      setBio((user as any).bio ?? '');
    }
  }, [user]);

  useEffect(() => {
    if (initialized && !user) {
      router.push('/login');
    }
  }, [initialized, user, router]);

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    setError(null);
    setLoading(true);
    try {
      await withCsrf(async () => {
        const form = new FormData();
        form.append('name', name);
        form.append('bio', bio);
        if (iconFile) form.append('icon_image', iconFile);

        // adjust endpoint if your backend expects /api/user or /api/profile
        await apiClient.post('/api/profile/update', form, {
          headers: { 'Accept': 'application/json' },
        });
      });

      router.push('/mypage');
    } catch (err: any) {
      setError(err?.response?.data?.message ?? err?.message ?? '更新に失敗しました');
    } finally {
      setLoading(false);
    }
  }

  if (!initialized) return <div className="p-6">読み込み中...</div>;
  if (!user) return null;

  return (
    <div className="max-w-3xl mx-auto px-4 py-6">
      <h1 className="text-2xl font-semibold mb-4">プロフィール編集</h1>
      <form onSubmit={handleSubmit} className="space-y-4 bg-white rounded shadow p-6">
        <div>
          <label className="block text-sm text-gray-700">名前</label>
          <input value={name} onChange={(e) => setName(e.target.value)} className="mt-1 w-full border rounded px-2 py-1" required />
        </div>

        <div>
          <label className="block text-sm text-gray-700">自己紹介</label>
          <textarea value={bio} onChange={(e) => setBio(e.target.value)} className="mt-1 w-full border rounded px-2 py-1" rows={4} />
        </div>

        <div>
          <label className="block text-sm text-gray-700">アイコン画像</label>
          <input type="file" accept="image/*" onChange={(e) => setIconFile(e.target.files?.[0] ?? null)} className="mt-1" />
        </div>

        {error && <div className="text-sm text-red-600">{error}</div>}

        <div>
          <button disabled={loading} className="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 disabled:opacity-60">更新する</button>
        </div>
      </form>
    </div>
  );
}
