import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
                'resources/css/app.css',
                'resources/js/pages/index.js',
                'resources/js/pages/gallery.js',
                'resources/js/pages/share.js',
                'resources/js/pages/event_password.js',
                'resources/js/bootstrap.js'
            ],
            refresh: true,
        }),
    ],
});
