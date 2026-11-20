import type { NextConfig } from "next";
import { redirects } from './src/data/store-editorial.json';

const nextConfig: NextConfig = {
  /* config options here */
  cacheComponents: true,
  partialPrefetching: true,
  async redirects() {
    return [
      { source: '/preview', destination: '/', permanent: true },
      { source: '/preview/concept-4', destination: '/', permanent: true },
      { source: '/preview/products/:slug', destination: '/products/:slug', permanent: true },
      { source: '/about', destination: '/pages/our-story', permanent: true },
      { source: '/contact', destination: '/pages/contact', permanent: true },
      { source: '/privacy', destination: '/pages/privacy-policy', permanent: true },
      { source: '/terms', destination: '/pages/terms', permanent: true },
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
