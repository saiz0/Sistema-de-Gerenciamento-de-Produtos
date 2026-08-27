import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
  plugins: [vue()],
  server: {
    host: '0.0.0.0',
    watch: {
      usePolling: process.env.VITE_USE_POLLING === 'true',
      interval: Number(process.env.VITE_POLL_INTERVAL || 500),
    },
  },
})
