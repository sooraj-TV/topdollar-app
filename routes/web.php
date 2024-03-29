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
        $router->post('auth/register', 'AuthController@register');                  // Admin register
        $router->post('device/register', 'ApiController@postRegisterDevice');       // Register devices once app init
        
        $router->post('chat/start', 'ApiController@postChatAssociate');             // Submit "chat with associate" (chat init) 
        $router->get('chat/details/{chat_id}', 'ApiController@getChatDetails');     // Get chat details by chat ID        
        $router->post('chat/accept', 'ApiController@acceptChatAppln');              // accept chat by admin
        $router->post('chat/messages/send', 'ApiController@postChatMessages');      // post chat messages into server
        $router->get('chat/messages/{chat_id}', 'ApiController@getChatMessages');   // get chat messages from server
        $router->get('chat/list/{user_id}', 'ApiController@getChatLists');          // get chat list for admin
        $router->get('chat/requests', 'ApiController@getChatRequests');             // notification list for admin
        $router->post('chat/messages/read', 'ApiController@postReadMessageStatus'); // post chat messages read status
        $router->post('notification/send', 'ApiController@sendBulkNotifications');  // Bulk push notifications
        
        $router->get('ads/list', 'ApiController@getAdBanners');             // ad list 

        $router->post('pushnotif/test', 'ApiController@sendPushNotification_TEST'); // test push notification
        //$router->get('auth/check', ['middleware' => 'auth',"ApiController@authCheck"]);     // Test auth middleware
    //});
});

$router->get('/admin', 'AdminController@index');