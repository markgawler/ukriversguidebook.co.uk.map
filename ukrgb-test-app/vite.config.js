import { fileURLToPath, URL } from 'node:url'

import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import vueDevTools from 'vite-plugin-vue-devtools'

// https://vite.dev/config/
export default defineConfig({
  plugins: [
    vue(),
    vueDevTools(),
  ],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url))
    },
  },
  build: {
    manifest: true,
    rollupOptions: {
      output: {
        assetFileNames: 'assets/ukrgbtest-[name]-[hash][extname]',
        entryFileNames: 'assets/ukrgbtest-[name]-[hash].js'
      }
    }
  }
})
