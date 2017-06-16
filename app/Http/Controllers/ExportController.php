<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;
use App\Temperature;
use App\Lightreading;
use App\Gasflow;
use App\Bioreactor;
use Carbon\Carbon;
use Excel;
use Lang;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ExportController extends Controller
{
  /**
   * Create a new controller instance.
   *
   * Register with the Auth so users must be logged in to access
   *
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
    $this->middleware( 'auth' );
  }


  /**
   * Download sensor data as an Excel spreadsheet
   *
   * @param Request $request - data from the form
   */
  public function export(Request $request) {
    // Get the deviceid of the Bioreactor that the user has access to
    $deviceid = Auth::user()->deviceid;
    $bioreactor = $this->getBioreactorFromId( $deviceid );

    // dd([$request->datatype_to_excel,$request->start_date,$request->end_date,$request->timezone_offset]);
    $sensor_name = $request->datatype_to_excel;
    // IDEA TODO handle $sensor_name === 'all'
    // $sensor_names = [ $sensor_name ]; // and iterate always
    $sensor_props = $this->sensors[ $sensor_name ];


    // Inspite of the above MDT bug, using numeric timezone offset works correctly
    // If the timezone offset from the browser is not a number, use 0
    $br_tzo = $request->timezone_offset / -60;// Negate hours offset
    $min_date = Carbon::parse($request->start_date, $br_tzo);
    $min_date->setTimeZone(0); // Get input date to UTC for DB compare
    $max_date = Carbon::parse($request->end_date, $br_tzo);
    $max_date->setTimeZone(0);
    $start_date_str = $min_date->format('Y-m-d H:i:s');
    $end_date_str = $max_date->format('Y-m-d H:i:s');

    // get database records based on the deviceid and the date range
    // $sensor_model =  self::MODEL_PREFIX . $sensor_props[ 'model' ];
    $sensor_model =  $sensor_props[ 'full_model' ];
    $data_property = $sensor_props[ 'prop' ];
    // IDEA do not include the deviceid in the db select: report as header instead
    // IDEA select full recorded_on field, and format for excel date
    $this->{ $data_property } = $sensor_model::select(
      'deviceid',
      $sensor_props[ 'data_field' ],
      DB::raw( "DATE(recorded_on) as date_recorded" ),
      DB::raw( "TIME(recorded_on) as time_recorded" ))
        ->where( 'deviceid',    '=',  $deviceid )
        ->where( 'recorded_on', '>=', $start_date_str)
        ->where( 'recorded_on', '<=', $end_date_str)
        ->get();
    // dd($this->{ $data_property });

    $excel_filename = Lang::get('export.' . $sensor_name . '_filename');
    Excel::create( $excel_filename, function( $excel )
        use ( $sensor_name, $data_property, $start_date_str, $end_date_str ) {
      // dd([$sensor_name, $data_property, $start_date_str, $end_date_str]);

      // Set the title, etc
      $excel->setTitle( Lang::get('export.' . $sensor_name . '_data' ))
        ->setCreator( Lang::get( 'export.solar_biocells' ))
        ->setLastModifiedBy( '2connect2biz.com' )
        ->setDescription( Lang::get('export.' . $sensor_name . '_description' ))
        ->setSubject( Lang::get( 'export.spreadsheet_subject' ))
        ->setKeywords( Lang::get( 'export.spreadsheet_keywords' ))
        ->setCompany( Lang::get('export.solar_biocells' ));
        // $company = $bioreactor->name
      // dd($excel);

      $sheet_name   = Lang::get('export.' . $sensor_name . '_sheet_name');
      $excel->sheet( $sheet_name, function( $sheet )
          use ( $sensor_name, $data_property, $start_date_str, $end_date_str ) {

        $starting_on  = Lang::get('export.starting_on');
        $ending_on    = Lang::get('export.ending_on');
        $date_recorded_col_title  = Lang::get('export.date_recorded_col_title');
        $time_recorded_col_title  = Lang::get('export.time_recorded_col_title');
        $bioreactor_id_col_title  = Lang::get('export.bioreactor_id_col_title');
        $data_col_title           = Lang::get('export.' . $sensor_name . '_col_title');

        $sheet->row(1, array($starting_on, $start_date_str . Lang::get('export.utc_suffix')));
        $sheet->row(2, array($ending_on, $end_date_str . Lang::get('export.utc_suffix')));

        $sheet->row(4, array($bioreactor_id_col_title,
          $data_col_title,
          $date_recorded_col_title, $time_recorded_col_title));

        $sheet->fromArray($this->{ $data_property }, null, 'A5', false, false);
      });

    })->export('xls');

    return redirect('/home');
  }
}

// DEBUG for Carbon github issue 968
// https://github.com/briannesbitt/Carbon/issues/968
// $datestr = "2016-06-15";
// $tst6 = Carbon::parse($datestr, -6);
// $tstmdt = Carbon::parse($datestr, 'MDT');
// $tstmst = Carbon::parse($datestr, 'MST');
// $tst6->setTimeZone(0);
// $tstmdt->setTimeZone(0);
// $tstmst->setTimeZone(0);
// dd([$datestr,
//   Carbon::parse($datestr, -6)->format('D, Y-m-d H:i:s O (T)'),
//   Carbon::parse($datestr, 'MDT')->format('D, Y-m-d H:i:s O (T)'),
//   Carbon::parse($datestr, 'MST')->format('D, Y-m-d H:i:s O (T)'),
//   $tst6->format('D, Y-m-d H:i:s O (T)'),
//   $tstmdt->format('D, Y-m-d H:i:s O (T)'),
//   $tstmst->format('D, Y-m-d H:i:s O (T)')
// ]);
