import { defineConfig } from "vite";
import vue from "@vitejs/plugin-vue";
import path from "path";

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [vue()],
  resolve: {
    alias: {
      "@": path.resolve(__dirname, "/src"),
      "~@": path.resolve(__dirname, "/src"),
    },
  },
  base: "./",
  build: {
    manifest: true,
    rollupOptions: {
      output: {
        assetFileNames: 'assets/ukrgbmap-[name]-[hash][extname]',
        entryFileNames: 'assets/ukrgbmap-[name]-[hash].js'
      }
    }
  }
});
