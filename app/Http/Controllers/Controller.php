<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App;
use Lang;

use App\Bioreactor;
use App\Temperature;
use App\Lightreading;
use App\Gasflow;
use App\Phreading;

use Carbon\Carbon;
// use AppNamespaceDetectorTrait;

use DB;

class Controller extends BaseController
{
  use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

  const MODEL_PREFIX = 'App\\';

  protected $bioreactor;

  protected $temperatures;
  protected $lightreadings;
  protected $gasflows;
  protected $phreadings;

  // Populate in constructor
  protected $localized_date_names = [];

  // Should be const, but php does not allow const array
  protected $route_to_sensor = [
    'mytemperatures'  => 'temperature',
    'mylightreadings' => 'light',
    'mygasflows'      => 'oxygen',
    'myphreadings'    => 'ph',
  ];

  // Should be const, but php does not allow const array
  // prop: property used to hold measurement data loaded from database
  // route: url path for full graph page
  // view: The view (folder) for sensor specific [partial] blades
  // model: the name of the Laravel model that links to the data
  // table: the name of the database table that contains the measuremnts
  // data_field: database field name holding measurment ValidatesRequests
  // summarize: the database function to use when combining groups of readings
  // accum_values: flag to indicate if measurements need to be accumulated for
  //   graphing
  // accum_limit: the number of intervals where accumlated values well be start
  //   being reset to zero at the end of each day
  // null_value: the value to use when no entry is found in the database
  // measure_fmt: sprintf format used to create graph data points
  protected $sensors = [
    'oxygen'        => [
      'prop'        => 'gasflows',
      'route'       => '/mygasflows',
      'model'       => 'Gasflow',
      // 'model'  => self::MODEL_PREFIX + 'Gasflow', // syntax error with either "." or "+" operator here
      'table'       => 'gasflows',
      'data_field'  => 'flow',
      'summarize'   => 'sum',
      'accum_values'=> true,
      'accum_limit' => 168,
      'null_value'  => 0,
      'measure_fmt' => "%5.2f",
    ],
    'light'         => [
      'prop'        => 'lightreadings',
      'route'       => '/mylightreadings',
      'model'       => 'Lightreading',
      'table'       => 'lightreadings',
      'data_field'  => 'lux',
      'summarize'   => 'avg',
      'accum_values'=> false,
      'null_value'  => 0,
      'measure_fmt' => "%6.1f",
    ],
    'temperature'   => [
      'prop'        => 'temperatures',
      'route'       => '/mytemperatures',
      'model'       => 'Temperature',
      'table'       => 'temperatures',
      'data_field'  => 'temperature',
      'summarize'   => 'avg',
      'accum_values'=> false,
      'null_value'  => 0,
      'measure_fmt' => "%2.2f",
    ],
    'ph'            => [
      'prop'        => 'phreadings',
      'route'       => '/myphreadings',
      'model'       => 'Phreading',
      'table'       => 'phreadings',
      'data_field'  => 'ph',
      'summarize'   => 'avg',
      'accum_values'=> false,
      'null_value'  => 7,
      'measure_fmt' => "%2.1f",
    ],
  ];


  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {
      // parent::__construct(); // Can not call constructor

      // Load language specific day of the week and month name abbreviation strings
      $names = [];
      for ($i = 0; $i < 7; $i++) {
        $names[] = Lang::get('messages.weekday_Www_' . $i );
      }
      $this->localized_date_names['weekday'] = $names;
      $names = [];
      for ($i = 1; $i <= 12; $i++) {
        $names[] = Lang::get('messages.month_Mmm_' . $i );
      }
      $this->localized_date_names['month'] = $names;

      foreach ($this->sensors as &$sensor) {
        $sensor['model'] = self::MODEL_PREFIX . $sensor['model'];
      }
  }


  /**
   * Handle pseudo class static method calls
   *
   * https://stackoverflow.com/questions/29440737/php-init-class-with-a-static-function
   *
   * @param $method string name of the method being called
   * @param $args mixed arguments to be passed to $method
   */
  public static function __callStatic($method, $args)
  {
    dd([$method,$args]);
    $instance = new static;
    // if ($method === 'issensor') {
    //   return isset($instance->sensors[$args]);
    // }
    // return call_user_func_array(array($instance, $method), $args);
  }


