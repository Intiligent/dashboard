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
                '@dashboard': resolve('dashboard/resources/script'),
            }
        },
        plugins: [
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
            new webpackNotifier({
                title: JSON.stringify(process.env.APP_NAME),
                contentImage: path.join(__dirname, 'dashboard/resources/img/logo-square.png'),
                alwaysNotify: true,
            }),
            new webpack.NormalModuleReplacementPlugin(/element-ui[\/\\]lib[\/\\]locale[\/\\]lang[\/\\]zh-CN/, 'element-ui/lib/locale/lang/ru-RU')
        ]
    }
});

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
// mix.copyDirectory('resources/fonts', 'public/fonts');
// mix.copyDirectory('resources/img', 'public/img');
mix.copyDirectory('dashboard/resources/img', 'public/dashboard-panel/img');

// home
// mix.js('resources/js/entry/index.js', 'js').vue()
    // .sass('resources/style/entry/index.scss', 'style');

/*
|--------------------------------------------------------------------------
| Dashboard entry
|--------------------------------------------------------------------------
|
| Scripts and styles
|
*/

// login
mix.sass('dashboard/resources/style/entry/login.scss', 'dashboard-panel/style');
// dashboard
mix.js('dashboard/resources/script/entry/dashboard.js', 'dashboard-panel/script').vue()
    .sass('dashboard/resources/style/entry/dashboard.scss', 'dashboard-panel/style');
// menu
mix.js('dashboard/resources/script/entry/menu.js', 'dashboard-panel/script').vue()
    .sass('dashboard/resources/style/entry/menu.scss', 'dashboard-panel/style');
// settings
mix.js('dashboard/resources/script/entry/settings.js', 'dashboard-panel/script').vue()
    .sass('dashboard/resources/style/entry/settings.scss', 'dashboard-panel/style');
// user
mix.js('dashboard/resources/script/entry/user.js', 'dashboard-panel/script').vue()
    .sass('dashboard/resources/style/entry/user.scss', 'dashboard-panel/style');
// role
mix.js('dashboard/resources/script/entry/role.js', 'dashboard-panel/script').vue()
    .sass('dashboard/resources/style/entry/role.scss', 'dashboard-panel/style');
// permission
mix.js('dashboard/resources/script/entry/permission.js', 'dashboard-panel/script').vue()
    .sass('dashboard/resources/style/entry/permission.scss', 'dashboard-panel/style');
// article
mix.js('dashboard/resources/script/entry/article.js', 'dashboard-panel/script').vue()
    .sass('dashboard/resources/style/entry/article.scss', 'dashboard-panel/style');

mix.version();
