import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';
import tailwindcss from '@tailwindcss/vite'
import basicSsl from '@vitejs/plugin-basic-ssl'

export default defineConfig({

    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources/js'),
        },
    },
    server: {
        // host: true, // equivalent to 0.0.0.0
        // port: 5174,
        // strictPort: true,
        // https: true,
        host: 'localhost',
        port: 5174,
        strictPort: true,
    },
    preview: {
        host: true,
        port: 5174,
        strictPort: true,
        https: true,
    },
    build: {
        target: 'esnext',
        commonjsOptions: {
            transformMixedEsModules: true,
        },
    },
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
        // basicSsl()
    ],
});
