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
	 *  Register with the Auth so users must be logged in to access
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

	/**
	 * Download GasFlows as Excel spreadsheet.
	 *
	 *
	 * @param Request $request - data from the form
	 *
	 */
	public function exportGasFlows(Request $request) {

		// Get the deviceid of the Bioreactor that the user has acess to

		$deviceid = Auth::user()->deviceid;

		//dd($deviceid);

		// get the records from the database
		// based on the deviceid and the date range

		$gasflows = Gasflow::select('deviceid', 'flow',
							DB::raw("DATE(recorded_on) as date_recorded"), DB::raw("TIME(recorded_on) as time_recorded"))
			->where('deviceid','=',$deviceid)
			->where('recorded_on', '>=', $request->start_date)
			->where('recorded_on', '<=', $request->end_date)
			->get();

		// generate the spreadsheet and download it through the browser

		$excel_filename = Lang::get('export.temperatures_filename');

		Excel::create($excel_filename, function($excel) use ($gasflows,$request) {

			$creator		= Lang::get('export.solar_biocells');
			$company		= Lang::get('export.solar_biocells');

			$title			= Lang::get('export.gasflows_data');

			$description	= Lang::get('export.gasflows_description');
			$sheet_name		= Lang::get('export.gasflows_sheet_name');

			// Set the title, etc
			$excel->setTitle($title)
				  ->setCreator($creator)
				  ->setCompany($company)
				  ->setDescription($description);

			$excel->sheet($sheet_name, function ($sheet) use ($gasflows,$request) {

				$starting_on	= Lang::get('export.starting_on');
				$ending_on		= Lang::get('export.ending_on');

				$date_recorded_col_title	= Lang::get('export.date_recorded_col_title');
				$time_recorded_col_title	= Lang::get('export.time_recorded_col_title');
				$bioreactor_id_col_title	= Lang::get('export.bioreactor_id_col_title');
				$flow_col_title				= Lang::get('export.flow_col_title');

				$sheet->row(1, array($starting_on,$request->start_date));
				$sheet->row(2, array($ending_on,$request->end_date));

				$sheet->row(4, array($bioreactor_id_col_title, $flow_col_title,
								$date_recorded_col_title, $time_recorded_col_title));

				$sheet->fromArray($gasflows, null, 'A5', false, false);
			});

		})->export('xls');

	}

	/**
	 * Download Temperatures as Excel spreadsheet.
	 *
	 *
	 * @param Request $request - data from the form
	 *
	 */
	public function exportTemperatures(Request $request) {

		// Get the deviceid of the Bioreactor that the user has acess to

		$deviceid = Auth::user()->deviceid;

		//dd($deviceid);

		// get the records from the database
		// based on the deviceid and the date range

		$temperatures = Temperature::select('deviceid', 'temperature',
							DB::raw("DATE(recorded_on) as date_recorded"), DB::raw("TIME(recorded_on) as time_recorded"))
			->where('deviceid','=',$deviceid)
			->where('recorded_on', '>=', $request->start_date)
			->where('recorded_on', '<=', $request->end_date)
			->get();

		// generate the spreadsheet and download it through the browser

		$excel_filename = Lang::get('export.temperatures_filename');

		Excel::create($excel_filename, function($excel) use ($temperatures,$request) {

			$creator		= Lang::get('export.solar_biocells');
			$company		= Lang::get('export.solar_biocells');

			$title			= Lang::get('export.temperature_data');

			$description	= Lang::get('export.temperature_description');
			$sheet_name		= Lang::get('export.temperature_sheet_name');

			// Set the title, etc
			$excel->setTitle($title)
				  ->setCreator($creator)
				  ->setCompany($company)
				  ->setDescription($description);

			$excel->sheet($sheet_name, function ($sheet) use ($temperatures,$request) {

				$starting_on	= Lang::get('export.starting_on');
				$ending_on		= Lang::get('export.ending_on');

				$date_recorded_col_title	= Lang::get('export.date_recorded_col_title');
				$time_recorded_col_title	= Lang::get('export.time_recorded_col_title');
				$bioreactor_id_col_title	= Lang::get('export.bioreactor_id_col_title');
				$temperature_col_title		= Lang::get('export.temperature_col_title');


				$sheet->row(1, array($starting_on,$request->start_date));
				$sheet->row(2, array($ending_on,$request->end_date));

				$sheet->row(4, array($bioreactor_id_col_title, $temperature_col_title,
								$date_recorded_col_title, $time_recorded_col_title));

				$sheet->fromArray($temperatures, null, 'A5', false, false);
			});

		})->export('xls');

	}

	/**
	 * Download LightReadings as Excel spreadsheet.
	 *
	 *
	 * @param Request $request - data from the form
	 *
	 */
	public function exportLightReadings(Request $request)
	{
		// Get the deviceid of the Bioreactor that the user has acess to

		$deviceid = Auth::user()->deviceid;

		//dd($deviceid);

		// get the records from the database
		// based on the deviceid and the date range

		$lightreadings = Lightreading::select('deviceid', 'lux',
							DB::raw("DATE(recorded_on) as date_recorded"), DB::raw("TIME(recorded_on) as time_recorded"))
			->where('deviceid','=',$deviceid)
			->where('recorded_on', '>=', $request->start_date)
			->where('recorded_on', '<=', $request->end_date)
			->get();

		// generate the spreadsheet and download it through the browser

		$excel_filename = Lang::get('export.lightreadings_filename');

		Excel::create($excel_filename, function($excel) use ($lightreadings,$request) {

			$creator		= Lang::get('export.solar_biocells');
			$company		= Lang::get('export.solar_biocells');

			$title			= Lang::get('export.lightreadings_data');

			$description	= Lang::get('export.lightreadings_description');
			$sheet_name		= Lang::get('export.lightreadings_sheet_name');

			// Set the title, etc
			$excel->setTitle($title)
				  ->setCreator($creator)
				  ->setCompany($company)
				  ->setDescription($description);


			$excel->sheet($sheet_name, function ($sheet) use ($lightreadings,$request) {

				$starting_on	= Lang::get('export.starting_on');
				$ending_on		= Lang::get('export.ending_on');

				$date_recorded_col_title	= Lang::get('export.date_recorded_col_title');
				$time_recorded_col_title	= Lang::get('export.time_recorded_col_title');
				$bioreactor_id_col_title	= Lang::get('export.bioreactor_id_col_title');
				$lux_col_title				= Lang::get('export.lux_col_title');

				$sheet->row(1, array($starting_on,$request->start_date));
				$sheet->row(2, array($ending_on,$request->end_date));

				$sheet->row(4, array($bioreactor_id_col_title, 'µmol photons/(m^2 S)',
								$date_recorded_col_title,$time_recorded_col_title));

				$sheet->fromArray($lightreadings, null, 'A5', false, false);
			});

		})->export('xls');


	}

	/**
	 * Download data as Excel spreadsheet.
	 * User will have selected which datatype and a date range
	 * on the form. This handles the Form post.
	 *
	 * @param Request $request - data from the form
	 */
	public function export(Request $request) {

		//dd($request);

		// select which datatype to export

		switch($request->datatype_to_excel)	{
			case '1':
				$this->exportGasFlows($request);
				break;
			case '2':
				$this->exportLightReadings($request);
				break;
			case '3':
				$this->exportTemperatures($request);
				break;
		}

		return redirect('/home');

	}

}
