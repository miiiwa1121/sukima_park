import './globals.css';
import Header from '../components/Header';

export const metadata = {
  title: 'SukimaPark',
};

export default function RootLayout({ children }: { children: React.ReactNode }) {
  return (
    <html lang="ja">
      <head>
        {/* Include Leaflet CSS from CDN to avoid missing asset issues during build */}
        <link
          rel="stylesheet"
          href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-sA+e2eXkYpP8E3KShmS4Gv7wG7k0xszH1p2R9ZZk8wY="
          crossOrigin=""
        />
      </head>
      <body className="bg-gray-50">
        <Header />
        <main>{children}</main>
      </body>
    </html>
  );
}
