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

use DB;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

  /**  @var Bioreactor $bioreactor      read in via getBioreactorFromId */
  /**  @var Temperature Collection $temperatures   read in via getTemperatureData */
  /**  @var Temperature [] $x_temperatures   JS Charts data created in _buildXYTemperatureData */
  /**  @var Temperature [] $y_temperatures   JS Charts data created in _buildXYTemperatureData */

  protected $bioreactor="";

  protected $temperatures;
  protected $x_temperatures=array();
  protected $y_temperatures=array();

  protected $lightreadings;
  protected $x_lightreadings=array();
  protected $y_lightreadings=array();

  protected $gasflows;
  protected $x_gasflows=array();
  protected $y_gasflows=array();

  protected $phreadings;
  protected $x_phreadings=array();
  protected $y_phreadings=array();

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
  }

  /**
   * Read the Temperature records from the table for a specific deviceid
   * parameter. The records are summarized by the hour
   *
   * @param string $id The deviceid ex. '00002'
   * @param Carbon $start_time date and time to read records after
   * @param Carbon $last_time date and time of most recent record
   *
   * @return null
   */
  protected function _getHourlySummaryTemperatureData($deviceid,$start_time,$last_time)
  {

    // using raw call to the DB. I can't see how to do it using Eloquent
    // so going back to basics.
    // truncates all the recorded_on details down to just the hour.
    // In other words we are summarizing the results down to the average
    // over the hour.
    $r=DB::table('temperatures')
      ->select('deviceid', 'recorded_on',
      DB::raw('strftime("%H",time(recorded_on)) as hrs'),
      DB::raw('sum(temperature)/count(*) as temperature'))
      ->groupBy('hrs')
      ->where('deviceid', '=', $deviceid)
      ->where('recorded_on', '>', $start_time->toDateTimeString() )
      ->get();

    //var_dump( $r);

    // make a 24 element array to hold the x data points
    // The results of the above table get() may be missing data so it
    // may not return 24 results. we need to put zero in first
    $all_day=[];
    $hr_time = new Carbon($last_time);
    $hr_time->minute=0;
    $hr_time->second=0;

    //dd($hr_time);

    // the all_day array is an array of arrays. this is the format that we can use
    // to backfill the results into the eloquent format using the hydrate call
    for ( $i=0; $i < 24; $i++) {
      // for each hour, make an array holding the results
      $row = ['deviceid'=>$deviceid, 'hrs'=> 0, 'temperature'=>'0.0', 'recorded_on'=>$hr_time->toDateTimeString()];
      $all_day[$i] = $row;
      $hr_time->subhours(1);
    }

    // overwrite the average temperatures with the data from the actual
    // table get. Note we are putting the order to be the most recent hour last.
    $hr_time = new Carbon($last_time);
    $hr_time->minute=0;
    $hr_time->second=0;

    for ( $i=0; $i < sizeof($r); $i++) {
      $trec = new Carbon($r[$i]->recorded_on);
      $trec->minute=0;
      $trec->second=0;
      $index = $hr_time->diffInHours($trec);

      //echo "[".$i."]["."[".$index."][".$r[$i]->temperature."]<br>";

      $all_day[$index]['temperature'] =
        sprintf("%02.1f",$r[$i]->temperature);
    }
    //var_dump($all_day);


    // the hydrate function will put our constructed array into the
    // Collection format that we need
    $this->temperatures = Temperature::hydrate($all_day);

    //dd($this->temperatures);
  }

  /**
   * Read the Temperature records from the table for a specific deviceid
   * parameter. The records are stored in the class as well as being
   * returned. The records are loaded in descending order by dateTime
   * In other words the most recent first.
   *
   * @param string $id The deviceid ex. '00002'
   * @param int $data_size = 3  Number of hours of data (3 or 24)
   *
   * @throws Exception if SQL select fails (no records is ok though)
   *
   * @return Carbon datetime of last record
   */
  public function getTemperatureData($id, $data_size=3)
  {

    // correct id from uri if in the wrong format (or missing)!!
    $deviceid = Bioreactor::formatDeviceid($id);


    // get the last data entry record. Use the record_on time
    // and go backwards from that time to retrieve records
    try {
      $most_recent_temp = Temperature::where('deviceid', '=', $deviceid)->orderBy('recorded_on', 'desc')->first();
      if ( is_null($most_recent_temp)) {
        App::abort(404);
      }
    }
    catch (\Exception $e) {
      $start_time = Carbon::now();
      return $start_time;
    }

    //dd($most_recent_temp);

    $last_time = new Carbon($most_recent_temp->recorded_on);

    // subtract # of hours. We need to use a new Carbon or it will
    // just point at the old one anyways!
    $start_time = new Carbon($last_time);
    $start_time->subHours($data_size);
    //dd($last_time->toDateTimeString());


    // load the temperature data for this site
    try {
      if ($data_size==24) {
        $this->_getHourlySummaryTemperatureData($deviceid,$start_time,$last_time);
      }
      else {
        $this->temperatures = Temperature::where('deviceid', '=', $deviceid)->where('recorded_on', '>', $start_time->toDateTimeString() )->orderBy('recorded_on', 'desc')->get();
        //$this->temperatures = Temperature::where('deviceid', '=', $deviceid)->orderBy('recorded_on', 'desc')->take($data_size)->get();
      }
    }
    catch (\Exception $e) {
      $message = Lang::get('export.no_temperature_data_found');
      dd($message);
      //return Redirect::to('error')->with('message', $message);
    }

    //dd($this->temperatures);

    return $last_time;
  }


  /**
   * Builds the x and y temperature graph arrays that are passed to the
   * javascript Chart builder. The temperature records must already
   * have been loaded into the temperatures Collection in this class
   *
   * @param string $x_axis_style ='default' 'default' is time. 'dot' is a dot
   *
   * @throws Exception if temperatures have not been loaded from table yet
   *
   * @return Array Mixed  x and y temperature chart data
   */
  public function _buildXYTemperatureData($x_axis_style='default')
  {
    // put the data in the correct form for the charts JS library
    // generate an x and y array
    // x holds labels as mm:ss format
    // y holds temperatures as nn.nn format

    $this->x_temperatures = [];
    $this->y_temperatures = [];

    // if the temperatures have not been loaded
    // indicates that gettemperature data has not been called
    // or failed (no recs)
    if ( ! is_null ($this->temperatures) && count($this->temperatures)>0) {

      // reverse the order to make the graph more human like
      $rev_temps = $this->temperatures->reverse();

      foreach ($rev_temps as $temperature) {

         $dt = new carbon($temperature->recorded_on);

        switch($x_axis_style)
        {
        case 'dot':
          $this->x_temperatures[] = '.';
          break;
        default:
          $this->x_temperatures[] = $dt->format('h:i');
          break;

        }
        $this->y_temperatures[] = sprintf("%02.2f",$temperature->temperature);
      }
    }

    // just put something in if there is no data
    // otherwise no graph will be generated
    if ( is_null ($this->temperatures) || (count($this->temperatures) < 1) )
    {
      $this->x_temperatures[]='0';
      $this->y_temperatures[]=0;
    }

    //dd($this->x_temperatures);
    //dd($this->y_temperatures);

    return [
      'x_data' => $this->x_temperatures,
      'y_data' => $this->y_temperatures
    ];
  }

  /**
   * Read the Lightreading records from the table for a specific deviceid
   * parameter. The records are summarized by the hour
   *
   * @param string $id The deviceid ex. '00002'
   * @param Carbon $start_time date and time to read records after
   * @param Carbon $last_time date and time of most recent record
   *
   * @return null
   */
  protected function _getHourlySummaryLightreadingData($deviceid,$start_time,$last_time)
  {
    // using raw call to the DB. I can't see how to do it using Eloquent
    // so going back to basics.
    // truncates all the recorded_on details down to just the hour.
    // In other words we are summarizing the results down to the average
    // over the hour.
    $r=DB::table('lightreadings')
      ->select('deviceid', 'recorded_on',
      DB::raw('strftime("%H",time(recorded_on)) as hrs'),
      DB::raw('sum(lux)/count(*) as lux'))
      ->groupBy('hrs')
      ->where('deviceid', '=', $deviceid)
      ->where('recorded_on', '>', $start_time->toDateTimeString() )
      ->get();

    //dd($r);

    // make a 24 element array to hold the x data points
    // The results of the above table get may be missing data so it
    // may not return 24 results. we need to put zero in first
    $all_day=[];
    $hr_time = new Carbon($last_time);
    $hr_time->minute=0;
    $hr_time->second=0;

    //dd($hr_time);

    // the all_day array is an array of arrays. this is the format that we can use
    // to backfill the results into the eloquent format using the hydrate call
    for ( $i=0; $i < 24; $i++) {
      // for each hour, make an array holding the results
      $row = ['deviceid'=>$deviceid, 'hrs'=> 0, 'lux'=>'0.0', 'recorded_on'=>$hr_time->toDateTimeString()];
      $all_day[$i] = $row;
      $hr_time->subhours(1);
    }

    // overwrite the average lightreadings with the data from the actual
    // table get. Note we are putting the order to be the most recent hour last.
    $hr_time = new Carbon($last_time);
    $hr_time->minute=0;
    $hr_time->second=0;

    for ( $i=0; $i < sizeof($r); $i++) {
      $trec = new Carbon($r[$i]->recorded_on);
      $trec->minute=0;
      $trec->second=0;
      $index = $hr_time->diffInHours($trec);

      //echo "[".$i."]["."[".$index."][".$r[$i]->lux."]<br>";

      $all_day[$index]['lux'] =
        sprintf("%6.1f",$r[$i]->lux);
    }
    //dd($all_day);


    // the hydrate function will put our constructed array into the
    // Collection format that we need
    $this->lightreadings = Lightreading::hydrate($all_day);

    //dd($this->lightreadings);
  }


  /**
   * Read the Lightreading records from the table for a specific deviceid
   * parameter. The records are stored in the class as well as being
   * returned. The records are loaded in descending order by dateTime
   * In other words the most recent first.
   *
   * @param string $id The deviceid ex. '00002'
   * @param int $data_size = 3  Number of hours of data (3 or 24)
   *
   * @throws Exception if SQL select fails (no records is ok though)
   *
   * @return Carbon datetime of last record
   */
  public function getLightreadingData($id, $data_size=3)
  {

    // correct id from uri if in the wrong format (or missing)!!
    $deviceid = Bioreactor::formatDeviceid($id);

    // get the last data entry record. Use the record_on time
    // and go backwards from that time to retrieve records
    try {
      $most_recent_lightreading = Lightreading::where('deviceid', '=', $deviceid)->orderBy('recorded_on', 'desc')->first();
      if ( is_null($most_recent_lightreading)) {
        App::abort(404);
      }
    }
    catch (\Exception $e) {
      $start_time = Carbon::now();
      return $start_time;
    }
    $last_time = new Carbon($most_recent_lightreading->recorded_on);

    // subtract # of hours. We need to use a new Carbon or it will
    // just point at the old one anyways!
    $start_time = new Carbon($last_time);
    $start_time->subHours($data_size);

    //dd($last_time->toDateTimeString());

    // load the data for this site
    try {
      if ( $data_size==24 ) {
        $this->_getHourlySummaryLightreadingData($deviceid,$start_time,$last_time);
      }
      else {
        $this->lightreadings = Lightreading::where('deviceid', '=', $deviceid)->where('recorded_on', '>', $start_time->toDateTimeString() )->orderBy('recorded_on', 'desc')->get();
      }
    }
    catch (\Exception $e) {
      $message = Lang::get('export.no_lightreading_data_found');
      dd($message);
      //return Redirect::to('error')->with('message', $message);
    }

    //dd($this->lightreadings);

    return $last_time;
  }

  /**
   * Builds the x and y Lightreading graph arrays that are passed to the
   * javascript Chart builder. The Lightreading records must already
   * have been loaded into the Lightreading Collection in this class
   *
   * @param string $x_axis_style ='default' 'default' is time. 'dot' is a dot
   *
   * @throws Exception if Lightreading have not been loaded from table yet
   *
   * @return Array Mixed  x and y Lightreading chart data
   */
  public function _buildXYLightreadingData($x_axis_style='default')
  {

    // put the data in the correct form for the charts JS library
    // generate an x and y array
    // x holds labels as mm:ss format
    // y holds y_lightreadings as nnnnn.n format

    $this->x_lightreadings = [];
    $this->y_lightreadings = [];

    // abort if the lightreadings have not been loaded
    // indicates that getlightreading data has not been called
    if ( ! is_null ($this->lightreadings) && count($this->lightreadings)>0) {

      // reverse the order to make the graph more human like
      $rev_light = $this->lightreadings->reverse();

      foreach ($rev_light as $lightreading) {

         $dt = new carbon($lightreading->recorded_on);

        switch($x_axis_style)
        {
          case 'dot':
            $this->x_lightreadings[] = '.';
            break;
          default:
            $this->x_lightreadings[] = $dt->format('h:i');
            break;
        }
        $this->y_lightreadings[] = sprintf("%6.1f",$lightreading->lux);
      }
    }

    // just put something in if there is no data
    // otherwise no graph will be generated
    if (is_null ($this->lightreadings) || (count($this->lightreadings) < 1) )
    {
      $this->x_lightreadings[]='0';
      $this->y_lightreadings[]=0;
    }

    //dd($this->x_lightreadings);
    //dd($this->y_lightreadings);

    return [
      'x_data' => $this->x_lightreadings,
      'y_data' => $this->y_lightreadings
    ];
  }

  /**
   * Read the Gasflow records from the table for a specific deviceid
   * parameter. The records are summarized by the hour
   *
   * @param string $id The deviceid ex. '00002'
   * @param Carbon $start_time date and time to read records after
   * @param Carbon $last_time date and time of most recent record
   *
   * @return null
   */
  protected function _getHourlySummaryGasflowData($deviceid,$start_time,$last_time)
  {
    // using raw call to the DB. I can't see how to do it using Eloquent
    // so going back to basics.
    // truncates all the recorded_on details down to just the hour.
    // In other words we are summarizing the results down to the average
    // over the hour.
    $r=DB::table('gasflows')
      ->select('deviceid', 'recorded_on',
      DB::raw('strftime("%H",time(recorded_on)) as hrs'),
      DB::raw('sum(flow)/count(*) as flow'))
      ->groupBy('hrs')
      ->where('deviceid', '=', $deviceid)
      ->where('recorded_on', '>', $start_time->toDateTimeString() )
      ->get();

    //dd($r);

    // make a 24 element array to hold the x data points
    // The results of the above table get may be missing data so it
    // may not return 24 results. we need to put zero in first
    $all_day=[];
    $hr_time = new Carbon($last_time);
    $hr_time->minute=0;
    $hr_time->second=0;

    //dd($hr_time);

    // the all_day array is an array of arrays. this is the format that we can use
    // to backfill the results into the eloquent format using the hydrate call
    for ( $i=0; $i < 24; $i++) {
      // for each hour, make an array holding the results
      $row = ['deviceid'=>$deviceid, 'hrs'=> 0, 'flow'=>0.0, 'recorded_on'=>$hr_time->toDateTimeString()];
      $all_day[$i] = $row;
      $hr_time->subhours(1);
    }

    // overwrite the average Gasflow with the data from the actual
    // table get. Note we are putting the order to be the most recennt hour last.
    $hr_time = new Carbon($last_time);
    $hr_time->minute=0;
    $hr_time->second=0;

    for ( $i=0; $i < sizeof($r); $i++) {
      $trec = new Carbon($r[$i]->recorded_on);
      $trec->minute=0;
      $trec->second=0;
      $index = $hr_time->diffInHours($trec);

      //echo "[".$i."]["."[".$index."][".$r[$i]->flow."]<br>";

      $all_day[$index]['flow'] =
        sprintf("%5.2f",$r[$i]->flow);
    }
    //dd($all_day);


    // the hydrate function will put our constructed array into the
    // Collection format that we need
    $this->gasflows = Gasflow::hydrate($all_day);

    //dd($this->gasflows);
  }


  /**
   * Read the Gasflow records from the table for a specific deviceid
   * parameter. The records are stored in the class as well as being
   * returned. The records are loaded in descending order by dateTime
   * In other words the most recent first.
   *
   * @param string $id The deviceid ex. '00002'
   * @param int $data_size = 3  Number of hours of data (3 or 24)
   *
   * @throws Exception if SQL select fails (no records is ok though)
   *
   * @return Carbon datetime of last record
   */
  public function getGasflowData( $id, $data_size=3 )
  {

    // correct id from uri if in the wrong format (or missing)!!
    $deviceid = Bioreactor::formatDeviceid($id);

    // get the last data entry record. Use the record_on time
    // and go backwards from that time to retrieve records
    try {
      $most_recent_gasflow = Gasflow::where('deviceid', '=', $deviceid)->orderBy('recorded_on', 'desc')->first();
      if ( is_null($most_recent_gasflow)) {
        App::abort(404);
      }
    }
    catch (\Exception $e) {
      $start_time = Carbon::now();
      return $start_time;
    }
    $last_time = new Carbon($most_recent_gasflow->recorded_on);

    // subtract # of hours. We need to use a new Carbon or it will
    // just point at the old one anyways!
    $start_time = new Carbon($last_time);
    $start_time->subHours($data_size);

    //dd($last_time->toDateTimeString());

    // load the data for this site
    try {
      if ( $data_size==24) {
        $this->_getHourlySummaryGasflowData($deviceid,$start_time,$last_time);
      }
      else {
        $this->gasflows = Gasflow::where('deviceid', '=', $deviceid)->where('recorded_on', '>', $start_time->toDateTimeString() )->orderBy('recorded_on', 'desc')->get();
      }
    }
    catch (\Exception $e) {
      $message = Lang::get('export.no_gasflow_data_found');
      dd($message);
      //return Redirect::to('error')->with('message', $message);
    }

    //dd($this->gasflows);

    return $last_time;
  }

  /**
   * Build x and y, time and measurement arrays for recorded gas flows
   *
   * Data structured to be compatible with the javascript chart builder.
   *
   * The Gasflow records must already have been loaded into the Gasflow
   * Collection in this class
   *
   * @param string $x_axis_style ='default' 'default' is time. 'dot' is a dot
   *
   * @throws Exception if Gasflow has not been loaded from table yet
   *
   * @return Array 2 entries, with x and y Gasflow data
   */
  public function _buildXYGasflowData( $x_axis_style='default' )
  {
    // put the data in the correct form for the charts JS library
    // generate an x and y array
    // x holds time labels in hh:mm format
    // y holds gas flow measurements in nnnnn.nn format

    $this->x_gasflows = [];
    $this->y_gasflows = [];

    // abort if the gasflows have not been loaded
    // indicates that getgasflow data has not been called
    if ( ! is_null( $this->gasflows ) && count( $this->gasflows ) > 0 ) {

      // reverse the order to make the graph more human like
      $rev_gasflow = $this->gasflows->reverse();

      foreach ($rev_gasflow as $gasflow) {

        $dt = new carbon( $gasflow->recorded_on );

        switch( $x_axis_style )
        {
        case 'dot':
          $this->x_gasflows[] = '.';
          break;
        default:
          $this->x_gasflows[] = $dt->format( 'h:i' );
          break;
        }
        $this->y_gasflows[] = sprintf( "%5.2f", 10.0 * $gasflow->flow );
      }
    }

    // just put something in if there is no data
    // otherwise no graph will be generated
    if ( is_null( $this->gasflows ) || ( count( $this->gasflows ) < 1 ))
    {
      $this->x_gasflows[] = '0';
      $this->y_gasflows[] = 0;
    }

    //dd($this->x_gasflows);
    //dd($this->y_gasflows);

    return [
      'x_data' => $this->x_gasflows,
      'y_data' => $this->y_gasflows
    ];
  }

  /**
   * Read the Phreading records from the table for a specific deviceid
   * parameter. The records are summarized by the hour
   *
   * @param string $id The deviceid ex. '00002'
   * @param Carbon $start_time date and time to read records after
   * @param Carbon $last_time date and time of most recent record
   *
   * @return null
   */
  protected function _getHourlySummaryPhreadingData($deviceid,$start_time,$last_time)
  {
    // using raw call to the DB. I can't see how to do it using Eloquent
    // so going back to basics.
    // truncates all the recorded_on details down to just the hour.
    // In other words we are summarizing the results down to the average
    // over the hour.
    $r=DB::table('phreadings')
      ->select('deviceid', 'recorded_on',
      DB::raw('strftime("%H",time(recorded_on)) as hrs'),
      DB::raw('sum(ph)/count(*) as ph'))
      ->groupBy('hrs')
      ->where('deviceid', '=', $deviceid)
      ->where('recorded_on', '>', $start_time->toDateTimeString() )
      ->get();

    // make a 24 element array to hold the x data points
    // The results of the above table get may be missing data so it
    // may not return 24 results. we need to put zero in first
    $all_day=[];
    $hr_time = new Carbon($last_time);
    $hr_time->minute=0;
    $hr_time->second=0;

    // the all_day array is an array of arrays. this is the format that we can use
    // to backfill the results into the eloquent format using the hydrate call
    for ( $i=0; $i < 24; $i++) {
      // for each hour, make an array holding the results
      $row = ['deviceid'=>$deviceid, 'hrs'=> 0, 'ph'=>'0.0', 'recorded_on'=>$hr_time->toDateTimeString()];
      $all_day[$i] = $row;
      $hr_time->subhours(1);
    }

    // overwrite the average phreadings with the data from the actual
    // table get. Note we are putting the order to be the most recent hour last.
    $hr_time = new Carbon($last_time);
    $hr_time->minute=0;
    $hr_time->second=0;

    for ( $i=0; $i < sizeof($r); $i++) {
      $trec = new Carbon($r[$i]->recorded_on);
      $trec->minute=0;
      $trec->second=0;
      $index = $hr_time->diffInHours($trec);

      $all_day[$index]['ph'] =
        sprintf("%02.2f",$r[$i]->ph);
    }

    // the hydrate function will put our constructed array into the
    // Collection format that we need
    $this->phreadings = Phreading::hydrate($all_day);
  }


  /**
   * Read the Phreading records from the table for a specific deviceid
   * parameter. The records are stored in the class as well as being
   * returned. The records are loaded in descending order by dateTime
   * In other words the most recent first.
   *
   * @param string $id The deviceid ex. '00002'
   * @param int $data_size = 3  Number of hours of data (3 or 24)
   *
   * @throws Exception if SQL select fails (no records is ok though)
   *
   * @return Carbon datetime of last record
   */
  public function getPhreadingData($id, $data_size=3)
  {

    // correct id from uri if in the wrong format (or missing)!!
    $deviceid = Bioreactor::formatDeviceid($id);

    // get the last data entry record. Use the record_on time
    // and go backwards from that time to retrieve records
    try {
      $most_recent_phreading = Phreading::where('deviceid', '=', $deviceid)->orderBy('recorded_on', 'desc')->first();
      if ( is_null($most_recent_phreading)) {
        App::abort(404);
      }
    }
    catch (\Exception $e) {
      $start_time = Carbon::now();
      return $start_time;
    }
    $last_time = new Carbon($most_recent_phreading->recorded_on);

    // subtract # of hours. We need to use a new Carbon or it will
    // just point at the old one anyways!
    $start_time = new Carbon($last_time);
    $start_time->subHours($data_size);

    // load the data for this bioreactor (device) site
    try {
      if ( $data_size==24 ) {
        $this->_getHourlySummaryPhreadingData($deviceid,$start_time,$last_time);
      }
      else {
        $this->phreadings = Phreading::where('deviceid', '=', $deviceid)->where('recorded_on', '>', $start_time->toDateTimeString() )->orderBy('recorded_on', 'desc')->get();
      }
    }
    catch (\Exception $e) {
      $message = Lang::get('export.no_phreading_data_found');
      dd($message);
      //return Redirect::to('error')->with('message', $message);
    }

    return $last_time;
  }

  /**
   * Builds the x and y Phreading graph arrays that are passed to the
   * javascript Chart builder. The Phreading records must already
   * have been loaded into the Phreading Collection in this class
   *
   * @param string $x_axis_style ='default' 'default' is time. 'dot' is a dot
   *
   * @throws Exception if Phreading have not been loaded from table yet
   *
   * @return Array Mixed  x and y Phreading chart data
   */
  public function _buildXYPhreadingData($x_axis_style='default')
  {

    // put the data in the correct form for the charts JS library
    // generate an x and y array
    // x holds labels as mm:ss format
    // y holds y_phreadings as nnnnnn.n format

    $this->x_phreadings = [];
    $this->y_phreadings = [];

    // abort if the phreadings have not been loaded
    // indicates that getphreading data has not been called
    if ( ! is_null ($this->phreadings) && count($this->phreadings)>0) {

      // reverse the order to make the graph more human like
      $rev_ph = $this->phreadings->reverse();

      foreach ($rev_ph as $phreading) {

        $dt = new carbon($phreading->recorded_on);

        switch($x_axis_style)
        {
          case 'dot':
            $this->x_phreadings[] = '.';
            break;
          default:
            $this->x_phreadings[] = $dt->format('h:i');
            break;
        }
        $this->y_phreadings[] = sprintf("%6.1f",$phreading->ph);
      }
    }

    // just put something in if there is no data
    // otherwise no graph will be generated
    if (is_null ($this->phreadings) || (count($this->phreadings) < 1) )
    {
      $this->x_phreadings[]='0';
      $this->y_phreadings[]=0;
    }

    return [
      'x_data' => $this->x_phreadings,
      'y_data' => $this->y_phreadings
    ];
  }

}
