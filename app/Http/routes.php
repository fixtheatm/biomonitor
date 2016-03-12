<?php

use App\User;
use App\Bioreactor;
use App\Temperature;
use App\Lightreading;
use App\Gasflow;

//use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'PagesController@about' );


Route::post('/pitemp', 'PagesController@pitemp');
Route::post('/pigasflow', 'PagesController@pigasflow');
Route::post('/pilight', 'PagesController@pilight');


Route::get('/addgasflows', function () {
	
	dd('uncomment for testing');

    $format = 'Y-m-d H:i:s';

	$dt1 = DateTime::createFromFormat($format, '2016-02-15 12:16:17');


	$readings=[0.23,0.24,0.21,0.15,0.0,0.13,0.20,0.23,0.27,];
	$min=0;
	foreach ( $readings as $reading) {
	
		$dt = DateTime::createFromFormat($format, '2016-02-15 15:'.sprintf('%02d', $min).':00' );
		$min += 5;

		$gasflow = new Gasflow();
		$gasflow->deviceid = "00002";
		$gasflow->flow= $reading;
		$gasflow->recorded_on = $dt;
		$gasflow->created_at = $dt1;
		$gasflow->updated_at = $dt1;
		$gasflow->save();
	}

});


Route::get('/addlight', function () {
	
	dd('uncomment for testing');

    $format = 'Y-m-d H:i:s';

	$dt1 = DateTime::createFromFormat($format, '2016-02-15 12:16:17');


	$readings=[200.0,250.0,210.0,212.0,500.0,180.0,430.0,435.0,500.0,285.0];
	$min=0;
	foreach ( $readings as $reading) {
	
		$dt = DateTime::createFromFormat($format, '2016-02-15 15:'.sprintf('%02d', $min).':00' );
		$min += 5;

		$light = new Lightreading();
		$light->deviceid = "00002";
		$light->lux= $reading;
		$light->recorded_on = $dt;
		$light->created_at = $dt1;
		$light->updated_at = $dt1;
		$light->save();
	}

});



Route::get('/addtemps', function () {

	dd('uncomment for testing');

    $format = 'Y-m-d H:i:s';

	$dt1 = DateTime::createFromFormat($format, '2016-02-15 12:16:17');


	$readings=[20.0,25.0,21.0,21.2,23.0,23.5,21.6,22.8,22.6,20.0,25.0,21.0,21.2,23.0,23.5,21.6,22.8,22.6];
	$min=0;
	foreach ( $readings as $reading) {
	
		$dt = DateTime::createFromFormat($format, '2016-02-15 15:'.sprintf('%02d', $min).':00' );
		$min += 5;

		$temperature = new Temperature();
		$temperature->deviceid = "00002";
		$light->temperature= $reading;
		$temperature->recorded_on = $dt;
		$temperature->created_at = $dt1;
		$temperature->updated_at = $dt1;
		$temperature->save();
	}
});


// creating test data
Route::get('/addbioreactors', function () {

	dd('uncomment for testing');

	$bioreactor = new Bioreactor();
	$bioreactor->name = "St Paul High School";
	$bioreactor->city = "Niagara Falls";
	$bioreactor->country = "Canada";
	$bioreactor->email = "gs@abc.com";
	$bioreactor->deviceid = "00001";
	$bioreactor->latitude = 43.1167;
	$bioreactor->longitude = -79.0667;

	$bioreactor->save();

	$bioreactor = new Bioreactor();
	$bioreactor->name = "Elmira High School";
	$bioreactor->city = "Elmira, NY";
	$bioreactor->country = "USA";
	$bioreactor->email = "em@abc.com";
	$bioreactor->deviceid = "00002";
	$bioreactor->latitude = 42.0853;
	$bioreactor->longitude = -76.8092;
	$bioreactor->save();

	$bioreactor = new Bioreactor();
	$bioreactor->name = "University of Calgary";
	$bioreactor->city = "Calgary";
	$bioreactor->country = "Canada";
	$bioreactor->email = "ce@abc.com";
	$bioreactor->deviceid = "00003";
	$bioreactor->latitude = 51.079948;
	$bioreactor->longitude = -114.125534;
	$bioreactor->save();


});

Route::get('/addusers', function () {

	//dd('uncomment for testing');

	$user = new User();

	$user->name = "Fred Jones";
	$user->email = "fj@abc.com";
	$user->password = bcrypt('123456');
	$user->deviceid = "00001";
	$user->save();

	$user = new User();
	$user->name = "Glen Sharp";
	$user->email = "gs@abc.com";
	$user->password = bcrypt('123456');
	$user->deviceid = "00002";
	$user->save();

	$user = new User();
	$user->name = "Christine Sharp";
	$user->email = "cs@abc.com";
	$user->password = bcrypt('123456');
	$user->deviceid = "00003";
	$user->save();

	$user = new User();
	$user->name = "Lucy Petrella";
	$user->email = "lp@abc.com";
	$user->password = bcrypt('123456');
	$user->deviceid = "00002";
	$user->save();
});

/**
*
* @example http://laravel.dev/api?dtype=temp
**/

Route::get('/api', function () {
	$temp_data = array( [
		'deviceid'	=> '34234', 
		'temp'		=>	'21.012', 
		'date'		=>	'2016-01-23 12:34:00'
		],
		[
		'deviceid'	=> '34234', 
		'temp'		=>	'21.112', 
		'date'		=>	'2016-01-23 12:35:00'
		]
		);

	$light_data = array( [
		'deviceid'	=> '34234', 
		'light'		=>	'21.012', 
		'date'		=>	'2016-01-23 12:34:00'
		],
		[
		'deviceid'	=> '34234', 
		'light'		=>	'21.112', 
		'date'		=>	'2016-01-23 12:35:00'
		]
		);

	$flow_data = array( [
		'deviceid'	=> '34234', 
		'flow'		=>	'21.012', 
		'date'		=>	'2016-01-23 12:34:00'
		],
		[
		'deviceid'	=> '34234', 
		'flow'		=>	'21.112', 
		'date'		=>	'2016-01-23 12:35:00'
		]
		);
		

		//dd (Request::input('dtype'));

		//dd($request->all());

	$dtype = Request::input('dtype');


	switch( $dtype) {
		case 'temp':
			return $temp_data;
			break;
		case 'light':
			return $light_data;
			break;
		case 'flow':
			return $flow_data;
			break;
		default:
			return "Error";
			break;
	}
});


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
