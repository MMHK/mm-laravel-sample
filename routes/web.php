<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'HomeController@home')
    ->name('default.home');

/**
 * Admin
 */
Route::group([
    'prefix' => 'admin',
    'namespace' => 'Admin',
    'middleware' => [
        'json.result'
    ] ], function (){

    /**
     * Admin Sign In
     */
    Route::get('login', 'AuthController@signIn')
        ->name('admin.sign-in');
    Route::post('login', 'AuthController@handleSignIn');
    Route::get('logout', 'AuthController@handleSignOut')
        ->name('admin.logout');

    /**
     * Admin API
     */
    Route::group([
        'prefix' => 'api',
        'namespace' => 'Api',
        'middleware' => [
            'json.result'
        ]], function (){

        Route::post('/upload/save', 'UploadController@save')
            ->name('admin.api.upload.save');
    });


    Route::group(['middleware' => ['admin.auth']], function () {
        Route::get('', 'HomeController@dashboard')
            ->name('admin.dashboard');

        Route::get('user', 'UserController@index')
            ->name('admin.user');
    });

    Route::get('demo', 'HomeController@demo')
        ->name('admin.demo');
});

/**
 * Swagger
 */
Route::group([
    'prefix' => 'swagger',
    'namespace' => 'Api',
], function () {
    Route::get('/', 'SwaggerController@ui')
        ->name('swagger');
    Route::get('/json', 'SwaggerController@urls')
        ->name('swagger.urls');
    Route::get('/admin.json', 'SwaggerController@admin')
        ->name('swagger.admin.doc');
    Route::get('/front.json', 'SwaggerController@front')
        ->name('swagger.front.doc');
});
