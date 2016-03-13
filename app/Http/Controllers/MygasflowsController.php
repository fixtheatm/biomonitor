<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Bioreactor;

use Carbon\Carbon;

class MygasflowsController extends Controller
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
	 * Show the users gas production flows
	 *
	 * @param int $hrs default 3. number of hours of data to view. (only 3 or 24 right now)
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

		$end_datetime = $this->getGasflowData($id, $hrs);

		// put the data in the correct form for the charts JS library
		// generate an x and y array
		// x holds labels as mm:ss format
		// y holds temperatures as nn.nnn format

		$gasflow_axis_data = $this->_buildXYGasflowData();
		//dd($gasflow_axis_data);

		// pass data it to the view

	    return view('GasFlows.mygasflows', ['route' => 'mygasflows',
	                                'header_title'	=> 'My BioRector Gas Flows ',
		                             'id'				=> $id,
									 'bioreactor'		=> $bioreactor,
									 'end_datetime'     => $end_datetime->toDateTimeString(),
									 'x_gasflow_data'	=> $gasflow_axis_data['x_data'],
									 'y_gasflow_data'	=> $gasflow_axis_data['y_data'],
									 'dbdata'			=> $this->gasflows
									]);	

    }

}
