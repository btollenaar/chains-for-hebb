import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/notification.js',
                'resources/js/cart.js',
                'resources/js/pages/home.js',
            ],
            refresh: true,
        }),
    ],
});
