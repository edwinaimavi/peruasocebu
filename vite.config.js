import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/css/public-home.css',
                'resources/js/app.js',
                'resources/js/pages/owner.js',
                'resources/js/pages/ranch.js',
                'resources/js/pages/veterinarian.js',
                'resources/js/pages/breed.js',
                'resources/js/pages/blog-post.js',
                'resources/js/pages/contact-message.js',
                'resources/js/pages/cattle.js',
                'resources/js/pages/cattle-genealogy.js',
                'resources/js/pages/ownership-history.js',
                'resources/js/pages/cattle-sale.js',
                'resources/js/pages/certificate.js',
                'resources/js/pages/certificate-signature.js',
                'resources/js/pages/veterinary-record.js',
                'resources/js/pages/vaccination.js',
                'resources/js/pages/treatment.js',
                'resources/js/pages/weight-record.js',
                'resources/js/pages/reproduction-record.js',
            ],
            refresh: true,
        }),
    ],
});
