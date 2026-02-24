import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import tailwind from '@tailwindcss/vite';
import path from 'path';

// https://vite.dev/config/
export default defineConfig({
  plugins: [react(), tailwind()],
  resolve: {
    alias: {
      '@': path.resolve(__dirname, './src'),
    },
  },
  server: {
    proxy: {
      '/api': {
        target: 'http://192.168.0.101:5000',
        changeOrigin: true,
      },
      '/data-api': {
        target: 'http://apis.data.go.kr',
        changeOrigin: true,
        rewrite: (path: string) => path.replace(/^\/data-api/, ''),
      },
      '/kma-img': {
        target: 'http://www.kma.go.kr',
        changeOrigin: true,
        rewrite: (path: string) => path.replace(/^\/kma-img/, ''),
      },
    },
  },
});
