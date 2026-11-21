import type { NextConfig } from "next";
import { redirects } from './src/data/store-editorial.json';

const nextConfig: NextConfig = {
  /* config options here */
  cacheComponents: true,
  partialPrefetching: true,
  async headers() {
    return [{
      source: '/:path*',
      has: [{ type: 'host', value: 'orq\\.mhmdfwzi\\.com' }],
      headers: [{ key: 'X-Robots-Tag', value: 'noindex, nofollow' }],
    }];
  },
  async rewrites() {
    const api = new URL(process.env.API_URL ?? 'http://127.0.0.1:8001/api/v1');
    if (!['127.0.0.1', 'localhost', '[::1]'].includes(api.hostname)) return [];

    return [{
      source: '/api/v1/account/:path*',
      destination: `${api.origin}${api.pathname.replace(/\/$/, '')}/account/:path*`,
    }];
  },
  async redirects() {
    return [
      {
        source: '/:path*',
        has: [{ type: 'host', value: 'www\\.orqperfume\\.com' }],
        destination: 'https://orqperfume.com/:path*',
        permanent: true,
      },
      { source: '/preview', destination: '/', permanent: true },
      { source: '/preview/concept-4', destination: '/', permanent: true },
      { source: '/preview/products/:slug', destination: '/products/:slug', permanent: true },
      { source: '/about', destination: '/pages/our-story', permanent: true },
      { source: '/contact', destination: '/pages/contact', permanent: true },
      { source: '/privacy', destination: '/pages/privacy-policy', permanent: true },
      { source: '/terms', destination: '/pages/terms', permanent: true },
      { source: '/login', destination: '/account', permanent: false },
      ...redirects.map(({ from, to }) => ({ source: from, destination: to, permanent: true })),
    ];
  },
  // Cap build parallelism: 11 default workers can exhaust memory on small machines.
  experimental: {
    cpus: 3,
  },
  images: {
    // Images uploaded through the Laravel/Filament admin are served from /storage.
    remotePatterns: [
      { protocol: 'http', hostname: '127.0.0.1', port: '8001', pathname: '/storage/**' },
    ],
  },
  turbopack: {
    rules: {
      "*.css": {
        loaders: ["@tailwindcss/turbopack"],
        as: "*.css",
      },
    },
  },
};

export default nextConfig;
