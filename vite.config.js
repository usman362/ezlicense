import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
                'resources/js/find-instructor.js',
                'resources/js/find-instructor-results.js',
                'resources/js/instructor-calendar.js',
                'resources/js/instructor-settings-profile.js',
                'resources/js/instructor-settings-vehicle.js',
                'resources/js/instructor-settings-pricing.js',
                'resources/js/instructor-settings-service-area.js',
                'resources/js/instructor-settings-opening-hours.js',
                'resources/js/instructor-settings-calendar.js',
                'resources/js/instructor-settings-documents.js',
                'resources/js/instructor-settings-banking.js',
            ],
            refresh: true,
        }),
    ],
});
