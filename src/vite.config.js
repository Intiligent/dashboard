import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
// import compression from 'vite-plugin-compression';
import laravel from 'laravel-vite-plugin';
import { viteStaticCopy } from 'vite-plugin-static-copy';
import path from 'path';
import { readFileSync } from 'fs';

function makeServerOptions() {
    const serverOptions = {
        host: process.env.VITE_SERVER_HOST ?? '0.0.0.0',
        port: Number(process.env.VITE_SERVER_PORT ?? 5173),
        strictPort: true,
        hmr: { host: process.env.VITE_SERVER_HMR_HOST ?? 'localhost' },
    };
    if (process.env.VITE_SERVER_HTTPS_KEY && process.env.VITE_SERVER_HTTPS_CERT) {
        serverOptions.https = {
            ...serverOptions.https,
            key: readFileSync(process.env.VITE_SERVER_HTTPS_KEY),
            cert: readFileSync(process.env.VITE_SERVER_HTTPS_CERT),
        };
    }
    return serverOptions;
}

export default defineConfig({
    server: makeServerOptions(),
    plugins: [
        laravel({
            input: [
                // 'resources/js/entries/units/index.js',
                // 'resources/style/entries/unit.scss',

                'resources/style/entries/login.scss',
                'resources/js/entries/home/index.js',
                'resources/style/entries/home.scss',
            ],
            refresh: true,
            publicDirectory: '../public',
            buildDirectory: 'dashboard',
        }),
        viteStaticCopy({
            targets: [
                {
                    src: 'resources/assets/*',
                    dest: '../assets',
                },
            ],
        }),
        vue(),
        // compression({
        //     algorithm: 'gzip',
        //     ext: '.gz',
        //     threshold: 1024,
        //     deleteOriginFile: false,
        //     verbose: false,
        // }),
    ],
    build: {
        outDir: '../public/dashboard',
        manifest: 'manifest.json',
        emptyOutDir: true,
        chunkSizeWarningLimit: Infinity,
        rollupOptions: {
            output: {
                format: 'es',
                entryFileNames: 'js/[name].[hash].js',
                chunkFileNames: 'js/[name].[hash].js',
                dir: '../public/dashboard',
                assetFileNames: assetInfo => {
                    if (assetInfo.name && assetInfo.name.endsWith('.css')) {
                        return 'css/[name].[hash][extname]';
                    }
                    return 'assets/[name].[hash][extname]';
                },
                manualChunks: {
                    vendor: ['vue'],
                    ui: ['element-plus'],
                    utils: ['axios', 'lodash'],
                },
            },
        },
    },
    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js',
            '@': path.resolve(__dirname, 'resources/js'),
        },
    },
    envDir: path.resolve(__dirname, '..'),
});
