import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import flowbite from 'flowbite/plugin'

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
        "./node_modules/flowbite/**/*.js"
    ],

    theme: {
        extend: {
            fontFamily: {
                'silk-serif': ['"Silk Serif TRIAL"', 'serif'],
                'sora': ['Sora', 'sans-serif'],
            },
            colors: {
                primary: '#F8B26A',
                secondary: '#81562A',
            },
        },
    },

    plugins: [
        forms,
        flowbite,
    ],
};
