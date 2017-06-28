<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Register all of the routes in the application.
|
*/

Route::get('/',             'PagesController@about' );

Route::post('/pitemp',      'PagesController@pitemp');
Route::post('/pigasflow',   'PagesController@pigasflow');
Route::post('/pilight',     'PagesController@pilight');
Route::post('/piph',        'PagesController@piph');

Route::get('/api',          'ApiController@api');

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

  Route::get('/home',                     'MybioController@index' );
  Route::get('/global',                   'GlobalController@index' );
  Route::get('/single/{id}',              'GlobalController@show' );
  Route::post('/fullchart/{id}',          'GlobalController@formgraph' );
  Route::get('/getjson',                  'GlobalController@getjson' );
  Route::get('/mybio',                    'MybioController@index' );
  Route::get('/mytemperatures/{hrs}',     'MybioController@graph' );
  Route::get('/mytemperatures',           'MybioController@graph' );
  Route::get('/mylightreadings/{hrs}',    'MybioController@graph' );
  Route::get('/mylightreadings',          'MybioController@graph' );
  Route::get('/mygasflows/{hrs}',         'MybioController@graph' );
  Route::get('/mygasflows',               'MybioController@graph' );
  Route::get('/myphreadings/{hrs}',       'MybioController@graph' );
  Route::get('/myphreadings',             'MybioController@graph' );

  Route::get('/password',                 'PasswordController@show' );
  Route::post('/password',                'PasswordController@update' );

  Route::get('/about',                    'PagesController@about' );
  Route::post('/export',                  'ExportController@export' );


  // All the routes below this point are only for admins

  Route::get('/users',                    'UserController@index' );
  Route::get('/users/excel',              'UserController@excel' );
  Route::get('/user/{id}',                'UserController@show' );
  Route::post('/user/{user}',             'UserController@update' );
  Route::get('/user',                     'UserController@create' );
  Route::post('/user',                    'UserController@update' );
  Route::get('/user/delete/{id}',         'UserController@delete' );

  Route::get('/bioreactors',              'BioreactorController@index' );
  Route::get('/bioreactors/excel',        'BioreactorController@excel' );
  Route::get('/bioreactor/{id}',          'BioreactorController@show' );
  Route::post('/bioreactor/{bioreactor}', 'BioreactorController@update' );
  Route::get('/bioreactor',               'BioreactorController@create' );
  Route::post('/bioreactor',              'BioreactorController@update' );
  Route::get('/bioreactor/delete/{id}',   'BioreactorController@delete' );

});
