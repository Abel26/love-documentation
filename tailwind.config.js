const defaultTheme = require('tailwindcss/defaultTheme');

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                love: {
                    50: '#FDF5F2',
                    100: '#FCE8E0',
                    200: '#F8D0C2',
                    300: '#F2B09A',
                    400: '#EA8669',
                    500: '#D96B4F',
                    600: '#C4553D',
                    700: '#A64636',
                    800: '#8B3D31',
                    900: '#75352D',
                    950: '#4E1F1A',
                },
                brown: {
                    soft: '#D4A574',
                    light: '#E8C9A0',
                    medium: '#C4956A',
                    dark: '#8B6F47',
                },
            },
        },
    },

    plugins: [require('@tailwindcss/forms')],
};
