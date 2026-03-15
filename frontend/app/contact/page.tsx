"use client";

import { useEffect, useState } from 'react';
import { useRouter } from 'next/navigation';
import useAuth from '../../hooks/useAuth';
import apiClient, { withCsrf } from '../../lib/axios';

export default function ContactPage() {
  const router = useRouter();
  const { user, fetchUser } = useAuth();
  const [initialized, setInitialized] = useState(false);

  const [name, setName] = useState('');
  const [email, setEmail] = useState('');
  const [message, setMessage] = useState('');
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    let mounted = true;
    async function init() {
      // try to fetch user silently for prefill; don't force login
      try { await fetchUser(); } catch (_) {}
      if (mounted) setInitialized(true);
    }
    init();
    return () => { mounted = false; };
  }, [fetchUser]);

  useEffect(() => {
    if (user) {
      setName(user.name ?? '');
      setEmail(user.email ?? '');
    }
  }, [user]);

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    setError(null);
    if (!name || !email || !message) {
      setError('必須項目を入力してください');
      return;
    }
    setLoading(true);
    try {
      await withCsrf(async () => {
        await apiClient.post('/api/contact', { name, email, message });
      });

      alert('お問い合わせを受け付けました。');
      router.push('/');
    } catch (err: any) {
      setError(err?.response?.data?.message ?? err?.message ?? '送信に失敗しました');
    } finally {
      setLoading(false);
    }
  }

  return (
    <div className="max-w-3xl mx-auto px-4 py-6">
      <h1 className="text-2xl font-semibold mb-4">お問い合わせ</h1>

      <form onSubmit={handleSubmit} className="bg-white rounded shadow p-6 space-y-4">
        <div>
          <label className="block text-sm text-gray-700">お名前</label>
          <input value={name} onChange={(e) => setName(e.target.value)} className="mt-1 w-full border rounded px-2 py-1" />
        </div>

        <div>
          <label className="block text-sm text-gray-700">メールアドレス</label>
          <input value={email} onChange={(e) => setEmail(e.target.value)} className="mt-1 w-full border rounded px-2 py-1" />
        </div>

        <div>
          <label className="block text-sm text-gray-700">お問い合わせ内容</label>
          <textarea value={message} onChange={(e) => setMessage(e.target.value)} rows={6} className="mt-1 w-full border rounded px-2 py-1" />
        </div>

        {error && <div className="text-sm text-red-600">{error}</div>}

        <div>
          <button disabled={loading} className="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 disabled:opacity-60">
            {loading ? '送信中...' : '送信する'}
          </button>
        </div>
      </form>
    </div>
  );
}
