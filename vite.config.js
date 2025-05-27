import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    build: {
        outDir: 'public/build',
        manifest: true,
        manifestFileName: 'manifest.json',
        rollupOptions: {
            input: ['resources/js/app.js', 'resources/css/app.css',],
        },
        emptyOutDir: true,
    },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            buildDirectory: 'build', // 👈 important!
        }),
    ],
});
