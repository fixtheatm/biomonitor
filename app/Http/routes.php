<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Register all of the routes in the application.
|
*/

Route::get('/',          'PagesController@about' );

Route::post('/pitemp',      'PagesController@pitemp');
Route::post('/pigasflow',    'PagesController@pigasflow');
Route::post('/pilight',      'PagesController@pilight');
Route::post('/piph',      'PagesController@piph');

// Following routes are for testing only: can be removed for production
// Route::get('/addgasflows',  'TestDataController@addgasflows');
// Route::get('/addlight',      'TestDataController@addlight');
// Route::get('/addtemps',      'TestDataController@addtemps');
// Route::get('/addph',        'TestDataController@addph');
// Route::get('/addbioreactors',  'TestDataController@addbioreactors');
// Route::get('/addusers',      'TestDataController@addusers');


Route::get('/api',        'ApiController@api');


/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => 'web'], function () {
    Route::auth();

    Route::get('/home',          'MybioController@index' ); //   'HomeController@index');
    Route::get('/global',        'GlobalController@index' );
    Route::get('/single/{id}',  'GlobalController@show' );
    Route::get('/getjson',      'GlobalController@getjson' );
    Route::get('/mybio',        'MybioController@index' );
    Route::get('/mytemperatures/{hrs}',  'MytemperaturesController@index' );
    Route::get('/mytemperatures',    'MytemperaturesController@index' );
    Route::get('/mylightreadings/{hrs}','MylightreadingsController@index' );
    Route::get('/mylightreadings',    'MylightreadingsController@index' );
    Route::get('/mygasflows/{hrs}',    'MygasflowsController@index' );
    Route::get('/mygasflows',    'MygasflowsController@index' );
    Route::get('/myphreadings/{hrs}',  'MyphreadingsController@index' );
    Route::get('/myphreadings',  'MyphreadingsController@index' );

    Route::get('/password',      'PasswordController@show' );
    Route::post('/password',    'PasswordController@update' );

    Route::get('/about',        'PagesController@about' );
    Route::post('/export',      'ExportController@export' );


  // All the routes below this point are only for admins

    Route::get('/users',        'UserController@index' );
    Route::get('/users/excel',  'UserController@excel' );
    Route::get('/user/{id}',    'UserController@show' );
    Route::post('/user/{user}',  'UserController@update' );
    Route::get('/user',          'UserController@create' );
    Route::post('/user',        'UserController@update' );
    Route::get('/user/delete/{id}',  'UserController@delete' );

    Route::get('/bioreactors',        'BioreactorController@index' );
    Route::get('/bioreactors/excel',  'BioreactorController@excel' );
    Route::get('/bioreactor/{id}',    'BioreactorController@show' );
    Route::post('/bioreactor/{bioreactor}',  'BioreactorController@update' );
    Route::get('/bioreactor',          'BioreactorController@create' );
    Route::post('/bioreactor',        'BioreactorController@update' );
    Route::get('/bioreactor/delete/{id}',  'BioreactorController@delete' );

});
