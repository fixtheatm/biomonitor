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
    'tsttemperatures' => 'temperature',
    'tstlightreadings'=> 'light',
    'tstgasflows'     => 'oxygen',
    'tstphreadings'   => 'ph'
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
    // Truncates all the recorded_on details down to just the hour.
    // In other words we are summarizing the results down to the average or
    // sum over the hour.
    // IDEA refactor hrs to interval_size, and strftime fmt to parameter
    //  to handle other levels of summarization
    // TODO check what toDateTimeString() does around daylight time shifts
    //  data is in UTC, but with no marker to show that, so start before the
    //  daylight shift, and end after could be a problem
    $r = DB::table( $props['table'])
      ->select( 'deviceid', 'recorded_on',
        DB::raw( 'strftime("%Y%m%d%H",recorded_on) as hrs' ),
        DB::raw( $props['summarize'] . '(' . $props['data_field'] . ') as ' . $props['data_field']))
      ->groupBy( 'hrs' )
      ->where( 'deviceid', '=', $deviceid )
      ->where( 'recorded_on', '>', $start_time->toDateTimeString())
      ->get();
    // TODO add $end_time to selection where clause

    // Create array to hold the summarized y data and timestamp
    // The results of the above table get() may be missing data so it may not
    // return the number of hours in the full interval. we need to put zero in first
    $full_period = [];
    $hr_time = new Carbon( $end_time );
    $hr_time->minute = 0;
    $hr_time->second = 0;
    // IDEA $start_time should be 0 min, 0 seconds as well
    $interval_count = $hr_time->diffInHours( $start_time ) + 1;
    // one extra interval, due to end points not being exact interval boundaries

    // The full_period array is an array of arrays. this is the format that we can use
    // to backfill the results into the eloquent format using the hydrate call
    for ( $i = 0; $i <= $interval_count; $i++ ) {
      // For each interval, make an array holding the summarizated results
      // TODO as above, check results accross daylight time shift
      // IDEA is it more appropriate/possible to drop missing records instead?
      $row = [
        'deviceid'            => $deviceid,
        $props['data_field']  => $props['null_value'],
        'recorded_on'         => $hr_time->toDateTimeString()];
      $full_period[$i] = $row;
      // TODO handle shifting by other summarization interval sizes
      $hr_time->subhours(1);
    }

    // Overwrite the initial summarized value with the data from the actual
    // table get. Note we are putting the order to be the most recent hour last.
    $hr_time = new Carbon( $end_time );
    $hr_time->minute = 0;
    $hr_time->second = 0;

    for ( $i = 0; $i < sizeof( $r ); $i++ ) {
      $trec = new Carbon( $r[$i]->recorded_on );
      $trec->minute = 0;
      $trec->second = 0;
      $index = $hr_time->diffInHours( $trec );

      $full_period[$index][$props['data_field']] =
        sprintf( $props['measure_fmt'], $r[$i]->{ $props['data_field'] });
    }

    // Put constructed array into the Collection format that we need
    $sensor_model =  self::MODEL_PREFIX . $props[ 'model' ];
    $this->{ $props['prop'] } = $sensor_model::hydrate( $full_period );
  }// ./_getHourlySummarySensorData(…)


  /**
   * Read the sensor measurement records from the table for a specific deviceid
   * parameter. The records are stored in the class, loaded in descending order
   * by dateTime.  In other words the most recent first.
   * The date the most recent record was recorded is returned.
   *
   * @param string $sensor Key to $sensor table of (sensor specific) properties
   * @param string $id The deviceid ex. '00002'
   * @param int $data_size = 3  Number of hours of data to collect
   *
   * @throws Exception if SQL select fails (no records is ok though)
   *
   * @return Carbon datetime of last record
   */
  public function getSensorData( $sensor, $id, $data_size=3 )
  {
    $sensor_props = $this->sensors[ $sensor ];
    $deviceid = Bioreactor::formatDeviceid($id); // format to 00000

    $sensor_model =  self::MODEL_PREFIX . $sensor_props[ 'model' ];
    // https://laracasts.com/discuss/channels/eloquent/access-eloquent-model-dynamically
    // https://laravel.com/docs/5.2/eloquent Dynamic Scope

    // Get the last date entry record
    // TODO use extra (optional) parameter to limit highest date (instead of latest)
    //   and $recorded_on <= utc date
    try {
      // Temperature::
      $most_recent_measurement = $sensor_model::where('deviceid', '=', $deviceid)->orderBy('recorded_on', 'desc')->first();
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
        // deviceid, «sensor data field», recorded_on
      }
      else {
        // TODO limit highest recorded_on date as well
        // TODO check $start_time->toDateTimeString() is utc
        $this->{ $sensor_props[ 'prop' ]} = $sensor_model::where('deviceid', '=', $deviceid)->where('recorded_on', '>', $start_time->toDateTimeString() )->orderBy('recorded_on', 'desc')->get();
        // id, deviceid, temperature, recorded_on, created_at, updated_at
      }
    }
    catch (\Exception $e) {
      $message = Lang::get('export.no_' . $sensor_props[ 'type' ] . '_data_found');
      dd($message);
      //return Redirect::to('error')->with('message', $message);
    }

    //dd($this->{ $sensor_props[ 'prop' ]});
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
