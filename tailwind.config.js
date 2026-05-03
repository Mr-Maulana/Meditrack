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
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                tni: {
                    50: '#f2f6f2',
                    100: '#e1ede1',
                    200: '#c5dac5',
                    300: '#9bc09d',
                    400: '#6ca06f',
                    500: '#48824c', // Primary TNI Green
                    600: '#356839',
                    700: '#2c5330', // Darker TNI Green
                    800: '#254328', // Hospital Dark Green
                    900: '#1f3722',
                },
                gold: {
                    400: '#FCD34D',
                    500: '#F59E0B', // Accent Gold
                    600: '#D97706',
                }
            },
            animation: {
                'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                'fade-in': 'fadeIn 0.5s ease-out',
                'slide-up': 'slideUp 0.4s ease-out',
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                slideUp: {
                    '0%': { transform: 'translateY(10px)', opacity: '0' },
                    '100%': { transform: 'translateY(0)', opacity: '1' },
                }
            }
        },
    },

    plugins: [forms],
};
