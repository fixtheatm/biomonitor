<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Bioreactor;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Response;
use Lang;

use Carbon\Carbon;


class GlobalController extends Controller
{
  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {
  }

  /**
   * return all bioreactors as JSON
   *
   * @return json containing information about all bioreactors
   */
  public function getjson()
  {
    // get all the bioreactors to show on the map
    $bioreactors = Bioreactor::all();

    return Response::json( array('markers' => $bioreactors->toArray() , 200 ));
  }

  /**
   * Show all bioreactors. Default view is the map of the world.
   *
   * The user can also view them in a list form
   *
   * @TODO Add filter to get rid of inactive Bioreactors
   */
  public function index()
  {
    // get all the bioreactors to show on the map
    $bioreactors = Bioreactor::all();
    //dd($bioreactors->toJson());

    return view( 'Global.index', [
      'route'      => 'global',
      'dbdata'     => $bioreactors
    ]);
  }


  /**
   * Show current sensor graphs for a single bioreactor
   *
   * Selected from the global map or from the global list
   *
   * @param string $id = deviceid of the bioreactor ex. 00001
   */
  public function show( $id )
  {
    $bioreactor = $this->getBioreactorFromId( $id );

    // For each sensor, get the associated data from the database for this
    // location.  Convert the data to the format needed by the Chart.js library.
    // x holds time labels (hh:mm), y holds the data

    // The datetime of the last (most recent) record is returned, or the current
    // datetime, if no records .
    $end_datetime = $this->getTemperatureData( $id );
    $temp_axis_data = $this->_buildXYTemperatureData(); // Degrees Celsius

    $this->getLightreadingData( $id );
    $light_axis_data = $this->_buildXYLightreadingData(); // lux as nnnnn.n

    $this->getGasflowData( $id );
    $gasflow_axis_data = $this->_buildXYGasflowData(); // milliliters/minute

    $this->getPhreadingData( $id );
    $ph_axis_data = $this->_buildXYPhreadingData(); // pH

    $view_end_time = $end_datetime->toDateTimeString(); // locale specific?
    $sensor_ref = [
      'gasflow'         => [
        'name' => 'gasflow',
        'title' => Lang::get('bioreactor.gasflow_title' ),
        'end_datetime' => $view_end_time,
        'x_data'    => $gasflow_axis_data['x_data'],
        'y_data'    => $gasflow_axis_data['y_data'],
      ],
      'light'           => [
        'name' => 'light',
        'title' => Lang::get('bioreactor.light_title' ),
        'end_datetime' => $view_end_time,
        'x_data'    => $light_axis_data['x_data'],
        'y_data'    => $light_axis_data['y_data'],
      ],
      'temperature'     => [
        'name' => 'temp',
        'title' => Lang::get('bioreactor.temperature_title' ),
        'end_datetime' => $view_end_time,
        'x_data'    => $temp_axis_data['x_data'],
        'y_data'    => $temp_axis_data['y_data'],
      ],
      'ph'              => [
        'name' => 'ph',
        'title' => Lang::get('bioreactor.ph_title' ),
        'end_datetime' => $view_end_time,
        'x_data'    => $ph_axis_data['x_data'],
        'y_data'    => $ph_axis_data['y_data'],
      ],
    ];

    // pass data into the view
    return view( 'Global.show', [
      'route'               => 'single',
      'id'                  => $id,
      'bioreactor'          => $bioreactor,
      'sensors'             => $sensor_ref,
      'end_datetime'        => $view_end_time,
      'x_temperature_data'  => $temp_axis_data['x_data'],
      'y_temperature_data'  => $temp_axis_data['y_data'],
      'x_lightreading_data' => $light_axis_data['x_data'],
      'y_lightreading_data' => $light_axis_data['y_data'],
      'x_gasflow_data'      => $gasflow_axis_data['x_data'],
      'y_gasflow_data'      => $gasflow_axis_data['y_data'],
      'x_phreading_data'    => $ph_axis_data['x_data'],
      'y_phreading_data'    => $ph_axis_data['y_data'],
    ]);
  }
}
