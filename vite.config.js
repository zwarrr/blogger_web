import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/js/app.js', 
                'resources/js/animations-fixed.js', 
                'resources/js/loading-fix.js',
                'resources/js/visit-camera-system.js',
                'resources/js/visit-modal-manager.js'
            ],
            refresh: true,
        }),
    ],
});
