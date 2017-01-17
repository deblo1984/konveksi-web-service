<?php

use Dingo\Api\Routing\Router;

/** @var Router $api */
$api = app(Router::class);

$api->version('v1', function (Router $api) {
    $api->group(['prefix' => 'auth'], function(Router $api) {
        $api->post('signup', 'App\\Api\\V1\\Controllers\\SignUpController@signUp');
        $api->post('login', 'App\\Api\\V1\\Controllers\\LoginController@login');

        $api->post('recovery', 'App\\Api\\V1\\Controllers\\ForgotPasswordController@sendResetEmail');
        $api->post('reset', 'App\\Api\\V1\\Controllers\\ResetPasswordController@resetPassword');
    });

    $api->group(['middleware' => 'jwt.auth'], function(Router $api) {
        $api->get('/refresh', [
            'middleware' => 'jwt.refresh',
            function() {
                return response()->json([
                    'message' => 'By accessing this endpoint, you can refresh your access token at each request. Check out this response headers!'
                ]);
            }
        ]);
        $api->get('', 'App\\Api\\V1\\Controllers\\MainController@index');
    });
    $api->group(['middleware' => 'api.auth'], function ($api) {
        $api->post('/project', 'App\\Api\\V1\\Controllers\\ProjectController@store');
        $api->get('/project', 'App\\Api\\V1\\Controllers\\ProjectController@getAll');
        $api->delete('/project/{id}', 'App\\Api\\V1\\Controllers\\ProjectController@destroy');
        $api->post('/project/upload/{id}', 'App\\Api\\V1\\Controllers\\ProjectController@upload');
        $api->get('/project/status/{status}', 'App\\Api\\V1\\Controllers\\ProjectController@getByStatus');
    });
        //user controller route
        $api->post('/user', 'App\\Api\\V1\\Controllers\\UserController@register');
        $api->get('/user', 'App\\Api\\V1\\Controllers\\ProjectController@getAll');
        $api->delete('/user/{id}', 'App\\Api\\V1\\Controllers\\ProjectController@destroy');
        $api->post('/user/upload/{id}', 'App\\Api\\V1\\Controllers\\ProjectController@upload');
        $api->get('/user/test', 'App\\Api\\V1\\Controllers\\UserController@getAngka');

    //admin user controller route
    $api->group(['prefix' => 'admin'], function(Router $api) {
        $api->post('/user/signup', 'App\\Api\\V1\\Controllers\\Admin\\AdminController@signUp');
        $api->post('/user/login', 'App\\Api\\V1\\Controllers\\Admin\\AdminController@login');

    });

    $api->group(['prefix' => 'admin', 'middleware' => 'api.auth'], function (Router $api) {
        $api->post('/project', 'App\\Api\\V1\\Controllers\\Admin\\AdminProjectController@store');
        $api->get('/project', 'App\\Api\\V1\\Controllers\\Admin\\AdminProjectController@getAll');
        $api->delete('/project/{id}', 'App\\Api\\V1\\Controllers\\Admin\\AdminProjectController@destroy');
        $api->get('/project/image/{id}', 'App\\Api\\V1\\Controllers\\Admin\\AdminProjectController@getImageById');
        $api->post('/project/upload/{id}', 'App\\Api\\V1\\Controllers\\Admin\\AdminProjectController@upload');
        $api->get('/project/status/{status}', 'App\\Api\\V1\\Controllers\\Admin\\AdminProjectController@getByStatus');
        $api->put('/project/finish/{id}', 'App\\Api\\V1\\Controllers\\Admin\\AdminProjectController@finishProject');
        $api->get('/project/finish/{status}', 'App\\Api\\V1\\Controllers\\Admin\\AdminProjectController@getByFinishStatus');
        $api->put('/project/process/{id}', 'App\\Api\\V1\\Controllers\\Admin\\AdminProjectController@acceptProject');

        //admin user role
        $api->delete('/user/{id}', 'App\\Api\\V1\\Controllers\\Admin\\AdminController@destroy');
        $api->post('/user/upload/{id}', 'App\\Api\\V1\\Controllers\\Admin\\AdminController@upload');
        $api->get('/user', 'App\\Api\\V1\\Controllers\\Admin\\AdminController@show');
        $api->post('/user', 'App\\Api\\V1\\Controllers\\Admin\\AdminController@store');
        $api->get('/user/{id}', 'App\\Api\\V1\\Controllers\\Admin\\AdminController@getById');
        $api->get('/user/{id}', 'App\\Api\\V1\\Controllers\\Admin\\AdminController@getById');

        //customer
        $api->post('/customer', 'App\\Api\\V1\\Controllers\\Admin\\AdminUserController@register');
        $api->post('/customer/upload/{id}', 'App\\Api\\V1\\Controllers\\Admin\\AdminUserController@upload');
        $api->delete('/customer/{id}', 'App\\Api\\V1\\Controllers\\Admin\\AdminUserController@destroy');
        $api->get('/customer', 'App\\Api\\V1\\Controllers\\Admin\\AdminUserController@show');
        $api->get('/customer/dropdown', 'App\\Api\\V1\\Controllers\\Admin\\AdminUserController@userDropDown');
        $api->get('/customer/{id}', 'App\\Api\\V1\\Controllers\\Admin\\AdminUserController@getById');


        //work types
        $api->post('/works', 'App\\Api\\V1\\Controllers\\Admin\\WorkTypeController@store');
        $api->delete('/works/{id}', 'App\\Api\\V1\\Controllers\\Admin\\WorkTypeController@destroy');
        $api->get('/works', 'App\\Api\\V1\\Controllers\\Admin\\WorkTypeController@show');
        $api->get('/works/{id}', 'App\\Api\\V1\\Controllers\\Admin\\WorkTypeController@getById');
        $api->put('/works/{id}', 'App\\Api\\V1\\Controllers\\Admin\\WorkTypeController@update');

        //project details
        $api->post('/projectdetails', 'App\\Api\\V1\\Controllers\\Admin\\ProjectDetailController@store');
        $api->delete('/projectdetails/{id}', 'App\\Api\\V1\\Controllers\\Admin\\ProjectDetailController@destroy');
        $api->get('/projectdetails/{id}', 'App\\Api\\V1\\Controllers\\Admin\\ProjectDetailController@getByProject');
        $api->get('/projectdetails/getbyid/{id}', 'App\\Api\\V1\\Controllers\\Admin\\ProjectDetailController@getById');

        //project progress
        $api->post('/projectprogress/', 'App\\Api\\V1\\Controllers\\Admin\\ProjectProgressController@store');
        $api->delete('/projectprogress/{id}', 'App\\Api\\V1\\Controllers\\Admin\\ProjectProgressController@destroy');
        $api->get('/projectprogress/{id}', 'App\\Api\\V1\\Controllers\\Admin\\ProjectProgressController@getByProject');

        //Bulletins
        $api->post('/bulletin', 'App\\Api\\V1\\Controllers\\Admin\\BulletinController@store');
        $api->get('/bulletin', 'App\\Api\\V1\\Controllers\\Admin\\BulletinController@show');
        $api->delete('/bulletin/{id}', 'App\\Api\\V1\\Controllers\\Admin\\BulletinController@destroy');
        $api->put('/bulletin/{id}', 'App\\Api\\V1\\Controllers\\Admin\\BulletinController@update');
        $api->get('/bulletin/{id}', 'App\\Api\\V1\\Controllers\\Admin\\BulletinController@getById');

    });

});
