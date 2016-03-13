<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Bioreactor;

use Carbon\Carbon;

class MytemperaturesController extends Controller
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

	/*
	 * Show the users recent temperatures in the graph
	 *
	 * @param int $hrs default 3. number of hours of data to view ( 3 or 24 )
	 *
	 */
	public function index($hrs=3) {	

	    //dd($hrs);

		// the deviceid should not be blank or bogus as 
		// it is from the user record enforced with a foreign key constraint

		$id = Auth::user()->deviceid;
		//dd($id);

		// load the record from the table
		$bioreactor = $this->getBioreactorFromId($id);
		//dd($bioreactor);

		// load the temperature data for this site
		// returns recorded_on date of last (most recent) record

		$end_datetime = $this->getTemperatureData($id, $hrs);

		// put the data in the correct form for the charts JS library
		// generate an x and y array
		// x holds labels as mm:ss format
		// y holds temperatures as nn.nn format

		$axis_data = $this->_buildXYTemperatureData();
		//dd($axis_data);

		// pass data it to the view

	    return view('Temperatures.mytemperatures', ['route' => 'mytemperatures',
	                                'header_title'	=> 'My BioRector Temperatures ',
		                             'id'				=> $id,
									 'bioreactor'		=> $bioreactor,
									 'end_datetime'     => $end_datetime->toDateTimeString(),
									 'x_temperature_data'	=> $axis_data['x_data'],
									 'y_temperature_data'	=> $axis_data['y_data'],
									 'dbdata'			=> $this->temperatures
									]);	

    }

}
