const resolve = require('path').resolve;
const webpack = require('webpack');
const webpackNotifier = require('webpack-notifier');
const webpackShellPlugin = require('webpack-shell-plugin-next');

module.exports = {
    resolve: {
        alias: {
            '@': resolve(__dirname, 'resources/js'),
            '@dashboard': resolve(__dirname, 'dashboard/resources/js'),
            'vue$': 'vue/dist/vue.esm-bundler.js',
        }
    },
    plugins: [
        new webpackShellPlugin({
            onBuildStart: {
                scripts: [
                    'php artisan ziggy:generate resources/js/routes.js',
                ],
                parallel: true,
            },
            onBuildEnd: [],
        }),
        new webpackNotifier({
            title: JSON.stringify(process.env.APP_NAME),
            contentImage: resolve(__dirname, 'dashboard/resources/img/logo-square.png'),
            alwaysNotify: true,
        }),
        // new webpack.DefinePlugin({
        //     __VUE_OPTIONS_API__: false,
        //     __VUE_PROD_DEVTOOLS__: false,
        // }),
        // [
        //     'import',
        //     {
        //         libraryName: 'element-plus',
        //         customStyleName: (name) => {
        //             name = name.slice(3)
        //             return `element-plus/packages/theme-chalk/src/${name}.scss`;
        //         },
        //     },
        // ],
    ],
};
