<?php
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
use Illuminate\Http\Request;

$router->get('/', function () use ($router) {
    return response()->json(['version' => '1.0']);
});

$router->post('login', ['uses' => 'AuthController@authenticate']);

// TODO: Remove register route in production
$router->post('register', ['uses' => 'AuthController@register']);

$router->group(
    ['middleware' => 'jwt.auth'],
    function () use ($router) {
        // User Info
        $router->get('user', function (Request $request) {
            return response()->json($request->auth);
        });

        // Flat API
        $router->group([
            'prefix' => '/flat',
        ], function () use ($router) {
            $router->get('/', 'FlatController@index');
            $router->post('/', 'FlatController@store');
            $router->get('/{id:[\d]+}', 'FlatController@show');
            $router->put('/{id:[\d]+}', 'FlatController@update');
            $router->delete('/{id:[\d]+}', 'FlatController@destroy');
        });

        // Lessee API
        $router->group([
            'prefix' => '/lessee',
        ], function () use ($router) {
            $router->get('/', 'LesseeController@index');
            $router->post('/', 'LesseeController@store');
            $router->get('/{id:[\d]+}', 'LesseeController@show');
            $router->put('/{id:[\d]+}', 'LesseeController@update');
            $router->delete('/{id:[\d]+}', 'LesseeController@destroy');
        });

        // Expense API
        $router->group([
            'prefix' => '/expense',
        ], function () use ($router) {
            $router->get('/', 'ExpenseController@index');
            $router->post('/', 'ExpenseController@store');
            $router->get('/{id:[\d]+}', 'ExpenseController@show');
            $router->put('/{id:[\d]+}', 'ExpenseController@update');
            $router->delete('/{id:[\d]+}', 'ExpenseController@destroy');
        });
    }
);