  /**
   * Get a comma delimited string of the known sensors (sensor types)
   *
   */
  public function getKnownSensors()
  {
    $csv_sensors = "";
    foreach ($this->sensors as $sensor_key => $sensor) {
      $csv_sensors .= $sensor_key;
      if ($sensor !== end($this->sensors)) {
        $csv_sensors .= ',';
      }
    }
    return $csv_sensors;
  }


  /**
   * Read the Bioreactor record from the table based on the deviceid
   * parameter. The record is stored in the class as well as being
   * returned
   *
   * @param string $id The deviceid ex. '00002'
   *
   * @throws Exception if no record exists. Not supposed to happen.
   *
   * @return Bioreactor
   */
  public function getBioreactorFromId( $id )
  {

    // correct id from uri if in the wrong format (or missing)!!
    $id = Bioreactor::formatDeviceid( $id );

    // load the record from the table
    try {
      $this->bioreactor = Bioreactor::where('deviceid', '=', $id)->firstOrFail();
    }
    catch (\Exception $e) {
      $message = Lang::get('export.invalid_deviceid');
      dd($message);
      //return Redirect::to('error')->with('message', $message);
    }
    //dd($bioreactor);

    return $this->bioreactor;
  }// ./getBioreactorFromId(…)


  /**
   * Collect data details needed to create a single sensor graph
   *
   * Add the details for a single sensor to the array of information used by
   * the graphing functions.
   *
   * @param $graph_data array by reference to add the sensor details to
   * @param $sensor string sensor name (identifier)
   * @param $sensor_props array sensor properties
   * @param $id string bioreactor id
   * @param $title string graph title, default empty string
   * @param $hours integer number of hours of data to collect, default 3
   * @param $end date the most recent (recorded_on) measurement to show, default now
   *
   * @return null
   */
  protected function loadSensorData( & $graph_data, $sensor, $sensor_props, $id,
      $title='', $hours=3, $end='now' )
  {
    $graph_data[$sensor]['end_datetime'] = $this->getSensorData( $sensor, $id,
      $hours, $end )->toDateTimeString();
    if ( is_null( $this->{ $sensor_props[ 'prop' ]} )) {
      $this->{ $sensor_props[ 'prop' ]} = array();
    }
    $graph_data[$sensor]['xy_data'] = $this->_buildXYMeasurementData( $sensor, $hours );
    $graph_data[$sensor]['route'] = $sensor_props[ 'route' ]; // to build btn href
    $graph_data[$sensor]['title'] = $title;
    $graph_data[$sensor]['point_count'] = count( $graph_data[$sensor]['xy_data'] );
  }// ./loadSensorData(…)


  /**
   * Base data and parameters to show current sensor graphs for a single bioreactor
   *
   * @param string $id deviceid of the bioreactor ex. 00001
   *
   * @return array with view path and base parameters
   */
  protected function bioreactorSiteView( $id )
  {
    // For each configured sensor, get the associated data from the database for
    // this location.  Convert data to the format needed by the Chart.js library.
    $chart_data = [];
    foreach ($this->sensors as $sensor => $props) {
      $this->loadSensorData( $chart_data, $sensor, $props, $id,
        Lang::get('bioreactor.' . $sensor . '_title' ));
    }
    // dd( $chart_data );

    // data to pass into the view
    return [ 'MyBio.mybio', [
      'id'                  => $id,
      'bioreactor'          => $this->getBioreactorFromId( $id ),
      'date_constants'      => $this->localized_date_names,
      'sensors'             => $chart_data,
      'interval_count'      => 3
    ]];
  }// ./bioreactorSiteView(…)


  /**
   * Base data and parameters to show a graph for a single sensor
   *
   * @param string $id bioreactor deviceid ex. 00001
   * @param string $sensor the identifier for the sensor
   * @param int $hrs default 3. number of hours of data to view.
   * @param int $end default now. the most recent (recorded_on) measurement to show
   *
   * @return array with view path and parameters
   */
  protected function sensorGraphFullView( $id, $sensor, $hrs=3, $end='now' )
  {
    $sensor_props = $this->sensors[ $sensor ];
    $id = Bioreactor::formatDeviceid( $id );

    // Get the associated data from the database for $sensor, for this location,
    // and convert it to the format needed by the Chart.js library.
    $chart_data = [];
    $this->loadSensorData( $chart_data, $sensor, $sensor_props, $id, '', $hrs, $end );

    // pass the formatted data to the view
    // TODO ?move? to Global.full_graph
    return [ 'MyBio.sensor_graph', [
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
    ]];
  }// ./sensorGraphFullView(…)


