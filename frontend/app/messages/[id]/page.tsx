"use client";

import { useEffect, useRef, useState } from 'react';
import { useParams, useRouter } from 'next/navigation';
import useAuth from '../../../hooks/useAuth';
import apiClient, { withCsrf } from '../../../lib/axios';

interface Message {
  id: number | string;
  user_id?: number | string;
  content: string;
  created_at?: string | null;
}

export default function MessageRoomPage() {
  const params = useParams();
  const id = params?.id as string | undefined;
  const router = useRouter();
  const { user, fetchUser } = useAuth();
  const [initialized, setInitialized] = useState(false);
  const [messages, setMessages] = useState<Message[]>([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [input, setInput] = useState('');
  const bottomRef = useRef<HTMLDivElement | null>(null);
  const pollRef = useRef<number | null>(null);

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
      setError(null);
      try {
        const res = await apiClient.get(`/api/messages/${id}`);
        const data = res.data?.data ?? res.data ?? [];
        if (mounted) setMessages(Array.isArray(data) ? data : data.data ?? []);
        // scroll to bottom after initial load
        setTimeout(() => bottomRef.current?.scrollIntoView({ behavior: 'smooth' }), 50);
      } catch (e: any) {
        setError(e?.response?.data?.message ?? e?.message ?? '取得に失敗しました');
      } finally {
        if (mounted) setLoading(false);
      }
    }

    load();

    // Polling: check for new messages every 5 seconds
    const interval = window.setInterval(async () => {
      try {
        const res = await apiClient.get(`/api/messages/${id}/poll`);
        const data = res.data?.data ?? res.data ?? [];
        const newMessages = Array.isArray(data) ? data : data.data ?? [];
        if (mounted && newMessages.length > 0) {
          // append only messages that are not present
          setMessages((prev) => {
            const ids = new Set(prev.map((m) => String(m.id)));
            const toAdd = newMessages.filter((m: Message) => !ids.has(String(m.id)));
            if (toAdd.length === 0) return prev;
            return [...prev, ...toAdd];
          });
          setTimeout(() => bottomRef.current?.scrollIntoView({ behavior: 'smooth' }), 50);
        }
      } catch (e) {
        // ignore poll errors silently
      }
    }, 5000);
    pollRef.current = interval as unknown as number;

    return () => {
      mounted = false;
      if (pollRef.current) window.clearInterval(pollRef.current);
    };
  }, [initialized, user, id, router]);

  async function handleSend(e?: React.FormEvent) {
    e?.preventDefault();
    if (!input.trim()) return;
    if (!user) {
      router.push('/login');
      return;
    }
    if (!id) return;

    const content = input.trim();
    setInput('');
    try {
      await withCsrf(async () => {
        const res = await apiClient.post(`/api/messages/${id}`, { content });
        const msg = res.data?.data ?? res.data ?? null;
        // append message without reloading
        if (msg) setMessages((prev) => [...prev, msg]);
        setTimeout(() => bottomRef.current?.scrollIntoView({ behavior: 'smooth' }), 50);
      });
    } catch (err: any) {
      setError(err?.response?.data?.message ?? err?.message ?? '送信に失敗しました');
    }
  }

  if (!initialized || loading) return <div className="p-6">読み込み中...</div>;
  if (!user) return null;

  return (
    <div className="max-w-3xl mx-auto px-4 py-6 h-[80vh] flex flex-col">
      <h1 className="text-2xl font-semibold mb-4">チャットルーム</h1>

      <div className="flex-1 overflow-auto bg-gray-50 p-4 rounded">
        {messages.map((m) => {
          const isMe = String(m.user_id) === String(user.id);
          return (
            <div key={m.id} className={`mb-3 flex ${isMe ? 'justify-end' : 'justify-start'}`}>
              <div className={`${isMe ? 'bg-blue-600 text-white' : 'bg-white text-gray-800'} max-w-[70%] p-3 rounded-lg shadow`}>
                <div className="whitespace-pre-wrap">{m.content}</div>
                <div className="text-xs text-gray-300 mt-2 text-right">{m.created_at ? new Date(m.created_at).toLocaleString() : ''}</div>
              </div>
            </div>
          );
        })}
        <div ref={bottomRef} />
      </div>

      <form onSubmit={(e) => handleSend(e)} className="mt-3">
        <div className="flex items-end space-x-2">
          <textarea value={input} onChange={(e) => setInput(e.target.value)} placeholder="メッセージを入力..." className="flex-1 resize-none border rounded p-2 h-16" />
          <button type="submit" className="px-4 py-2 bg-blue-600 text-white rounded">送信</button>
        </div>
      </form>
    </div>
  );
}
