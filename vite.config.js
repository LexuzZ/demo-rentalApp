import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";
import preset from "./vendor/filament/support/tailwind.config.preset";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/js/app.js",

            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
