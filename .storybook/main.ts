import path from 'path';
import { fileURLToPath } from 'url';
import tailwindcss from '@tailwindcss/vite';
import type { StorybookConfig } from '@storybook/react-vite';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const config: StorybookConfig = {
    stories: ['../resources/js/**/*.stories.@(ts|tsx)'],
    framework: {
        name: '@storybook/react-vite',
        options: {},
    },
    viteFinal: async (viteConfig) => {
        // Add Tailwind CSS v4 plugin
        viteConfig.plugins = [...(viteConfig.plugins ?? []), tailwindcss()];

        // Add @/* path alias mapping to resources/js/*
        const existingAlias =
            viteConfig.resolve?.alias && !Array.isArray(viteConfig.resolve.alias)
                ? viteConfig.resolve.alias
                : {};

        viteConfig.resolve = {
            ...viteConfig.resolve,
            alias: {
                ...existingAlias,
                '@': path.resolve(__dirname, '../resources/js'),
            },
        };

        return viteConfig;
    },
};

export default config;
