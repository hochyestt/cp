import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            fontFamily: {
                // добавляем шрифт Tinos
                tinos: ['Tinos', ...defaultTheme.fontFamily.serif],
            },
        },
    },
    plugins: [forms],
}
