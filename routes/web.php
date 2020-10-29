<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->post('/register', [
    'as' => 'auth.register',
    'uses' => 'AuthController@register'
]);

$router->post('/login', [
    'as' => 'auth.login',
    'uses' => 'AuthController@login'
]);

$router->get('/tags', [
    'as' => 'tags.get',
    'uses' => 'TagsController@get'
]);

$router->get('/growers/{id}/', [
    'as' => 'growers.show',
    'uses' => 'GrowersController@show'
]);

$router->group([
    'middleware' => 'jwt.auth:user'
], function () use ($router) {
    $router->put('/users/{id}/', [
        'as' => 'users.update',
        'uses' => 'UsersController@update'
    ]);

    $router->put('/growers/{id}/', [
        'as' => 'growers.update',
        'uses' => 'GrowersController@update'
    ]);
});

$router->group([
    'middleware' => 'jwt.auth:public'
], function () use ($router) {
    $router->get('/authenticated', [
        'as' => 'auth.authenticated',
        'uses' => 'AuthController@authenticated'
    ]);
});

$router->group([
    'middleware' => 'jwt.auth:user'
], function () use ($router) {
    $router->get('/profile', [
        'as' => 'users.profile',
        'uses' => 'UsersController@profile'
    ]);
});