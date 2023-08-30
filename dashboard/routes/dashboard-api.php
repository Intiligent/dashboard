<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('api/dashboard')->as('api.dashboard.')->group(function() {
    // settings
    Route::group(['prefix' => 'setting'], function() {
        Route::post('item', 'SettingsController@postSetting')->name('postSetting');
        Route::post('group', 'SettingsController@postSettingGroup')->name('postSettingGroup');
        Route::put('form', 'SettingsController@putSettings')->name('putSettings');
        Route::delete('item', 'SettingsController@deleteSetting')->name('deleteSetting');
        Route::delete('group', 'SettingsController@deleteSettingGroup')->name('deleteSettingGroup');
    });
    // menu
    Route::group(['prefix' => 'menu'], function() {
        Route::post('item', 'MenuController@postMenu')->name('postMenu');
        Route::post('group', 'MenuController@postMenuGroup')->name('postMenuGroup');
        Route::put('order', 'MenuController@putMenuOrder')->name('putMenuOrder');
        Route::delete('item', 'MenuController@deleteMenu')->name('deleteMenu');
        Route::delete('group', 'MenuController@deleteMenuGroup')->name('deleteMenuGroup');
    });
    // user
    Route::group(['prefix' => 'user'], function() {
        Route::post('item', 'UserController@postUser')->name('postUser');
        Route::get('fetch', 'UserController@getUsers')->name('getUsers');
    });
    // acl role
    Route::group(['prefix' => 'role'], function() {
        Route::post('role', 'RoleController@postRole')->name('postRole');
        Route::delete('role', 'RoleController@deleteRole')->name('deleteRole');
    });
    // acl permission
    Route::group(['prefix' => 'permission'], function() {
        Route::post('permission', 'PermissionController@postPermission')->name('postPermission');
        Route::delete('permission', 'PermissionController@deletePermission')->name('deletePermission');
    });
    // article
    Route::group(['prefix' => 'article'], function() {
        Route::get('fetch', 'ArticleController@getArticles')->name('getArticles');
        Route::post('item', 'ArticleController@postArticle')->name('postArticle');
        Route::post('duplicate', 'ArticleController@postArticleDuplicate')->name('postArticleDuplicate');
        Route::delete('item', 'ArticleController@deleteArticle')->name('deleteArticle');
    });
    // media
    Route::group(['prefix' => 'media'], function() {
        Route::get('library', 'MediaController@getMediaLibrary')->name('getMediaLibrary');
        Route::get('gallery', 'MediaController@getMedia')->name('getMedia');
        Route::post('files', 'MediaController@postMediaUploadFiles')->name('postMediaUploadFiles');
        Route::post('uploadfromurl', 'MediaController@postMediaUploadFromUrl')->name('postMediaUploadFromUrl');
        Route::post('set', 'MediaController@postSetMedia')->name('postSetMedia');
        Route::post('edit', 'MediaController@postMediaEdit')->name('postMediaEdit');
        Route::put('meta', 'MediaController@putMediaMeta')->name('putMediaMeta');
        Route::put('gallery/order', 'MediaController@putGalleryOrder')->name('putGalleryOrder');
        Route::delete('item', 'MediaController@deleteMedia')->name('deleteMedia');
    });
});
