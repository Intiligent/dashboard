<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::as('dashboard.')->prefix('dashboard')->group(function() {
    // auth
    Route::get('/login', 'LoginController@showLoginForm')->name('login');
    Route::post('/login', 'LoginController@postLogin')->name('postLogin');
    Route::get('/logout', 'LoginController@logout')->name('logout');

    Route::group(['middleware' => ['auth:dashboard']], function() {
        // menu
        Route::get('/menu', 'MenuController@index')->name('menu');
        // settings
        Route::get('/settings', 'SettingsController@index')->name('settings');
        // article
        Route::group(['prefix' => 'article'], function() {
            Route::get('/', 'ArticleController@showArticleList')->name('articleList');
            Route::get('/{id}', 'ArticleController@showArticlePage')->name('article');
        });
        // user
        Route::group(['prefix' => 'user'], function() {
            Route::get('/', 'UserController@showList')->name('userList');
            Route::get('/{id}', 'UserController@showItem')->name('user');
        });
        // acl role
        Route::group(['prefix' => 'roles'], function() {
            Route::get('/', 'RoleController@index')->name('roles');
            Route::get('/{item}', 'RoleController@item')->name('role');
        });
        // acl permission
        Route::group(['prefix' => 'permissions'], function() {
            Route::get('/', 'PermissionController@index')->name('permissions');
            Route::get('/{item}', 'PermissionController@item')->name('permission');
        });
        // home page
        Route::get('/', 'DashboardController@index')->name('home');
    });
});
