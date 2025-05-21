import { defineConfig } from 'vite';
import path from 'path';
import glob from 'fast-glob';
import vitePluginImp from 'vite-plugin-imp';
import vitePluginLegacy from '@vitejs/plugin-legacy';
import vitePluginSassDts from 'vite-plugin-sass-dts';

// Helper to get all entry points for SCSS and JS
function getEntries() {
  const scssFiles = glob.sync('scss/**/*.scss', { cwd: __dirname });
  const jsFiles = glob.sync('js/**/*.js', { cwd: __dirname });
  const entries: Record<string, string> = {};

  scssFiles.forEach(file => {
    const name = file.replace(/\\/g, '/').replace(/\.(scss)$/, '');
    entries[name] = path.resolve(__dirname, file);
  });
  jsFiles.forEach(file => {
    const name = file.replace(/\\/g, '/').replace(/\.(js)$/, '');
    entries[name] = path.resolve(__dirname, file);
  });
  return entries;
}

export default defineConfig({
  root: __dirname,
  publicDir: false, // Don't copy public dir
  build: {
    outDir: path.resolve(__dirname, '../public/resources'),
    emptyOutDir: true,
    rollupOptions: {
      input: getEntries(),
      output: {
        entryFileNames: (chunkInfo) => {
          // Keep directory structure for JS
          if (chunkInfo.name.startsWith('js/')) {
            return chunkInfo.name.replace(/^js\//, 'js/') + '.js';
          }
          // Keep directory structure for SCSS (output as CSS)
          if (chunkInfo.name.startsWith('scss/')) {
            return chunkInfo.name.replace(/^scss\//, 'css/') + '.js';
          }
          return '[name].js';
        },
        assetFileNames: (assetInfo) => {
          if (assetInfo.name && assetInfo.name.endsWith('.css')) {
            return assetInfo.name.replace(/^scss\//, 'css/');
          }
          return '[name][extname]';
        },
      },
    },
    minify: 'esbuild',
    cssCodeSplit: true,
  },
  plugins: [
    vitePluginLegacy(),
    vitePluginImp(),
    vitePluginSassDts(),
  ],
  resolve: {
    alias: {
      '@scss': path.resolve(__dirname, 'scss'),
      '@js': path.resolve(__dirname, 'js'),
    },
  },
  css: {
    preprocessorOptions: {
      scss: {
        additionalData: '',
      },
    },
  },
  server: {
    watch: {
      usePolling: true,
    },
    strictPort: true,
  },
});
