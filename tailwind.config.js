import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: ['class', '[data-theme="dark"]'],
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['"Plus Jakarta Sans"', ...defaultTheme.fontFamily.sans],
                display: ['"Plus Jakarta Sans"', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Semua narik dari CSS variables — otomatis ikut theme
                bg: 'rgb(var(--bg-primary) / <alpha-value>)',
                'bg-soft': 'rgb(var(--bg-secondary) / <alpha-value>)',
                surface: 'rgb(var(--surface) / <alpha-value>)',
                'surface-hover': 'rgb(var(--surface-hover) / <alpha-value>)',
                'surface-elevated': 'rgb(var(--surface-elevated) / <alpha-value>)',
                border: 'rgb(var(--border) / <alpha-value>)',
                'border-soft': 'rgb(var(--border-soft) / <alpha-value>)',
                'border-strong': 'rgb(var(--border-strong) / <alpha-value>)',
                ink: 'rgb(var(--text-primary) / <alpha-value>)',
                muted: 'rgb(var(--text-secondary) / <alpha-value>)',
                faint: 'rgb(var(--text-muted) / <alpha-value>)',
                brand: 'rgb(var(--brand) / <alpha-value>)',
                'brand-hover': 'rgb(var(--brand-hover) / <alpha-value>)',
                'brand-soft': 'rgb(var(--brand-soft) / <alpha-value>)',
                'brand-strong': 'rgb(var(--brand-strong) / <alpha-value>)',
                accent: 'rgb(var(--accent) / <alpha-value>)',
                'accent-hover': 'rgb(var(--accent-hover) / <alpha-value>)',
                'accent-soft': 'rgb(var(--accent-soft) / <alpha-value>)',
                success: 'rgb(var(--success) / <alpha-value>)',
                'success-soft': 'rgb(var(--success-soft) / <alpha-value>)',
                warning: 'rgb(var(--warning) / <alpha-value>)',
                'warning-soft': 'rgb(var(--warning-soft) / <alpha-value>)',
                danger: 'rgb(var(--danger) / <alpha-value>)',
                'danger-soft': 'rgb(var(--danger-soft) / <alpha-value>)',
                info: 'rgb(var(--info) / <alpha-value>)',
                'info-soft': 'rgb(var(--info-soft) / <alpha-value>)',
            },
            borderRadius: {
                '2xl': '1rem',
                '3xl': '1.5rem',
            },
        },
    },
    plugins: [],
};