import type { NextConfig } from "next";

const nextConfig: NextConfig = {
  /* config options here */
  cacheComponents: true,
  partialPrefetching: true,
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
