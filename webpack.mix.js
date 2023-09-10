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

mix.js('dashboard/resources/js/entry/login/index.js', 'dashboard/js/login.js');
mix.sass('dashboard/resources/style/entry/login.scss', 'dashboard/style');

mix.js('dashboard/resources/js/entry/menu/index.js', 'dashboard/js/menu.js').vue();
mix.sass('dashboard/resources/style/entry/menu.scss', 'dashboard/style');

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
