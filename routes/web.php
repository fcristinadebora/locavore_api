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

$router->get('/product-categories', [
    'as' => 'productCategories.get',
    'uses' => 'ProductCategoriesController@get'
]);

$router->get('/growers/{id}', [
    'as' => 'growers.show',
    'uses' => 'GrowersController@show'
]);

$router->get('/products', ['as' => 'products.get','uses' => 'ProductsController@get']);
$router->get('/products/{id}', ['as' => 'products.show','uses' => 'ProductsController@show']);
$router->post('/products', ['as' => 'products.create', 'uses' => 'ProductsController@create']);
$router->put('/products/{id}', ['as' => 'products.update','uses' => 'ProductsController@update']);
$router->delete('/products/{id}', ['as' => 'products.delete','uses' => 'ProductsController@delete']);

$router->group([
    'middleware' => 'jwt.auth:user'
], function () use ($router) {
    $router->put('/users/{id}', [
        'as' => 'users.update',
        'uses' => 'UsersController@update'
    ]);

    $router->put('/growers/{id}', [
        'as' => 'growers.update',
        'uses' => 'GrowersController@update'
    ]);

    $router->post('/images', [
        'as' => 'images.create',
        'uses' => 'ImagesController@create'
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