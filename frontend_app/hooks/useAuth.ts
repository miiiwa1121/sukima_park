import { useState } from 'react';
import apiClient, { withCsrf } from '../lib/axios';

export interface AuthUser {
  id: number | string;
  email: string;
  name?: string | null;
  icon_image?: string | null;
}

export default function useAuth() {
  const [user, setUser] = useState<AuthUser | null>(null);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  async function fetchUser() {
    try {
      setLoading(true);
      setError(null);
      // try common user endpoints; adjust if your backend exposes a different route
  const res = await apiClient.get('/api/me');
  setUser(res.data?.user ?? res.data?.data ?? res.data ?? null);
      return res.data;
    } catch (err: any) {
      // keep user as null on failure
      setUser(null);
      return null;
    } finally {
      setLoading(false);
    }
  }

  async function login(payload: { email: string; password: string }) {
    setLoading(true);
    setError(null);
    try {
      // Ensure Sanctum CSRF cookie is set before login
      return await withCsrf(async () => {
        const res = await apiClient.post('/login', payload);
        // backend may return user in various shapes; try to normalize
  const data = res.data?.user ?? res.data?.data ?? res.data ?? null;
        setUser(data);
        return res;
      });
    } catch (err: any) {
      setError(err?.response?.data?.message ?? err?.message ?? 'Login failed');
      throw err;
    } finally {
      setLoading(false);
    }
  }

  async function register(payload: { name?: string; email: string; password: string; password_confirmation?: string }) {
    setLoading(true);
    setError(null);
    try {
      return await withCsrf(async () => {
        const res = await apiClient.post('/register', payload);
  const data = res.data?.user ?? res.data?.data ?? res.data ?? null;
        setUser(data);
        return res;
      });
    } catch (err: any) {
      setError(err?.response?.data?.message ?? err?.message ?? 'Registration failed');
      throw err;
    } finally {
      setLoading(false);
    }
  }

  async function logout() {
    setLoading(true);
    setError(null);
    try {
      await apiClient.post('/logout');
      setUser(null);
    } catch (err: any) {
      setError(err?.response?.data?.message ?? err?.message ?? 'Logout failed');
      throw err;
    } finally {
      setLoading(false);
    }
  }

  return {
    user,
    setUser,
    loading,
    error,
    login,
    register,
    logout,
    fetchUser,
  } as const;
}
