<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Bioreactor;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Lang;

use Carbon\Carbon;
use Route;

class MybioController extends Controller
{
  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
    $this->middleware('auth');
  }


  /**
   * Show the users bioreactor summary view
   */
  public function index() {
    $id = Auth::user()->deviceid;
    $route_base = explode( '/', Route::current()->uri())[0];

    // For each configured sensor, get the associated data from the database for
    // this location, and convert it to the format needed by the Chart.js library.
    $chart_data = [];
    foreach ($this->sensors as $sensor => $props) {
      // TODO Refactor: match to sensor data load code in graph function
      // TODO and GlobalController::show
      $chart_data[$sensor]['end_datetime'] = $this->getSensorData( $sensor, $id )->toDateTimeString();
      if ( is_null( $this->{ $props[ 'prop' ]} )) {
        $this->{ $props[ 'prop' ]} = array();
      }
      $chart_data[$sensor]['xy_data'] = $this->_buildXYMeasurementData( $sensor );
      $chart_data[$sensor]['route'] = $props[ 'route' ]; // to build btn href
      $chart_data[$sensor]['title'] = Lang::get('bioreactor.' . $sensor . '_title' );
      $chart_data[$sensor]['point_count'] = count( $chart_data[$sensor]['xy_data'] );
    }
    // dd( $chart_data );

    // pass data into the view
    return view('MyBio.mybio', [
      'id'                  => $id,
      'bioreactor'          => $this->getBioreactorFromId( $id ),
      'date_constants'      => $this->localized_date_names,
      'header_title'        => Lang::get('bioreactor.all_graph_page_title'),
      'sensors'             => $chart_data,
      'interval_count'      => 3,
      'show_excel'          => true,
      'show_button'         => true,
    ]);
  }// ./index()


  /**
   * Show a block of sensor measurements for the current user´s bioreactor
   *
   * @param int $hrs default 3. number of hours of data to view.
   * @param int $end default now. the most recent (recorded_on) measurement to show
   */
  public function graph( $hrs=3, $end='now' )
  {
    $id = Auth::user()->deviceid;
    $route_base = explode( '/', Route::current()->uri())[0];

    $sensor = $this->route_to_sensor[ $route_base ];
    $props = $this->sensors[ $sensor ];

    // TODO turn optional $end into time stamp reference to use to get the initial database record

    // Get the associated date from the database for $sensor, for this location,
    // and convert it to the format needed by the Chart.js library.
    $chart_data = [];
    $chart_data[$sensor]['end_datetime'] = $this->getSensorData( $sensor, $id, $hrs )->toDateTimeString();
    if ( is_null( $this->{ $props[ 'prop' ]} )) {
      $this->{ $props[ 'prop' ]} = array();
    }
    $chart_data[$sensor]['xy_data'] = $this->_buildXYMeasurementData( $sensor, $hrs );
    $chart_data[$sensor]['point_count'] = count( $chart_data[$sensor]['xy_data'] );
    // dd( $chart_data );

    // pass the formatted data to the view
    return view( 'MyBio.sensor_graph', [
      'route'           => $route_base,
      'id'              => $id,
      'bioreactor'      => $this->getBioreactorFromId( $id ),
      'date_constants'  => $this->localized_date_names,
      'header_title'    => Lang::get('bioreactor.' . $sensor . '_graph_page_title'),
      'sensors'         => $chart_data,
      'value_field'     => $props[ 'data_field' ],
      'value_label'     => Lang::get('bioreactor.' . $sensor . '_head'),
      'dbdata'          => $this->{ $props[ 'prop' ]},
      'interval_count'  => $hrs,
      'show_excel'      => false,
      'show_button'     => false,
    ]);
  }// ./graph(…)
}
