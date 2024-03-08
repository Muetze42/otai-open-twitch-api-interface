/** @type {import('tailwindcss').Config} */

export default {
    content: [
        "./resources/app/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            maxWidth: {
                desktop: '40rem', // 640px
                // content: '96rem', // max-w-screen-2xl 1536px
                content: '80rem', // max-w-screen-xl 1280px
                // content: '64rem', // max-w-screen-lg 1024px
            },
            screens: {
                desktop: '45rem',
            },
            padding: {
                content: '1rem',
            },
            colors: {
                primary: {
                    50: 'var(--primary-50)',
                    100: 'var(--primary-100)',
                    200: 'var(--primary-200)',
                    300: 'var(--primary-300)',
                    400: 'var(--primary-400)',
                    500: 'var(--primary-500)',
                    600: 'var(--primary-600)',
                    700: 'var(--primary-700)',
                    800: 'var(--primary-800)',
                    900: 'var(--primary-900)',
                    950: 'var(--primary-950)',
                    text: 'var(--primary-text)',
                    '300/50': 'var(--primary-300-50)',
                    '500/50': 'var(--primary-500-50)',
                    '800/50': 'var(--primary-800-50)',
                    '900/50': 'var(--primary-900-50)',
                },
                link: 'var(--link)',
            },
            transitionDuration: {
                '250': '250ms',
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0'},
                    '100%': { opacity: '1'}
                }
            },
            animation: {
                fade: 'fadeIn 500ms ease-in',
            }
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
    ],
}
