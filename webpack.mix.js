const mix = require('laravel-mix');

mix.js('resources/js/app.js', 'public/js')
    .vue()
    .postCss('resources/css/app.css', 'public/css', [require("tailwindcss")])
    .version();

mix.webpackConfig({
    output: {
        chunkFilename: 'js/[name].js?id=[chunkhash]',
    },
});

mix.options({
    terser: {
        parallel: false,
    },
});
