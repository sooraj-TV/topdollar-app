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

$router->get('/', function () use ($router) {
    //return $router->app->version();
    echo "<h4>Invalid Access!</h4>";
});

$router->group(['prefix' => 'api'], function () use ($router) {
    //$router->group(['prefix' => 'v1'], function () use ($router) {
        $router->get('store-location/list', "ApiController@getStoreLocations");     // Get store location
        $router->get('categories/list', "ApiController@getCategories");             // Get categories and filters
        $router->post('get-quote/add', "ApiController@addGetQuoteAppln");           // Post "get a quote"
        $router->post('auth/login', 'AuthController@postLogin');                    // Admin login
        $router->post('device/register', 'ApiController@postRegisterDevice');       // Register devices once app init
        $router->post('chat/start', 'ApiController@postChatAssociate');             // Submit "chat with associate" (chat init) 

        $router->post('pushnotif/test', 'ApiController@sendPushNotification_TEST');

    //});
});
