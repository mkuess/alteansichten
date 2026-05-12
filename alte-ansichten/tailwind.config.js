import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/filament/**/*.blade.php',
        './vendor/filament/**/*.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
        './app/Filament/**/*.php',
    ],
    safelist: [
        'grid-cols-1',
        'grid-cols-2',
        'grid-cols-3',
        'grid-cols-4',
        'sm:grid-cols-2',
        'lg:grid-cols-4',
        'col-span-1',
        'col-span-2',
        'col-span-full',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans:  ['Geist', ...defaultTheme.fontFamily.sans],
                serif: ['Newsreader', ...defaultTheme.fontFamily.serif],
                mono:  ['JetBrains Mono', ...defaultTheme.fontFamily.mono],
            },
            colors: {
                brand: {
                    bg:           '#f7f6f3',
                    panel:        '#f0ede6',
                    card:         '#ffffff',
                    ink:          '#1c1a17',
                    'ink-2':      '#45413a',
                    'ink-3':      '#8a857b',
                    line:         '#e2ddd4',
                    'line-2':     '#ece8de',
                    accent:       '#6b7f56',
                    'accent-bg':  '#edf2e8',
                    'accent-ink': '#3f5234',
                },
            },
            borderRadius: {
                'card': '12px',
                'btn':  '8px',
                'pill': '999px',
            },
        },
    },
    plugins: [],
};
