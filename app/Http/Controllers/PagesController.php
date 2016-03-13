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


class PagesController extends Controller
{
	public function about() {	// resources/views/pages/about.blade.php

	    return view('Pages.about', ['route' => 'about',
		                            'header_title'	=> 'About BioMonitor']);	
	}

	/**
	*  Process multiple json temperature records arriving from
	*   the Raspberrry Pi. Would normally expect 1 at a time (??)
	*
	* @param json data [{"deviceid": "00002","temperature": 24.1,"recorded_on": "2016-02-22 21:30:00"},
    *                   {"deviceid": "00002","temperature": 24.3,"recorded_on": "2016-02-22 21:35:00"}]
	*
	*/
	public function pitemp(Request $request) {
	
		$json_data = $request->json()->all();

		//echo(sizeof($arr));
		//die();

		//var_dump($arr);

		// datestamp the incoming data records	
		$now = Carbon::now();

		foreach( $json_data as $data) {
			$temperature = new Temperature();
			$temperature->deviceid = $data['deviceid'];
			$temperature->temperature = $data['temperature'];
			$temperature->recorded_on =  $data['recorded_on'];
			$temperature->created_at = $now->toDateTimeString();
			$temperature->updated_at = $now->toDateTimeString();
			$temperature->save();
		}


		//$deviceid = $arr[0]['deviceid'];
		//$temperature = $arr[0]['temperature'];

		return Response::json( array('additions' => ['records'=>sizeof($json_data)] , 200 ) );
	}

	/**
	*  Process multiple json gasflow records arriving from
	*   the Raspberrry Pi. Would normally expect 1 at a time (??)
	*
	* @param json data [{"deviceid": "00002","flow": 24.1,"recorded_on": "2016-02-22 21:30:00"},
    *                   {"deviceid": "00002","flow": 24.3,"recorded_on": "2016-02-22 21:35:00"}]
	*
	*/
	public function pigasflow(Request $request) {
	
		$json_data = $request->json()->all();

		//echo(sizeof($arr));
		//die();

		//var_dump($arr);

		// datestamp the incoming data records	
		$now = Carbon::now();

		foreach( $json_data as $data) {
			$gasflow = new Gasflow();
			$gasflow->deviceid = $data['deviceid'];
			$gasflow->flow = $data['flow'];
			$gasflow->recorded_on =  $data['recorded_on'];
			$gasflow->created_at = $now->toDateTimeString();
			$gasflow->updated_at = $now->toDateTimeString();
			$gasflow->save();
		}


		//$deviceid = $arr[0]['deviceid'];
		//$temperature = $arr[0]['flow'];

		return Response::json( array('additions' => ['records'=>sizeof($json_data)] , 200 ) );
	}

	/**
	*  Process multiple json light records arriving from
	*   the Raspberrry Pi. Would normally expect 1 at a time (??)
	*
	* @param json data [{"deviceid": "00002","lux": 300,"recorded_on": "2016-02-22 21:30:00"},
    *                   {"deviceid": "00002","lux": 275,"recorded_on": "2016-02-22 21:35:00"}]
	*
	*/
	public function pilight(Request $request) {
	
		$json_data = $request->json()->all();

		//echo(sizeof($arr));
		//die();

		//var_dump($arr);

		// datestamp the incoming data records	
		$now = Carbon::now();

		foreach( $json_data as $data) {
			$lightreading = new Lightreading();
			$lightreading->deviceid = $data['deviceid'];
			$lightreading->lux = $data['lux'];
			$lightreading->recorded_on =  $data['recorded_on'];
			$lightreading->created_at = $now->toDateTimeString();
			$lightreading->updated_at = $now->toDateTimeString();
			$lightreading->save();
		}


		//$deviceid = $arr[0]['deviceid'];
		//$temperature = $arr[0]['temperature'];

		return Response::json( array('additions' => ['records'=>sizeof($json_data)] , 200 ) );
	}


}