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
            fontFamily: {
                inter: ['Inter', 'ui-sans-serif', 'system-ui'],
                figtree: ['Figtree', 'ui-sans-serif', 'system-ui'],
                merriweather: ['Merriweather', 'serif'],
                roboto: ['Roboto', 'ui-sans-serif', 'system-ui'],
            },
        },
    },

    plugins: [forms],
};
