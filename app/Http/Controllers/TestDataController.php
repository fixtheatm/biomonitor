<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Bioreactor;
use App\Temperature;
use App\Gasflow;
use App\Lightreading;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Response;
use Carbon\Carbon;


class TestDataController extends Controller
{
	public function addgasflows() {
	
		dd('uncomment for testing addgasflows');

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
	}

	public function addlight() {

		dd('uncomment for testing addlight');

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
	}

	public function addtemps() {

		dd('uncomment for testing addtemps');

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
	}

	public function addbioreactors() {

		dd('uncomment for testing addbioreactors');

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
	}

	public function addusers() {
		dd('uncomment for testing addusers');

		$user = new User();

		$user->name = "Fred Jones";
		$user->email = "fj@solarbiocells.com";
		$user->password = bcrypt('123456');
		$user->deviceid = "00001";
		$user->save();

		$user = new User();
		$user->name = "Glen Sharp";
		$user->email = "gs@solarbiocells.com";
		$user->password = bcrypt('123456');
		$user->deviceid = "00002";
		$user->save();

		$user = new User();
		$user->name = "Christine Sharp";
		$user->email = "cs@solarbiocells.com";
		$user->password = bcrypt('123456');
		$user->deviceid = "00002";
		$user->save();

		$user = new User();
		$user->name = "Lucy Petrella";
		$user->email = "lp@solarbiocells.com";
		$user->password = bcrypt('123456');
		$user->deviceid = "00002";
		$user->save();
	}

}

