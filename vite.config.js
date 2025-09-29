import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/assets/frontend/css/styles.css',
                'resources/assets/frontend/js/script.js',
                'resources/assets/frontend/css/information.css',
                'resources/assets/frontend/css/advanced-search.css',
                'resources/assets/frontend/js/advanced-search.js'
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        host: 'heoluoitruyen.local',
        port: 5173,
        strictPort: true,
        cors: true,
        hmr: {
            host: 'heoluoitruyen.local',
            protocol: 'http',
            port: 5173,
        },
    },
});
