import react from '@vitejs/plugin-react';
import laravel from 'laravel-vite-plugin';
import { defineConfig } from 'vite';
import tsconfigPaths from 'vite-tsconfig-paths';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/backend/js/main.js',
                'resources/backend/css/app.css',
                'resources/backend/website-setup/theme-selection.tsx',
                'resources/backend/website-setup/menu-builder.tsx',
                'resources/backend/website-setup/header-configuration.tsx',
                'resources/backend/website-setup/footer-configuration.tsx',
                'resources/backend/website-setup/landing-pages.tsx',
                'resources/backend/website-setup/floating-chat-configuration.tsx',

                'resources/backend/dashboard/index.tsx',
                'resources/backend/campaign/index.tsx',
                'resources/backend/seller/index.tsx',
                'resources/backend/admin-chat/index.tsx',
                'resources/backend/seller-chat/index.tsx',
                'resources/backend/pos/index.tsx',
                'resources/backend/order/index.tsx',
                'resources/backend/cart-list/index.tsx',
                'resources/backend/return-details/index.tsx',
                'resources/backend/react/pages/index.tsx',

                'resources/frontend/index.tsx',
                'resources/frontend/index.css',

                'resources/installation/index.tsx',
                'resources/installation/index.css',

                'resources/backend/bizana/dashboard/page.tsx',
                'resources/backend/bizana/price-calculator/page.tsx',
                'resources/backend/bizana/daily-expenses/page.tsx',
                'resources/backend/bizana/monthly-expenses/page.tsx',
                'resources/backend/bizana/delivery-partners/page.tsx',
                'resources/backend/bizana/sales-report/page.tsx',
                'resources/backend/bizana/order-profits/page.tsx',

                'resources/backend/purchase-order/index.tsx',
                'resources/backend/support/index.tsx',
                'resources/backend/agent-commissions/index.tsx',
            ],
            refresh: false,
        }),
        react(),
        tsconfigPaths(),
    ],
    server: {
        watch: {
            ignored: ['**/.env*', '**/RouteServiceProvider.php'],
        },
    },
});
