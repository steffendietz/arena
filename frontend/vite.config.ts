import { resolve } from "path";
import { defineConfig } from 'vite'
import { svelte } from '@sveltejs/vite-plugin-svelte'

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [svelte()],
  server: {
    proxy: {
      // string shorthand
      '^(?!/src|/node_modules|/@).*': {
        target: 'http://localhost:8080',
        changeOrigin: true,
        secure: false
      },
    }
  },
  build: {
    emptyOutDir: false,
    outDir: '../public',
    // generate manifest.json in outDir
    manifest: true,
    rollupOptions: {
      input: resolve(__dirname, 'src', 'main.ts')
    }
  }
})
