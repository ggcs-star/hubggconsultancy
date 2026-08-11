/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
            },
            colors: {
                brand: {
                    50: '#f5f3ff',
                    100: '#ede9fe',
                    200: '#ddd6fe',
                    300: '#c4b5fd',
                    400: '#a78bfa',
                    500: '#8b5cf6',
                    600: '#7c3aed',
                    700: '#6d28d9',
                    800: '#5b21b6',
                    900: '#4c1d95',
                },
                // Semantic tokens used by the course-builder views —
                // aliases over the existing brand/slate palette so those
                // views don't need a second, parallel design system.
                primary: {
                    DEFAULT: '#7c3aed',
                    dark: '#6d28d9',
                    light: '#ede9fe',
                },
                secondary: {
                    DEFAULT: '#475569',
                    dark: '#1e293b',
                    light: '#f1f5f9',
                },
                success: {
                    DEFAULT: '#059669',
                    light: '#d1fae5',
                },
                danger: {
                    DEFAULT: '#dc2626',
                    light: '#fee2e2',
                },
                warning: {
                    DEFAULT: '#d97706',
                    light: '#fef3c7',
                },
                'app-border': '#e2e8f0',
                'surface-alt': '#f8fafc',
                'chart-4': '#0ea5e9',
            },
        },
    },
    plugins: [],
}
