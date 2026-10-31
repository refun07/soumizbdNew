import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import tsconfigPaths from 'vite-tsconfig-paths';

// The Laravel plugin disables Vite public files; this isolated preview serves them directly.
export default defineConfig({
    plugins: [react(), tsconfigPaths()],
    publicDir: 'public',
    server: { host: '127.0.0.1', port: 5178, strictPort: true },
});
