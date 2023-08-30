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
mix.extract(['vue', 'element-plus']);

mix.js('dashboard/resources/js/entry/login/index.js', 'dashboard/js/login.js');
mix.sass('dashboard/resources/style/entry/login.scss', 'dashboard/style');

mix.js('dashboard/resources/js/entry/menu/index.js', 'dashboard/js/menu.js').vue();
mix.sass('dashboard/resources/style/entry/menu.scss', 'dashboard/style');

mix.version();
