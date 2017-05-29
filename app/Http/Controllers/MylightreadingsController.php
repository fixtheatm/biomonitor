<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Bioreactor;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Lang;

use Carbon\Carbon;

class MylightreadingsController extends Controller
{
  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {
    $this->middleware( 'auth' );
  }

  /**
   * Show the users recent light intensity readings
   *
   * @param int $hrs default 3. number of hours of data to view. (only 3 or 24 right now)
   */
  public function index( $hrs=3 )
  {
    // the deviceid should not be blank or bogus as
    // it is from the user record enforced with a foreign key constraint
    $id = Auth::user()->deviceid;

    // load the user´s bioreactor record from the table
    $bioreactor = $this->getBioreactorFromId( $id );

    // load $this->lightreadings data for this bioreactor (device) site
    // returns recorded_on date of last (most recent) record
    $end_datetime = $this->getLightreadingData( $id, $hrs );
    if ( is_null( $this->lightreadings )) {
      $this->lightreadings = array();
    }

    // get the x and y data points to be graphed
    $chart_data = $this->_buildXYLightreadingData();

    // pass the formatted data to the view
    return view( 'MyBio.sensor_graph', [
      'route'           => 'mylightreadings',
      'sensor_name'     => 'light',
      'value_field'     => 'lux',
      'value_label'     => Lang::get('bioreactor.intensity_head'),
      'id'              => $id,
      'bioreactor'      => $bioreactor,
      'end_datetime'    => $end_datetime->toDateTimeString(),
      'x_data'          => $chart_data['x_data'],
      'y_data'          => $chart_data['y_data'],
      'dbdata'          => $this->lightreadings
    ]);
  }

}
