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

$router->group(['prefix' => 'api/v1'], function () use ($router) {
    $router->get('/books', 'BookController@index');
    $router->get('/books/{id}', 'BookController@show');

    $router->get('/comments', 'CommentController@index');
    $router->post('/comments', 'CommentController@store');
    $router->put('/comments/{id}', 'CommentController@update');
    $router->delete('/comments/{id}', 'CommentController@destroy');
    $router->get('/comments/{id}', 'CommentController@show');

    $router->get('/characters', 'CharacterController@index');
});
