<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Hash;
use App\Temperature;
use App\Lightreading;
use App\Gasflow;
use App\Bioreactor;
use Carbon\Carbon;
use DB;
use Excel;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ExportController extends Controller
{

	public function exportGasFlows(Request $request) {	 
	 
		$deviceid = Auth::user()->deviceid;

		//dd($deviceid);

		$gasflows = Gasflow::select('deviceid', 'flow', 'recorded_on')
			->where('deviceid','=',$deviceid)
			->where('recorded_on', '>=', $request->start_date)
			->where('recorded_on', '<=', $request->end_date)
			->get();

		Excel::create('gasflows', function($excel) use ($gasflows,$request) {

			// Set the title
			$excel->setTitle('Gas Flow Data');

			// Chain the setters
			$excel->setCreator('Solar BioCells')
					->setCompany('Solar BioCells');

			// Call them separately
			$excel->setDescription('Gas Flows for Bioreactor');


			$excel->sheet('Gas Flow Data', function ($sheet) use ($gasflows,$request) {
				$sheet->row(1, array('Starting On',$request->start_date)); 
				$sheet->row(2, array('Ending On',$request->end_date));

				$sheet->row(4, array('BioReactor ID', 'Flow (x10)','Recorded On'));

				$sheet->fromArray($gasflows, null, 'A5', false, false);
			});

		})->export('xls');
	
	}

	public function exportTemperatures(Request $request) {	
	 
		$deviceid = Auth::user()->deviceid;

		//dd($deviceid);

		$temperatures = Temperature::select('deviceid', 'temperature', 'recorded_on')
			->where('deviceid','=',$deviceid)
			->where('recorded_on', '>=', $request->start_date)
			->where('recorded_on', '<=', $request->end_date)
			->get();

		Excel::create('temperatures', function($excel) use ($temperatures,$request) {

			// Set the title
			$excel->setTitle('Temperature Data');

			// Chain the setters
			$excel->setCreator('Solar BioCells')
					->setCompany('Solar BioCells');

			// Call them separately
			$excel->setDescription('Temperatures for Bioreactor');


			$excel->sheet('Temperature Data', function ($sheet) use ($temperatures,$request) {
				$sheet->row(1, array('Starting On',$request->start_date)); 
				$sheet->row(2, array('Ending On',$request->end_date));

				$sheet->row(4, array('BioReactor ID', 'Temperature','Recorded On'));

				$sheet->fromArray($temperatures, null, 'A5', false, false);
			});

		})->export('xls');
	
	}

	public function exportLightReadings(Request $request) {	 

		$deviceid = Auth::user()->deviceid;

		//dd($deviceid);

		$lightreadings = Lightreading::select('deviceid', 'lux', 'recorded_on')
			->where('deviceid','=',$deviceid)
			->where('recorded_on', '>=', $request->start_date)
			->where('recorded_on', '<=', $request->end_date)
			->get();

		Excel::create('lightreadings', function($excel) use ($lightreadings,$request) {

			// Set the title
			$excel->setTitle('Light Reading Data');

			// Chain the setters
			$excel->setCreator('Solar BioCells')
					->setCompany('Solar BioCells');

			// Call them separately
			$excel->setDescription('Light Readings for Bioreactor');


			$excel->sheet('Light Reading Data', function ($sheet) use ($lightreadings,$request) {
				$sheet->row(1, array('Starting On',$request->start_date)); 
				$sheet->row(2, array('Ending On',$request->end_date));

				$sheet->row(4, array('BioReactor ID', 'Lux','Recorded On'));

				$sheet->fromArray($lightreadings, null, 'A5', false, false);
			});

		})->export('xls');
	
	
	}

	/**
	 * Download all users as Excel spreadsheet. Only available if the logged in user
	 * is an admin
	 *
	 *
	 */
	public function export(Request $request) {	

		//dd($request);

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
