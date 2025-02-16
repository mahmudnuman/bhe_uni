import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import { fileURLToPath, URL } from 'node:url';
import path from 'path';

export default defineConfig({
  plugins: [vue()],
  server: {
    host: '127.0.0.1',
  },
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./resources/js', import.meta.url)),
    },
  },
  build: {
    outDir: path.resolve(__dirname, 'public/build'),
    manifest: 'manifest.json', // Specify the manifest filename explicitly
    assetsDir: '', // This prevents assets from being placed in a subdirectory
    rollupOptions: {
      input: 'resources/js/app.js',
    },
  },
});
