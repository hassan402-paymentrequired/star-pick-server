import tailwindcss from '@tailwindcss/vite';
import react from '@vitejs/plugin-react';
import laravel from 'laravel-vite-plugin';
import { resolve } from 'node:path';
import { defineConfig } from 'vite';

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.tsx',
            // ssr: 'resources/js/ssr.tsx',
            refresh: true,
        }),
        react(),
        tailwindcss(),
    ],
    resolve: {
        alias: {
            'ziggy-js': resolve(__dirname, 'vendor/tightenco/ziggy'),
            '@': '/resources/js',
        },
    },
    server: {
        host: true,
        port: 5173,
        origin: 'https://starpick.com.ng:5173',
        cors: {
            origin: 'https://starpick.com.ng',
            credentials: true,
        },
    },


});
