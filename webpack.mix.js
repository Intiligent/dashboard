const mix = require('laravel-mix');
const path = require('path');
const webpackNotifier = require('webpack-notifier');
const webpackShellPlugin = require('webpack-shell-plugin-next');

function resolve(dir) {
    return path.join(__dirname, '', dir);
}

/*
|--------------------------------------------------------------------------
| Mix Setting
|--------------------------------------------------------------------------
|
| Some project setting
|
*/

mix.options({
    terser: {
        extractComments: false,
    }
}).webpackConfig((webpack) => {
    return {
        resolve: {
            alias: {
                '@': resolve('resources/js'),
                '@dashboard': resolve('dashboard/resources/js'),
            }
        },
        plugins: [
            // запуск shell команды перед/после сборкой
            new webpackShellPlugin({
                onBuildStart: {
                    scripts: [
                        'php artisan ziggy:generate resources/js/routes.js',
                    ],
                    parallel: true,
                    // blocking: true,
                },
                onBuildEnd: [],
            }),
            // уведомления о сборке
            new webpackNotifier({
                title: 'Vintage',
                contentImage: path.join(__dirname, 'dashboard/resources/img/logo-square.png'),
                alwaysNotify: true, // показывать уведомления для watch
            }),
            new webpack.NormalModuleReplacementPlugin(/element-ui[\/\\]lib[\/\\]locale[\/\\]lang[\/\\]zh-CN/, 'element-ui/lib/locale/lang/ru-RU')
        ]
    }
});

// mix.autoload({
//     jquery: ['$', 'window.jQuery', 'jQuery'], // more than one
//     moment: 'moment' // only one
// });

/*
|--------------------------------------------------------------------------
| Mix Asset Management
|--------------------------------------------------------------------------
|
| Mix provides a clean, fluent API for defining some Webpack build steps
| for your Laravel application. By default, we are compiling the Sass
| file for the application as well as bundling up all the JS files.
|
*/

mix.setPublicPath('public');
mix.extract(['vue', 'axios', 'lodash']);

// copy files
mix.copy('resources/js/routes.js', 'public/js');

// copy directories
mix.copyDirectory('resources/fonts', 'public/fonts');
mix.copyDirectory('resources/img', 'public/img');

// process script for public (face)
// home
mix.js('resources/js/entry/index.js', 'js').vue()
    .sass('resources/style/entry/index.scss', 'style');
// category
mix.js('resources/js/entry/category.js', 'js').vue()
    .sass('resources/style/entry/category.scss', 'style');
// product
mix.js('resources/js/entry/product.js', 'js').vue()
    .sass('resources/style/entry/product.scss', 'style');

/*
|--------------------------------------------------------------------------
| Dashboard entry
|--------------------------------------------------------------------------
|
| Scripts and styles
|
*/

// login
mix.sass('dashboard/resources/style/entry/login.scss', 'dashboard/style');
// dashboard
mix.js('dashboard/resources/js/entry/dashboard.js', 'dashboard/script').vue()
    .sass('dashboard/resources/style/entry/dashboard.scss', 'dashboard/style');
// menu
mix.js('dashboard/resources/js/entry/menu.js', 'dashboard/script').vue()
    .sass('dashboard/resources/style/entry/menu.scss', 'dashboard/style');
// settings
mix.js('dashboard/resources/js/entry/settings.js', 'dashboard/script').vue()
    .sass('dashboard/resources/style/entry/settings.scss', 'dashboard/style');
// user
mix.js('dashboard/resources/js/entry/user.js', 'dashboard/script').vue()
    .sass('dashboard/resources/style/entry/user.scss', 'dashboard/style');
// article
mix.js('dashboard/resources/js/entry/article.js', 'dashboard/script').vue()
    .sass('dashboard/resources/style/entry/article.scss', 'dashboard/style');
// product
mix.js('dashboard/resources/js/entry/product.js', 'dashboard/script').vue()
    .sass('dashboard/resources/style/entry/product.scss', 'dashboard/style');
// catalog
mix.js('dashboard/resources/js/entry/catalog.js', 'dashboard/script').vue()
    .sass('dashboard/resources/style/entry/catalog.scss', 'dashboard/style');
// order
mix.js('dashboard/resources/js/entry/order.js', 'dashboard/script').vue()
    .sass('dashboard/resources/style/entry/order.scss', 'dashboard/style');

mix.version();
