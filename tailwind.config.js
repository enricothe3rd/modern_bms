import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    safelist: [
        // Management module colors - gradient backgrounds
        'from-slate-500', 'to-slate-600',
        'from-blue-500', 'to-blue-600',
        'from-green-500', 'to-green-600',
        'from-emerald-500', 'to-emerald-600',
        'from-orange-500', 'to-orange-600',
        'from-purple-500', 'to-purple-600',
        'from-indigo-500', 'to-indigo-600',
        'from-teal-500', 'to-teal-600',
        'from-cyan-500', 'to-cyan-600',
        'from-pink-500', 'to-pink-600',
        'from-rose-500', 'to-rose-600',
        'from-amber-500', 'to-amber-600',
        'from-lime-500', 'to-lime-600',
        'from-sky-500', 'to-sky-600',
        'from-violet-500', 'to-violet-600',
        
        // Management module colors - text colors
        'text-slate-600', 'text-slate-700',
        'text-blue-600', 'text-blue-700',
        'text-green-600', 'text-green-700',
        'text-emerald-600', 'text-emerald-700',
        'text-orange-600', 'text-orange-700',
        'text-purple-600', 'text-purple-700',
        'text-indigo-600', 'text-indigo-700',
        'text-teal-600', 'text-teal-700',
        'text-cyan-600', 'text-cyan-700',
        'text-pink-600', 'text-pink-700',
        'text-rose-600', 'text-rose-700',
        'text-amber-600', 'text-amber-700',
        'text-lime-600', 'text-lime-700',
        'text-sky-600', 'text-sky-700',
        'text-violet-600', 'text-violet-700',
        
        // Hover states for text colors
        'group-hover:text-slate-600', 'group-hover:text-slate-700',
        'group-hover:text-blue-600', 'group-hover:text-blue-700',
        'group-hover:text-green-600', 'group-hover:text-green-700',
        'group-hover:text-emerald-600', 'group-hover:text-emerald-700',
        'group-hover:text-orange-600', 'group-hover:text-orange-700',
        'group-hover:text-purple-600', 'group-hover:text-purple-700',
        'group-hover:text-indigo-600', 'group-hover:text-indigo-700',
        'group-hover:text-teal-600', 'group-hover:text-teal-700',
        'group-hover:text-cyan-600', 'group-hover:text-cyan-700',
        'group-hover:text-pink-600', 'group-hover:text-pink-700',
        'group-hover:text-rose-600', 'group-hover:text-rose-700',
        'group-hover:text-amber-600', 'group-hover:text-amber-700',
        'group-hover:text-lime-600', 'group-hover:text-lime-700',
        'group-hover:text-sky-600', 'group-hover:text-sky-700',
        'group-hover:text-violet-600', 'group-hover:text-violet-700',
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
