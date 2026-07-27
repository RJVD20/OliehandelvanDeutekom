import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                turbo: {
                    navy: '#03182B',
                    dark: '#08243D',
                    blue: '#123A5A',
                    gold: '#D9A42E',
                    'gold-light': '#F0BA3C',
                    white: '#F7F8FA',
                    gray: '#E9EDF2',
                    ink: '#10263D',
                },
            },
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                heading: ['Manrope', 'Inter', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
