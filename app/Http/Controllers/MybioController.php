<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Bioreactor;

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
	 *
	 *
	 *
	 */
	public function index() {	

		// the deviceid should not be blank or bogus as 
		// it is from the user record enforced with a foreign key constraint

		$id = Auth::user()->deviceid;
		//dd($id);

		$bioreactor = $this->getBioreactorFromId($id);
		//dd($bioreactor);

		// Load and prep all the temperature data
		$this->getTemperatureData($id);

		// build the x and y datapoints

		$temp_axis_data = $this->_buildXYTemperatureData();
		//dd($axis_data);

		// load and prep all the lightreadings data
		$lightreadings = $this->getLightreadingData($id);

		// put the data in the correct form for the charts JS library
		// generate an x and y array
		// x holds labels as mm:ss format
		// y holds lux as nnnnn.n format

		$light_axis_data = $this->_buildXYLightreadingData();
		//dd($light_axis_data);

		// load and prep all the gasflows data
		$gasflows = $this->getGasflowData($id);

		// put the data in the correct form for the charts JS library
		// generate an x and y array
		// x holds labels as mm:ss format
		// y holds lux as nn.nnn format

		$gasflow_axis_data = $this->_buildXYGasflowData();
		//dd($gasflow_axis_data);


		// pass data it to the view

	    return view('MyBio.mybio', ['route' => 'mybio',
		                             'id'				=> $id,
	                                 'header_title'	=> 'My BioReactor Status',
									 'bioreactor'		=> $bioreactor,
									 'x_temperature_data'	=> $temp_axis_data['x_data'],
									 'y_temperature_data'	=> $temp_axis_data['y_data'],
									 'x_lightreading_data'	=> $light_axis_data['x_data'],
									 'y_lightreading_data'	=> $light_axis_data['y_data'],
									 'x_gasflow_data'	=> $gasflow_axis_data['x_data'],
									 'y_gasflow_data'	=> $gasflow_axis_data['y_data'],

									]);	

	}
}
