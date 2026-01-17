import preset from './vendor/filament/filament/tailwind.config.preset'

export default {
    presets: [preset],
    content: [
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.js",
        "./resources/js/**/*.vue",
        "./resources/js/**/*.tsx",
        "./app/Filament/**/*.php",
        "./resources/views/filament/**/*.blade.php",
        "./vendor/filament/**/*.blade.php",
    ],
    corePlugins: { preflight: true },
    safelist: [
        { pattern: /(md|lg|xl):p-(.*)/ }, // Include responsive safelist patterns as needed
        // other patterns as required...
    ],
    theme: {
        screen: {
            'xs': '480px',
            'sm': '640px',
            'md': '768px',
            'lg': '1024px',
            'xl': '1280px',
            '2xl': '1536px',
        },
        extend: {
            colors: {
                primary: {
                    50: '#f0fdf4',
                    100: '#dcfce7', 
                    200: '#bbf7d0',
                    300: '#86efac',
                    400: '#4ade80',
                    500: '#06df72', // Your green
                    600: '#05c565',
                    700: '#04a555',
                    800: '#048046',
                    900: '#065f46',
                    950: '#022c22',
                },
                gray: {
                    50: '#eff6ff',
                    100: '#dbeafe',
                    200: '#bfdbfe', 
                    300: '#93c5fd',
                    400: '#60a5fa',
                    500: '#1c398e', // Your blue
                    600: '#1a3480',
                    700: '#172e71',
                    800: '#142862',
                    900: '#112154',
                    950: '#0e1a45',
                },
            },
        },
    },
}