  /**
   * Read the sensor measurement records from the table for a specific
   * deviceid parameter. The records are summarized by the hour.
   *
   * @param string $props the properties of the sensor
   * @param string $id The deviceid ex. '00002'
   * @param Carbon $start_time date and time to read records after
   * @param Carbon $end_time date and time of most recent record
   *
   * @return null
   */
  protected function _getHourlySummarySensorData( $props, $deviceid, $start_time, $end_time )
  {
    // Back to basics with raw DB call, since can't see how to do it using Eloquent
    // Summarize the results down to the average or sum over the hour
    // IDEA refactor hrs to interval_size, and strftime fmt to parameter
    //  to handle other levels of summarization
    $r = DB::table( $props['table'])
      ->select( 'deviceid', 'recorded_on',
        DB::raw( 'strftime("%Y%m%d%H",recorded_on) as hrs' ),
        DB::raw( $props['summarize'] . '(' . $props['data_field'] . ') as ' . $props['data_field']))
      ->groupBy( 'hrs' )
      ->where( 'deviceid', '=', $deviceid )
      ->where( 'recorded_on', '>', $start_time->toDateTimeString())
      ->where( 'recorded_on', '<=', $end_time->toDateTimeString())
      ->get();

    // align the start and end markers to the summarization interval boundary
    $hr_time = new Carbon( $end_time );
    $st_time = new Carbon( $r[0]->recorded_on);
    $hr_time->minute = 0;
    $hr_time->second = 0;
    $st_time->minute = 0;
    $st_time->second = 0;

    // Create array to hold the summarized y data and timestamp
    // The results of the above table get() may be missing data so it may not
    // return the number of hours in the full interval. we need to put zero in first
    $full_period = [];

    // The full_period array is an array of arrays. this is the format that we can use
    // to backfill the results into the eloquent format using the hydrate call
    while ( $hr_time >= $st_time ) {
      // For each interval, create array to hold the summarized results
      $row = [
        'deviceid'            => $deviceid,
        $props['data_field']  => $props['null_value'],
        'recorded_on'         => $hr_time->toDateTimeString()];
      $full_period[] = $row;
      // TODO handle shifting by other summarization interval sizes
      $hr_time->subhours(1);// ($shift_hours)
    }

    // Overwrite the initial summarized value with the data from the actual
    // table get. Note we are putting the order to be the most recent hour last.
    $hr_time = new Carbon( $end_time );
    $hr_time->minute = 0;
    $hr_time->second = 0;

    // IDEA better using foreach($r as $trec) ??
    for ( $i = 0; $i < sizeof( $r ); $i++ ) {
      $trec = new Carbon( $r[$i]->recorded_on );
      $trec->minute = 0;
      $trec->second = 0;
      $index = $hr_time->diffInHours( $trec );

      $full_period[$index][$props['data_field']] =
        sprintf( $props['measure_fmt'], $r[$i]->{ $props['data_field'] });
    }

    // Put constructed array into the Collection format that we need
    $sensor_model =  $props[ 'model' ];
    $this->{ $props['prop'] } = $sensor_model::hydrate( $full_period );
  }// ./_getHourlySummarySensorData(…)


