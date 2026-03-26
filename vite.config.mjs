import { defineConfig } from "vite";
import vue from "@vitejs/plugin-vue2";
import path from "path";

export default defineConfig({
  base: "./",
  plugins: [vue()],
  resolve: {
    alias: {
      assets: path.resolve(__dirname, "./server/assets"),
      less: path.resolve(__dirname, "./app/less"),
      vue: path.resolve(__dirname, "./node_modules/vue/dist/vue.common.js")
    }
  },
  css: {
    preprocessorOptions: {
      less: {
        additionalData: '@import "less/globals.less";',
        javascriptEnabled: true
      }
    }
  },
  server: {
    host: "0.0.0.0",
    port: Number(process.env.NODEDEVPORT || 2222),
    strictPort: true,
    cors: true
  },
  build: {
    outDir: "server/dist",
    emptyOutDir: true,
    manifest: true,
    chunkSizeWarningLimit: 700,
    rollupOptions: {
      input: {
        app: path.resolve(__dirname, "./app/index.js")
      },
      output: {
        manualChunks: {
          vue: ["vue", "vuex", "vue-router", "vue-i18n"],
          slider: ["vue-slider-component/dist/vue-slider-component.common.js"]
        }
      }
    }
  }
});
