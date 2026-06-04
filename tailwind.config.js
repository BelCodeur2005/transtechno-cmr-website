import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.tsx',
    ],

    theme: {
        extend: {
            // ─── Charte "Clinical Precision" — Transtechno Cameroun ───
            colors: {
                primary:           '#ba0013',
                'primary-container': '#e31e24',
                'brand-red':       '#ED1C24',
                'navy-blue':       '#004A80',
                secondary:         '#3a6090',
                tertiary:          '#006099',
                surface:           '#f9f9f9',
                background:        '#f9f9f9',
                'surface-1':       '#ffffff',
                'surface-2':       '#f4f3f3',
                'surface-3':       '#eeeeee',
                'surface-4':       '#e8e8e8',
                'surface-5':       '#e2e2e2',
                'outline-variant': '#e2e2e2',
                'dark-navy':       '#002B4B',
            },

            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },

            fontSize: {
                'headline-xl': ['48px', { fontWeight: '700', letterSpacing: '-0.02em' }],
                'headline-lg': ['32px', { fontWeight: '700', letterSpacing: '-0.01em' }],
                'headline-md': ['24px', { fontWeight: '600' }],
                'body-lg':     ['18px', { lineHeight: '1.6',  fontWeight: '400' }],
                'body-md':     ['16px', { lineHeight: '1.5',  fontWeight: '400' }],
                'body-sm':     ['14px', { lineHeight: '1.4',  fontWeight: '400' }],
                'label-caps':  ['12px', { fontWeight: '600',  letterSpacing: '0.05em' }],
            },

            spacing: {
                xs:  '8px',
                sm:  '16px',
                md:  '24px',
                lg:  '48px',
                xl:  '80px',
            },

            borderRadius: {
                DEFAULT: '0.125rem',
                lg:      '0.25rem',
                xl:      '0.5rem',
            },

            maxWidth: {
                '7xl': '1280px',
            },

            padding: {
                margin: '32px',
            },
        },
    },

    plugins: [forms],
};
