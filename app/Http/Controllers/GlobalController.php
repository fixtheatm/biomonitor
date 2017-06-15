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
      'header_title' => 'All Bioreactors',
      'dbdata'     => $bioreactors
    ]);
  }// ./index(…)


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
    $chart_data = [];
    foreach ($this->sensors as $sensor => $props) {
      // TODO refactor: MyBioController::index«2»
      $chart_data[$sensor]['end_datetime'] = $this->getSensorData( $sensor, $id )->toDateTimeString();
      if ( is_null( $this->{ $props[ 'prop' ]} )) {
        $this->{ $props[ 'prop' ]} = array();
      }
      $chart_data[$sensor]['xy_data'] = $this->_buildXYMeasurementData( $sensor );
      $chart_data[$sensor]['title'] = Lang::get('bioreactor.' . $sensor . '_title' );
      $chart_data[$sensor]['point_count'] = count( $chart_data[$sensor]['xy_data'] );
    }
    // dd( $chart_data );

    // pass data into the view
    return view( 'MyBio.mybio', [
      'id'                  => $id,
      'bioreactor'          => $bioreactor,
      'date_constants'      => $this->localized_date_names,
      'header_title'        => 'Bioreactor #' . $id,
      'sensors'             => $chart_data,
      'interval_count'  => 3,
      'show_excel'      => false,
      'show_button'     => false,
    ]);
  }// ./show(…)
}