  /**
   * Load a (date) range of sensor measurement records for a specific deviceid
   * parameter. The records are stored in the class, in descending order by
   * dateTime.  In other words, the most recent first.
   * The date the most recent selected record was recorded is returned.
   *
   * @param string $sensor Key to table of (sensor specific) properties
   * @param string $id The deviceid ex. '00002'
   * @param int $data_size Number of hours of data to collect (default 3)
   * @param int $max_date Most recent date to include in the data (default now)
   *
   * @throws Exception if SQL select fails (no records is ok though)
   *
   * @return Carbon datetime of last record
   */
  public function getSensorData( $sensor, $id, $data_size=3, $max_date='now' )
  {
    $sensor_props = $this->sensors[ $sensor ];
    $deviceid = Bioreactor::formatDeviceid($id); // format to 00000

    $sensor_model =  $sensor_props[ 'model' ];
    if ($max_date === 'now') {
      $max_date = Carbon::now();
    }

    // Get recorded_on date of the last record usable with $max_date
    try {
      $most_recent_measurement = $sensor_model::where('deviceid', '=', $deviceid)
        ->where('recorded_on', '<=', $max_date->toDateTimeString())
        ->orderBy('recorded_on', 'desc')->first();
      if ( is_null($most_recent_measurement)) {
        App::abort(404);
      }
    }
    catch (\Exception $e) {
      $start_time = Carbon::now();
      return $start_time;
    }
    $last_time = new Carbon($most_recent_measurement->recorded_on);

    // Go backwards from the recorded_on time to retrieve records
    // Use a new Carbon or it will just point at the old one anyways!
    $start_time = new Carbon($last_time);
    $start_time->subHours($data_size);

    // load the measurement data for this site
    try {
      if ($data_size >= 24) {
        $this->_getHourlySummarySensorData( $sensor_props, $deviceid, $start_time, $last_time );
      }
      else {
        $this->{ $sensor_props[ 'prop' ]} = $sensor_model::where('deviceid', '=', $deviceid)
          ->where('recorded_on', '<=', $last_time->toDateTimeString())
          ->where('recorded_on', '>', $start_time->toDateTimeString() )
          ->orderBy('recorded_on', 'desc')->get();
      }
    }
    catch (\Exception $e) {
      $message = Lang::get('export.no_' . $sensor_props[ 'type' ] . '_data_found');
      dd($message);
      //return Redirect::to('error')->with('message', $message);
    }

    return $last_time;
  }// ./getSensorData(…)


  /**
   * Create the x and y data points needed for the javascript chart builder
   * The measurement records must already have been loaded into the
   * sensor specific Collection in this class
   *
   * @throws Exception if measurements have not been loaded from table yet
   *
   * @param $sensor_name The name (type) of the sesnor
   * @param $hours The number of hours of data in the set
   *
   * @return Array sensor measurement data points
   */
  public function _buildXYMeasurementData( $sensor_name, $hours=3 )
  {
    $sensor_properties = $this -> sensors[ $sensor_name ];
    $xy_data = [];

    // if the measurements have not been loaded, or failed (no records)
    if ( is_null( $this ->{ $sensor_properties[ 'prop' ] })||( count( $this ->{ $sensor_properties[ 'prop' ] }) < 1 ))
    {
      // fill something in, otherwise no graph will be generated
      $xy_data[] = [ 'x' => '0', 'y' => $sensor_properties[ 'null_value' ]];
    } else {
      // reverse the order to make the graph more human like
      $rev_records = $this ->{ $sensor_properties[ 'prop' ] } -> reverse();
      if ( $sensor_properties[ 'accum_values' ]) {
        $value_accum = 0;
        // Insert zero value at the beginning of any accumulation chart
        // First array element in first element of collection
        $prv_dt = new carbon( current(current($rev_records))->recorded_on );
        $xy_data[] = [
          'x' => $prv_dt -> timestamp,
          'y' => $value_accum
        ];
      }
      foreach ( $rev_records as $reading ) {
        $dt = new carbon( $reading->recorded_on );
        if ( $sensor_properties[ 'accum_values' ]) {
          if ( $hours >=  $sensor_properties[ 'accum_limit' ] &&( $dt->day !== $prv_dt->day )) {
            // crossing midnight boundary for long (time period) accumulation chart
            $value_accum = 0;
            // insert extra data point at exactly midnight with zero accumulation
            $prv_dt = new carbon( $reading->recorded_on );
            $prv_dt->hour = 0;
            $prv_dt->minute = 0;
            $prv_dt->second = 0;
            $xy_data[] = [
              'x' => $prv_dt -> timestamp,
              'y' => $value_accum
            ];
          }
          $prv_dt = $dt;
          $value_accum += $reading ->{ $sensor_properties[ 'data_field' ]};
          $xy_data[] = [
            'x' => $dt -> timestamp,
            // 'y' => sprintf( $sensor_properties[ 'measure_fmt' ], $value_accum )
            'y' => $value_accum
          ];
        } else {
          $xy_data[] = [
            'x' => $dt -> timestamp,
            // 'y' => sprintf( $sensor_properties[ 'measure_fmt' ], $reading ->{ $sensor_properties[ 'data_field' ]})
            'y' => $reading ->{ $sensor_properties[ 'data_field' ]}
          ];
        }
      }
    }

    return $xy_data;
  }// ./ _buildXYMeasurementData(…)

}
