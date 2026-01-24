import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: '#284961',
                secondary: '#4C86B3',
                accent: '#B35F12',
                // 'border' ya existe en tailwind como borderColor, mejor no sobrescribir 'border' como color genérico si no es necesario, usaremos gray-200 (#E5E7EB) que es el estándar.
                // Pero podemos definirlo como 'custom-border' si quieres.
                'custom-border': '#E5E7EB',
                'custom-text': '#6B7280',
            },
        },
    },

    plugins: [forms, typography],
};
