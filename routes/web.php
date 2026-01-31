<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
| Public & Protected API Routes
|--------------------------------------------------------------------------
*/

// Default route
$router->get('/', function () use ($router) {
    return $router->app->version();
});

/*
|--------------------------------------------------------------------------
| AUTHENTICATION (PUBLIC)
|--------------------------------------------------------------------------
*/
$router->post('/register', 'AuthController@register');
$router->post('/login', 'AuthController@login');

/*
|--------------------------------------------------------------------------
| PUBLIC API (NO AUTH REQUIRED)
|--------------------------------------------------------------------------
*/
$router->get('/categories', 'CategoryController@index');
$router->get('/products', 'ProductController@index');
$router->get('/public/products', 'ProductController@publicIndex');
$router->get('/orders/{id}', 'OrderController@show');

/*
|--------------------------------------------------------------------------
| PROTECTED API (AUTH REQUIRED)
|--------------------------------------------------------------------------
*/
$router->group(['middleware' => 'auth'], function () use ($router) {

    // Order
    $router->post('/orders', 'OrderController@store');

    /*
       |--------------------------------------------------------------------------
       | ADMIN ONLY
       |--------------------------------------------------------------------------
       */
    $router->group(['middleware' => 'admin'], function () use ($router) {

        // Category
        $router->post('/categories', 'CategoryController@store');

        // Product
        $router->post('/products', 'ProductController@store');
    });
});