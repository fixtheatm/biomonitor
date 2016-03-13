<?php

//use App\User;
//use App\Bioreactor;
//use App\Temperature;
//use App\Lightreading;
//use App\Gasflow;

//use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Register all of the routes in the application.
|
*/

Route::get('/',					'PagesController@about' );

Route::post('/pitemp',			'PagesController@pitemp');
Route::post('/pigasflow',		'PagesController@pigasflow');
Route::post('/pilight',			'PagesController@pilight');

Route::get('/addgasflows',		'TestDataController@addgasflows');
Route::get('/addlight',			'TestDataController@addlight');
Route::get('/addtemps',			'TestDataController@addtemps');
Route::get('/addbioreactors',	'TestDataController@addbioreactors');
Route::get('/addusers',			'TestDataController@addusers');


Route::get('/api',				'ApiController@api');

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

    Route::get('/home',					'MybioController@index' ); //   'HomeController@index');
    Route::get('/global',				'GlobalController@index' );
    Route::get('/single/{id}',			'GlobalController@show' );
    Route::get('/getjson',				'GlobalController@getjson' );
    Route::get('/mybio',				'MybioController@index' );
    Route::get('/mytemperatures/{hrs}',	'MytemperaturesController@index' );
    Route::get('/mytemperatures',		'MytemperaturesController@index' );
    Route::get('/mylightreadings/{hrs}','MylightreadingsController@index' );
    Route::get('/mylightreadings',		'MylightreadingsController@index' );
    Route::get('/mygasflows/{hrs}',		'MygasflowsController@index' );
    Route::get('/mygasflows',			'MygasflowsController@index' );

	Route::get('/about',				'PagesController@about' );

});
