import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],

    server: {
        cors: true,
    },

    build: {
        chunkSizeWarningLimit: 1000, // naikkan limit dari 500KB → 1MB (opsional)

        rollupOptions: {
            output: {
                /**
                 * 🔥 AUTO SPLIT NODE_MODULES MENJADI CHUNK TERPISAH
                 * Contoh:
                 * - axios → axios.js
                 * - lodash → lodash.js
                 * - pusher-js → pusher-js.js
                 * Sangat efektif untuk Laravel + Livewire + PWA
                 */
                manualChunks(id) {
                    if (id.includes('node_modules')) {
                        return id
                            .toString()
                            .split('node_modules/')[1]
                            .split('/')[0]
                            .toString();
                    }
                },
            },
        },
    },
});
