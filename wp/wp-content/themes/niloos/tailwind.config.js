const _ = require("lodash");
const theme = require('./theme.json');
const tailpress = require("@jeffreyvr/tailwindcss-tailpress");

module.exports = {
    content: [
        './*.php',
        './**/*.php',
        '../../plugins/NlsHunter/*.php',
        '../../plugins/NlsHunter/**/*.php',
        './resources/css/*.css',
        './resources/js/*.js',
        './resources/wp-editor.php',
        './safelist.txt'
    ],
    theme: {
        container: {
            padding: {
                DEFAULT: '1rem',
                sm: '2rem',
                lg: '0rem'
            },
        },
        extend: {
            colors: tailpress.colorMapper(tailpress.theme('settings.color.palette', theme)),
            fontSize: tailpress.fontSizeMapper(tailpress.theme('settings.typography.fontSizes', theme)),
            minWidth: {
                flow: '200px'
            },
            fontSize: {
                h1: '2.75rem',
                h2: '2.5rem',
                mh1: '1.375rem',
                mh2: '1.25rem'
            }
        },
        screens: {
            //'xs': '480px',
            //'sm': '600px',
            'md': '782px',
            'lg': '1366px',
            //'lg': tailpress.theme('settings.layout.contentSize', theme),
            //'xl': tailpress.theme('settings.layout.wideSize', theme),
            //'2xl': '1440px'
        }
    },
    plugins: [
        tailpress.tailwind
    ]
};
