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

$router->post('/feedbacks', ['as' => 'feedbacks.create', 'uses' => 'FeedbacksController@create']);

$router->get('/growers', ['as' => 'growers.get','uses' => 'GrowersController@get']);
$router->get('/growers/{id}', ['as' => 'growers.show','uses' => 'GrowersController@show']);

$router->get('/products', ['as' => 'products.get','uses' => 'ProductsController@get']);
$router->get('/products/{id}', ['as' => 'products.show','uses' => 'ProductsController@show']);
$router->post('/products', ['as' => 'products.create', 'uses' => 'ProductsController@create']);
$router->put('/products/{id}', ['as' => 'products.update','uses' => 'ProductsController@update']);
$router->delete('/products/{id}', ['as' => 'products.delete','uses' => 'ProductsController@delete']);
$router->post('/products/images/{id}', ['as' => 'products.attachImage', 'uses' => 'ProductsController@attachImage']);
$router->get('/products/images/{id}', ['as' => 'products.getImages', 'uses' => 'ProductsController@getImages']);

$router->get('/favorites', ['as' => 'favorites.get','uses' => 'UserFavoritesController@get']);
$router->post('/favorites', ['as' => 'favorites.create', 'uses' => 'UserFavoritesController@create']);
$router->delete('/favorites/{id}', ['as' => 'favorites.delete','uses' => 'UserFavoritesController@delete']);

$router->get('/interests', ['as' => 'interests.get','uses' => 'InterestsController@get']);
$router->get('/interests/compatible', ['as' => 'interests.getCompatible','uses' => 'InterestsController@getCompatible']);
$router->post('/interests', ['as' => 'interests.create', 'uses' => 'InterestsController@create']);
$router->delete('/interests/{id}', ['as' => 'interests.delete','uses' => 'InterestsController@delete']);

$router->get('/addresses', ['as' => 'addresses.get','uses' => 'AddressesController@get']);
$router->get('/addresses/total', ['as' => 'addresses.total','uses' => 'AddressesController@total']);
$router->get('/addresses/{id}', ['as' => 'addresses.show','uses' => 'AddressesController@show']);
$router->post('/addresses', ['as' => 'addresses.create', 'uses' => 'AddressesController@create']);
$router->put('/addresses/{id}', ['as' => 'addresses.update','uses' => 'AddressesController@update']);
$router->delete('/addresses/{id}', ['as' => 'addresses.delete','uses' => 'AddressesController@delete']);
$router->put('/addresses/{id}/set-main', ['as' => 'addresses.seMain','uses' => 'AddressesController@setMain']);

$router->get('/contacts', ['as' => 'contacts.get','uses' => 'ContactsController@get']);
$router->get('/contacts/{id}', ['as' => 'contacts.show','uses' => 'ContactsController@show']);
$router->post('/contacts', ['as' => 'contacts.create', 'uses' => 'ContactsController@create']);
$router->put('/contacts/{id}', ['as' => 'contacts.update','uses' => 'ContactsController@update']);
$router->delete('/contacts/{id}', ['as' => 'contacts.delete','uses' => 'ContactsController@delete']);

$router->group([
    'middleware' => 'jwt.auth:user'
], function () use ($router) {
    $router->put('/users/{id}', [
        'as' => 'users.update',
        'uses' => 'UsersController@update'
    ]);

    $router->put('/growers/{id}', ['as' => 'growers.update','uses' => 'GrowersController@update']);
    $router->post('/growers/images/{id}', ['as' => 'growers.attachImage', 'uses' => 'GrowersController@attachImage']);
    $router->get('/growers/images/{id}', ['as' => 'growers.getImages', 'uses' => 'GrowersController@getImages']);

    $router->post('/images', ['as' => 'images.create', 'uses' => 'ImagesController@create']);
    $router->post('/images-multiple', ['as' => 'images.createMultiple','uses' => 'ImagesController@createMultiple' ]);
    $router->put('/images/{id}', ['as' => 'images.update','uses' => 'ImagesController@update']);
    $router->delete('/images/{id}', ['as' => 'images.delete','uses' => 'ImagesController@delete']);        
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