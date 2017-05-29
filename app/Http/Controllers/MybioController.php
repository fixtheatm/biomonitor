<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Bioreactor;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Lang;

class MybioController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

  /**
   * Show the users bioreactor summary view
   */
  public function index() {

    // the deviceid should not be blank or bogus as
    // it is from the user record enforced with a foreign key constraint

    $id = Auth::user()->deviceid;
    // TODO refactor: same block of code as GlobalController@show
    $bioreactor = $this->getBioreactorFromId( $id );

    // For each sensor, get the associated data from the database for this
    // location.  Convert the data to the format needed by the Chart.js library.
    // x holds time labels (hh:mm), y holds the data

    // Load and prep all the temperature data
    $end_datetime = $this->getTemperatureData( $id );
    $temp_axis_data = $this->_buildXYTemperatureData(); // Degrees Celsius

    $this->getLightreadingData( $id );
    $light_axis_data = $this->_buildXYLightreadingData(); // intensity as nnnnn.n

    $this->getGasflowData( $id );
    $gasflow_axis_data = $this->_buildXYGasflowData(); // milliliters/minute

    $this->getPhreadingData( $id );
    $ph_axis_data = $this->_buildXYPhreadingData(); // pH

    $view_end_time = $end_datetime->toDateTimeString(); // locale specific?
    $sensor_ref = [
      '1' => [
        'name'        => 'gasflow',
        'graph'       => 'gasflow',
        'title'       => Lang::get('bioreactor.gasflow_title' ),
        'end_datetime'=> $view_end_time,
        'x_data'      => $gasflow_axis_data['x_data'],
        'y_data'      => $gasflow_axis_data['y_data'],
      ],
      '2' => [
        'name'        => 'light',
        'graph'       => 'lightreading',
        'title'       => Lang::get('bioreactor.light_title' ),
        'end_datetime'=> $view_end_time,
        'x_data'      => $light_axis_data['x_data'],
        'y_data'      => $light_axis_data['y_data'],
      ],
      '3' => [
        'name'        => 'temp',
        'graph'       => 'temperature',
        'title'       => Lang::get('bioreactor.temperature_title' ),
        'end_datetime'=> $view_end_time,
        'x_data'      => $temp_axis_data['x_data'],
        'y_data'      => $temp_axis_data['y_data'],
      ],
      '4' => [
        'name'        => 'ph',
        'graph'       => 'phreading',
        'title'       => Lang::get('bioreactor.ph_title' ),
        'export_idx'  => 4,
        'end_datetime'=> $view_end_time,
        'x_data'      => $ph_axis_data['x_data'],
        'y_data'      => $ph_axis_data['y_data'],
      ],
    ];

    // pass data into the view
    return view('MyBio.mybio', [
      'route'               => 'mybio',
      'header_title'        => 'My BioReactor',
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
