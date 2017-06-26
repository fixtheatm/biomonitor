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
      // TODO refactor: MyBioController::index
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
      'interval_count'      => 3,
      'show_excel'          => false,
      'show_button'         => false,
      'show_inline'         => true,
      'show_graph'          => true,
    ]);
  }// ./show(…)


  /**
   * Graph a block of sensor measurements for a bioreactor based on form data
   *
   * @param Request $request graph configuration information from the form
   * @param int $hrs default 3. number of hours of data to view.
   * ?@param int $end default now. the most recent (recorded_on) measurement to show
   */
  public function formgraph( Request $request, $id )
  {
    $this->validate($request, [
      'sensor_to_graph' => "required|issensor:{$this->getKnownSensors()}",
      'graph_interval' => 'required',
      'graph_end_date' => 'date',
      'hours' => 'only_custom',
    ]);
    $sensor_name = $request->input('sensor_to_graph');
    $tz_offset = $request->input('timezone_offset', '0');
    // $submit_src = $request->input('submit_graph', 'dont really care');
    $browser_tzo = $tz_offset / -60;// Negate hours offset
    $max_date = Carbon::parse($request->input('graph_end_date'), $browser_tzo);
    $max_date->setTimeZone(0);
    $hrs = intval(($request->input('graph_interval') === 'custom') ?
      $request->input('hours') :
      $request->input('graph_interval'));

    // dd([$id, $sensor_name, $hrs, $max_date, $request]);
    return $this->fullgraph( $id, $sensor_name, $hrs, $max_date);
  }// ./formgraph(…)


  /**
   * Graph a block of sensor measurements for a bioreactor
   *
   * @param Request $request graph configuration information from the form
   * @param int $hrs default 3. number of hours of data to view.
   * ?@param int $end default now. the most recent (recorded_on) measurement to show
   */
  public function fullgraph( $id, $sensor, $hrs=3, $end='now')
  {
    $sensor_props = $this->sensors[ $sensor ];
    $id = Bioreactor::formatDeviceid( $id );

    // TODO Global.sensor_graph is small, and only @included from MyBio/…bio.blade
    //  could merge to there, and delete the file

    // Get the associated data from the database for $sensor, for this location,
    // and convert it to the format needed by the Chart.js library.
    $chart_data = [];
    $chart_data[$sensor]['end_datetime'] = $this->getSensorData( $sensor, $id, $hrs, $end )->toDateTimeString();
    if ( is_null( $this->{ $sensor_props[ 'prop' ]} )) {
      $this->{ $sensor_props[ 'prop' ]} = array();
    }
    $chart_data[$sensor]['xy_data'] = $this->_buildXYMeasurementData( $sensor, $hrs );
    $chart_data[$sensor]['point_count'] = count( $chart_data[$sensor]['xy_data'] );

    // pass the formatted data to the view
    // TODO ?move? to Global.full_graph
    return view( 'MyBio.sensor_graph', [
      'id'              => $id,
      'bioreactor'      => $this->getBioreactorFromId( $id ),
      'date_constants'  => $this->localized_date_names,
      'header_title'    => Lang::get('bioreactor.' . $sensor . '_graph_page_title'),
      'sensors'         => $chart_data,
      'value_field'     => $sensor_props[ 'data_field' ],
      'value_label'     => Lang::get('bioreactor.' . $sensor . '_head'),
      'dbdata'          => $this->{ $sensor_props[ 'prop' ]},
      'interval_count'  => $hrs,
      'show_excel'      => false,
      'show_button'     => false,
    ]);
  }// ./fullgraph(…)
}
