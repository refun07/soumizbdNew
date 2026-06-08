/** @type {import('tailwindcss').Config} */
module.exports = {
    darkMode: 'class',
    future: {
        hoverOnlyWhenSupported: true,
    },
    content: [
        './resources/**/*.blade.php',
        './app/**/*.php',
        './resources/**/*.(js|jsx|ts|tsx)',
    ],
    safelist: [{ pattern: /button--.*/ }],
    theme: {
        extend: {
            colors: {
                primary: {
                    DEFAULT: 'rgb(var(--primary) / <alpha-value>)',
                    foreground:
                        'rgb(var(--primary-foreground) / <alpha-value>)',
                },
                secondary: {
                    DEFAULT: 'rgb(var(--secondary) / <alpha-value>)',
                    foreground:
                        'rgb(var(--secondary-foreground) / <alpha-value>)',
                    light: 'rgb(var(--secondary-light) / <alpha-value>)',
                },

                'theme-alert': 'rgb(var(--theme-alert) / <alpha-value>)', // #ee5858
                'theme-orange': 'rgb(var(--theme-orange) / <alpha-value>)', // #EE7739
                'theme-green': 'rgb(var(--theme-green) / <alpha-value>)', // #31AF2E
                'theme-yellow': 'rgb(var(--theme-yellow) / <alpha-value>)', // #FFD34D

                // dark / light mood
                muted: 'rgb(var(--muted) / <alpha-value>)',

                foreground: 'rgb(var(--foreground) / <alpha-value>)',
                'foreground-invert':
                    'rgb(var(--foreground-invert) / <alpha-value>)',

                background: 'rgb(var(--background) / <alpha-value>)',
                'background-invert':
                    'rgb(var(--background-invert) / <alpha-value>)',
                'background-hover':
                    'rgb(var(--background-hover) / <alpha-value>)',
                'background-active':
                    'rgb(var(--background-active) / <alpha-value>)',
                'background-primary-light':
                    'rgb(var(--background-primary-light) / <alpha-value>)',

                popover: 'rgb(var(--popover) / <alpha-value>)',
                'popover-foreground':
                    'rgb(var(--popover-foreground) / <alpha-value>)',

                'background-primary-light-invert':
                    'rgb(var(--background-primary-light-invert) / <alpha-value>)',

                border: 'rgb(var(--border) / <alpha-value>)',
                'border-hover': 'rgb(var(--border-hover) / <alpha-value>)',

                card: 'rgb(var(--card) / <alpha-value>)',
                'card-foreground':
                    'rgb(var(--card-foreground) / <alpha-value>)',
            },
            fontFamily: {
                poppins: ['Poppins', 'sans-serif'],
                inter: ['Inter', 'sans-serif'],
                'public-sans': ['Public Sans', 'sans-serif'],
                sacramento: ['Sacramento', 'cursive'],
            },
            screens: {
                xxs: '400px',
                xs: '490px',
                '3xl': '1600px',
                '4xl': '1850px',
            },
            boxShadow: {
                theme: '0 3px 10px 0 var(--shadow-color)',
            },
            gridTemplateColumns: {
                14: 'repeat(14, minmax(0, 1fr))',
            },
            borderColor: {
                'primary-14': 'rgb(var(--primary) / 0.14)',
            },
            backgroundColor: {
                'badge-success': '#1D9679',
                'badge-danger': '#FF0404',
                'badge-processing': '#FF7E06',
                'badge-confirmed': '#74C1FD',
                'badge-shipped': '#AEB109',
                'badge-default': '#1F84D2',
            },
            width: {
                'container-xxs': 'var(--container-xxs)',
                'container-sm': 'var(--container-sm)',
                'container-md': 'var(--container-md)',
                'container-lg': 'var(--container-lg)',
                'container-xl': 'var(--container-xl)',
                'container-2xl': 'var(--container-2xl)',
                'container-3xl': 'var(--container-3xl)',
            },
            container: {
                center: true,
                padding: '14px',
                screens: {
                    xl: '1204px',
                    '3xl': '1628px',
                },
            },
            transitionTimingFunction: {
                'in-expo': 'cubic-bezier(0.95, 0.05, 0.795, 0.035)',
                'out-expo': 'cubic-bezier(0.19, 1, 0.22, 1)',
            },
            borderRadius: {
                banner: '10px',
                card: '5px',
                button: '3px',
            },
        },
    },
    plugins: [],
};
