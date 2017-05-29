<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Bioreactor;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Lang;

use Carbon\Carbon;

class MygasflowsController extends Controller
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
   * Show the users gas production flows
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

    // load $this->gasflows data for this site
    // returns recorded_on date of last (most recent) record
    $end_datetime = $this->getGasflowData( $id, $hrs );
    if ( is_null( $this->gasflows )) {
      $this->gasflows = array();
    }

    // get the x and y data points to be graphed
    $chart_data = $this->_buildXYGasflowData();

    // pass the formatted data to the view
    return view('MyBio.sensor_graph', [
      'route'           => 'mygasflows',
      'sensor_name'     => 'gasflow',
      'value_field'     => 'flow',
      'value_label'     => Lang::get('bioreactor.ml_per_min_head'),
      'id'              => $id,
      'bioreactor'      => $bioreactor,
      'end_datetime'    => $end_datetime->toDateTimeString(),
      'x_data'          => $chart_data['x_data'],
      'y_data'          => $chart_data['y_data'],
      'dbdata'          => $this->gasflows
    ]);
  }

}
