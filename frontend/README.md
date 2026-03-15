# frontend (Next.js + TypeScript + Tailwind) — starter notes

This directory contains starter helper files for a Next.js (App Router) + TypeScript + Tailwind CSS project that will talk to a Laravel API using Sanctum (cookie-based SPA auth).

## Quick setup commands

Run these in a parent folder where you want to create the `frontend` app.

1) Create the Next.js app (App Router + TypeScript + Tailwind):

```bash
npx create-next-app@latest frontend --typescript --app --tailwind
```

2) Enter the project and install `axios`:

```bash
cd frontend
npm install axios
# or: pnpm add axios
```

3) Create a `.env.local` (copy from `.env.local.example`) and set `NEXT_PUBLIC_API_URL` to your Laravel backend origin (e.g. `http://localhost:8000`).

## Provided helper

- `lib/axios.ts` — an axios instance configured for Laravel Sanctum:
  - uses `withCredentials: true` so cookies are sent and received
  - uses `process.env.NEXT_PUBLIC_API_URL` as `baseURL`
  - exports `getCsrfCookie()` to call `/sanctum/csrf-cookie`
  - exports `withCsrf(fn)` helper that calls `getCsrfCookie()` then runs `fn`

Example usage:

```ts
// pages or app route client-side code
import apiClient, { withCsrf } from '../lib/axios';

async function login(email: string, password: string) {
  return withCsrf(() => apiClient.post('/login', { email, password }));
}

// For authenticated requests after login, use apiClient directly:
// apiClient.get('/api/user')
```

Notes:
- Laravel Sanctum SPA flow expects the browser to call `GET /sanctum/csrf-cookie` first to set the XSRF-TOKEN cookie, then perform POST/PUT/DELETE requests where Laravel will validate the X-XSRF-TOKEN header automatically (axios reads cookie and sets header).
- Ensure your backend CORS and cookie settings allow the frontend origin and send cookies (Access-Control-Allow-Credentials, correct SameSite settings, etc.).

## Recommended initial directory structure

This structure is oriented to a C2C map app and can be extended as you go:

- `app/` — Next.js App Router pages and layout
- `components/` — presentational and shared components (Map, Marker, Button, Modal, etc.)
- `lib/` — low-level libraries and clients (e.g. `lib/axios.ts`, map helpers)
- `hooks/` — custom React hooks (useAuth, useMap, useSWR wrappers)
- `types/` — TypeScript domain types (User, Listing, Location, API responses)
- `services/` or `api/` — higher-level API wrappers that call `lib/axios`
- `utils/` — small utility functions
- `styles/` — global Tailwind config/css if any
- `public/` — static assets

## Next steps / checklist

- Run the create-next-app command above.
- Install `axios`.
- Copy `.env.local.example` → `.env.local` and set `NEXT_PUBLIC_API_URL`.
- Start building `services/auth.ts` that wraps `lib/axios` for login/logout/register and a `useAuth` hook in `hooks/`.
