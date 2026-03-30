import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
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
                maroc: {
                    vert: '#006233',
                    'vert-fonce': '#004d28',
                    'vert-clair': '#e8f5ee',
                    rouge: '#C1272D',
                    'rouge-clair': '#fde8e9',
                    or: '#C8A951',
                },
            },
        },
    },

    plugins: [forms],
};
