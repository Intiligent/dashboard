const mix = require('laravel-mix');
const webpackConfig = require('./webpack.config');

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
}).webpackConfig(webpackConfig);

mix.setPublicPath('public');
mix.extract(['vue', 'element-plus', 'axios']);
mix.copy('dashboard/resources/fonts', 'public/dashboard/fonts');
mix.copy('dashboard/resources/img', 'public/dashboard/img');
// login
mix.js('dashboard/resources/js/entry/login/index.js', 'dashboard/js/login.js');
mix.sass('dashboard/resources/style/entry/login.scss', 'dashboard/style');
// home
mix.js('dashboard/resources/js/entry/home/index.js', 'dashboard/js/home.js').vue();
// menu
mix.js('dashboard/resources/js/entry/menu/index.js', 'dashboard/js/menu.js').vue();
// user list
mix.js('dashboard/resources/js/entry/user-list/index.js', 'dashboard/js/user-list.js').vue();
// user item
mix.js('dashboard/resources/js/entry/user-item/index.js', 'dashboard/js/user-item.js').vue();
// article list
mix.js('dashboard/resources/js/entry/article-list/index.js', 'dashboard/js/article-list.js').vue();
// article item
mix.js('dashboard/resources/js/entry/article-item/index.js', 'dashboard/js/article-item.js').vue();
// settings
mix.js('dashboard/resources/js/entry/settings/index.js', 'dashboard/js/settings.js').vue();

mix.version();

// mix.browserSync({
//     watch: false,
//     proxy: 'http://v3.dashboard.loc',
//     files: [
//         'dashboard/resources/**/*.blade.php',
//         'dashboard/resources/**/*.js',
//         'dashboard/resources/**/*.vue',
//         'dashboard/resources/**/*.scss',
//     ],
// });

// mix.disableSuccessNotifications();
