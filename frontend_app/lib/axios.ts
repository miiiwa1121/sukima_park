import axios, { AxiosInstance } from 'axios';

// Base URL comes from environment variable. Example: NEXT_PUBLIC_API_URL=http://localhost:8000
const baseURL = process.env.NEXT_PUBLIC_API_URL ?? 'http://localhost:8000';

// Create a pre-configured axios instance for Laravel Sanctum (cookie-based SPA auth)
export const apiClient: AxiosInstance = axios.create({
  baseURL,
  withCredentials: true, // important: send/receive cookies for Sanctum session
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json',
  },
});

/**
 * Request Laravel Sanctum CSRF cookie endpoint.
 * Call this before any state-changing request (POST/PUT/DELETE) to ensure the XSRF cookie is set.
 * Usage: await getCsrfCookie(); then make your POST /login or other requests using `apiClient`.
 */
export async function getCsrfCookie() {
  // Laravel Sanctum expects GET /sanctum/csrf-cookie
  return apiClient.get('/sanctum/csrf-cookie');
}

/**
 * Convenience helper that ensures CSRF cookie then executes the provided callback.
 * Useful to wrap login/registration requests.
 */
export async function withCsrf<T>(fn: () => Promise<T>): Promise<T> {
  await getCsrfCookie();
  return fn();
}

export default apiClient;
