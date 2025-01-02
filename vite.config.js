import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import vueJsx from "@vitejs/plugin-vue-jsx";
import dotenv from "dotenv";
import path from "path";

// Load environment variables
dotenv.config({ path: "./.env" });

export default defineConfig({
    plugins: [
        // Laravel Vite Plugin configuration
        laravel({
            input: [
                "resources/js/app.js", // Main JavaScript entry point
                "resources/css/app.css", // Main CSS file
                "resources/frontend/css/style.css", // Additional CSS if necessary
            ],
            refresh: true, // Enable automatic page reload on changes
        }),

        // Vue plugin for handling .vue files
        vue(),

        // Vue JSX plugin for handling JSX/TSX syntax
        vueJsx(),
    ],
    define: {
        // Ensure process.env variables are available in the client-side code
        "process.env": JSON.stringify(process.env),
    },
    resolve: {
        alias: {
            ziggy: path.resolve("resources/js/ziggy.js"), // Ziggy for named routes
            '@': '/resources/js',
        },
    },
    server: {
        hmr: {
            host: "localhost", // Use localhost for Hot Module Replacement
        },
        headers: {
            "Access-Control-Allow-Origin": "*", // Allow CORS for development
        },
    },
    build: {
        outDir: "public/build", // Output directory for build artifacts
        rollupOptions: {
            output: {
                // Optimize chunking for better build performance
                manualChunks: undefined,
            },
        },
    },
});
