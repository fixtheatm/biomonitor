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
        $this->middleware('auth');
    }

  /**
   * Show the users bioreactor summary view
   */
  public function index() {
    $common_data = $this->collect_common();

    // For each sensor, get the associated data from the database for this
    // location.  Convert the data to the format needed by the Chart.js library.
    // x holds time labels (hh:mm), y holds the data

    // Load and prep all the temperature data
    $end_datetime = $this->getTemperatureData( $common_data[ 'id'] );
    $temp_axis_data = $this->_buildXYTemperatureData(); // Degrees Celsius

    $this->getLightreadingData( $common_data[ 'id'] );
    $light_axis_data = $this->_buildXYLightreadingData(); // intensity as nnnnn.n

    $this->getGasflowData( $common_data[ 'id'] );
    $gasflow_axis_data = $this->_buildXYGasflowData(); // milliliters/minute

    $this->getPhreadingData( $common_data[ 'id'] );
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
      'route'               => $common_data[ 'route_base' ],
      'id'                  => $common_data[ 'id'],
      'bioreactor'          => $common_data[ 'site'],
      'date_constants'      => $common_data[ 'dt_constants'],
      'header_title'        => Lang::get('bioreactor.all_graph_page_title'),
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
      'sensor_name'     => 'all',
      'point_count'     => count( $temp_axis_data['y_data'] ),
      'interval_count'  => 3,
    ]);
  }// ./index()


  /**
   * Show a block of sensor measurements for the current user´s bioreactor
   *
   * @param int $hrs default 3. number of hours of data to view.
   * @param int $end default now. the most recent (recorded_on) measurement to show
   */
  public function graph( $hrs=3, $end=0 )
  {
    $common_data = $this->collect_common();

    $mySensor = substr( $common_data[ 'route_base' ], 2 );
    $sensor_props = $this->sensors[ $mySensor ];
    // dd( $sensor_props );

    // TODO turn optional $end into time stamp reference to use to get the initial database record

    // load sensor specific data for this bioreactor (device) site
    // returns recorded_on date of last (most recent) record
    // $end_datetime = $this->getTemperatureData( $common_data[ 'id'], $hrs );
    $end_datetime = $this->getSensorData( $mySensor, $common_data[ 'id'], $hrs );
    if ( is_null( $this->{ $sensor_props[ 'prop' ]} )) {
      $this->{ $sensor_props[ 'prop' ]} = array();
    }
    // TODO figure out what to do about gas flow data, which is not currently
    // actually stored as a gas flow rate.  Massage to rate here?  Preprocess on
    // load to store as rate? (convert fixed volumn and time interval to rate)

    // get the x and y data points to be graphed
    $chart_data = $this->_buildXYMeasurementData( $mySensor );
    // dd($chart_data);

    // pass the formatted data to the view
    return view( 'MyBio.sensor_graph', [
      'route'           => $common_data[ 'route_base' ],
      'id'              => $common_data[ 'id'],
      'bioreactor'      => $common_data[ 'site'],
      'date_constants'  => $common_data[ 'dt_constants'],
      'header_title'    => Lang::get('bioreactor.' . $sensor_props[ 'name' ] . '_graph_page_title'),
      'sensor_name'     => $sensor_props[ 'name' ],
      'sensor_type'     => $sensor_props[ 'type' ],
      'sensor_view'     => $sensor_props[ 'view' ],
      'value_field'     => $sensor_props[ 'data_field' ],
      'value_label'     => Lang::get('bioreactor.' . $sensor_props[ 'name' ] . '_head'),
      'end_datetime'    => $end_datetime->toDateTimeString(),
      'xy_data'         => $chart_data,
      'dbdata'          => $this->{ $sensor_props[ 'prop' ]},
      'point_count'     => count( $chart_data ),
      'interval_count'  => $hrs,
    ]);
  }// ./graph( $hrs=3, $end=0 )


  /**
   * collect some data that is used in multiple places
   *
   * @returns object with some directly calculatable values
   */
  private function collect_common()
  {
    // create language specific date (text) interval constants.  Really this
    // should be part of a full locale sensitive date formating package
    // TODO move to protected static in Controller class (populate in init)?
    $date_range_names = [];
    $names = [];
    for ($i = 0; $i < 7; $i++) {
      $names[] = Lang::get('messages.weekday_Www_' . $i );
    }
    $date_range_names['weekday'] = $names;
    $names = [];
    for ($i = 1; $i <= 12; $i++) {
      $names[] = Lang::get('messages.month_Mmm_' . $i );
    }
    $date_range_names['month'] = $names;

    $route_base = explode( '/', Route::current()->uri())[0];

    $id = Auth::user()->deviceid;
    $bioreactor = $this->getBioreactorFromId( $id );

    return [
      'dt_constants'  => $date_range_names,
      'route_base'    => $route_base,
      'id'            => $id,
      'site'          => $bioreactor,
    ];
  }// ./collect_common()

}
