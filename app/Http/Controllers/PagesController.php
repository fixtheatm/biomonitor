<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Bioreactor;
use App\Temperature;
use App\Gasflow;
use App\Lightreading;
use App\Phreading;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Response;
use Carbon\Carbon;


class PagesController extends Controller
{
  public function about() {  // resources/views/pages/about.blade.php

    return view('Pages.about', ['route' => 'about',
      'header_title'  => 'About BioMonitor']);
  }

  /**
  * Update the Biorector record for the give deviceid. Set the
  *  last_datasync_at column to the passed in datetime
  *
  * @param $deviceid the device id of the bioreactor from the json data
    * @param $now the carbon datetime stamp used for the temp,gas or light records
  *
  */
  public function updateBioreactorLastDatasync($deviceid, $now) {

    // load the record from the table
    try {
      $bioreactor = Bioreactor::where('deviceid', '=', $deviceid)->firstOrFail();
    }
    catch (\Exception $e) {
      return;
    }

    $bioreactor->last_datasync_at = $now->toDateTimeString();

    $bioreactor->save();

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

    // there will be only 1 deviceid in the package of records

    $deviceid='';

    foreach( $json_data as $data) {
      $deviceid = $data['deviceid'];

      $temperature = new Temperature();
      $temperature->deviceid = $deviceid;
      $temperature->temperature = $data['temperature'];
      $temperature->recorded_on =  $data['recorded_on'];
      $temperature->created_at = $now->toDateTimeString();
      $temperature->updated_at = $now->toDateTimeString();
      $temperature->save();
    }

    // just in case the data package was damaged or empty
    if ($deviceid != '') {
      $this->updateBioreactorLastDatasync($deviceid, $now);
    }


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

    // there will be only 1 deviceid in the package of records

    $deviceid='';

    foreach( $json_data as $data) {
      $deviceid = $data['deviceid'];

      $gasflow = new Gasflow();
      $gasflow->deviceid = $deviceid;
      $gasflow->flow = $data['flow'];
      $gasflow->recorded_on =  $data['recorded_on'];
      $gasflow->created_at = $now->toDateTimeString();
      $gasflow->updated_at = $now->toDateTimeString();
      $gasflow->save();
    }


    // just in case the data package was damaged or empty
    if ($deviceid != '') {
      $this->updateBioreactorLastDatasync($deviceid, $now);
    }

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

    // there will be only 1 deviceid in the package of records

    $deviceid='';

    foreach( $json_data as $data) {
      $deviceid = $data['deviceid'];

      $lightreading = new Lightreading();
      $lightreading->deviceid = $deviceid;
      $lightreading->lux = $data['lux'];
      $lightreading->recorded_on =  $data['recorded_on'];
      $lightreading->created_at = $now->toDateTimeString();
      $lightreading->updated_at = $now->toDateTimeString();
      $lightreading->save();
    }

    // just in case the data package was damaged or empty
    if ($deviceid != '') {
      $this->updateBioreactorLastDatasync($deviceid, $now);
    }

    return Response::json( array('additions' => ['records'=>sizeof($json_data)] , 200 ) );
  }

  /**
  *  Process multiple json ph records arriving from
  *   the Raspberrry Pi. Would normally expect 1 at a time (??)
  *
  * @param json data [{"deviceid": "00002","ph": 7.1,"recorded_on": "2016-02-22 21:30:00"},
  *                   {"deviceid": "00002","ph": 7.2,"recorded_on": "2016-02-22 21:35:00"}]
  *
  */
  public function piph(Request $request) {

    $json_data = $request->json()->all();

    // datestamp the incoming data records
    $now = Carbon::now();

    // there will be only 1 deviceid in the package of records

    $deviceid='';

    foreach( $json_data as $data) {
      $deviceid = $data['deviceid'];

      $phreading = new Phreading();
      $phreading->deviceid = $deviceid;
      $phreading->ph = $data['ph'];
      $phreading->recorded_on =  $data['recorded_on'];
      $phreading->created_at = $now->toDateTimeString();
      $phreading->updated_at = $now->toDateTimeString();
      $phreading->save();
    }

    // just in case the data package was damaged or empty
    if ($deviceid != '') {
      $this->updateBioreactorLastDatasync($deviceid, $now);
    }

    return Response::json( array('additions' => ['records'=>sizeof($json_data)] , 200 ) );
  }

}
