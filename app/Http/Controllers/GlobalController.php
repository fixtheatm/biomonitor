<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Bioreactor;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Response;

use Carbon\Carbon;


class GlobalController extends Controller
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
	 * return all bioreactors as JSON
	 *
	 *
	 *
	 */
	public function getjson() {	

		// get all the bioreactors to show on the map

		$bioreactors = Bioreactor::all();

		return Response::json( array('markers' => $bioreactors->toArray() , 200 ) );

	}

	/*
	 * Show all bioreactors. Default view is the map of the world.
	 * The user can also view them in a list form
	 *
	 * @TODO Add filter to get rid of inactive Bioreactors
	 *
	 */
	public function index() {	

		// get all the bioreactors to show on the map

		$bioreactors = Bioreactor::all();

		//dd($bioreactors->toJson());

	    return view('Global.index', ['route'			=> 'global',
		                             'header_title'		=> 'Global BioReactor Status',
									 'dbdata'			=> $bioreactors
									]);	
	}


	/*
	 * Shows single bioreactor that is selected from the global
	 * map or from the global list
	 *
	 * @param string $id = deviceid of the bioreactor ex. 00001
	 *
	 */
	public function show($id) {	

		$bioreactor = $this->getBioreactorFromId($id);
		//dd($bioreactor);
	
		// get the temperature data from the database for this location
		// the datetime of the last (most recent) record is returned.
		// If no records then the current datetime us returned

		$end_datetime = $this->getTemperatureData($id);

		// builds the x and y datapoint values for the graph

		$temp_axis_data = $this->_buildXYTemperatureData();
		//dd($axis_data);

		// load and prep all the lightreadings data
		$this->getLightreadingData($id);

		// put the data in the correct form for the charts JS library
		// generate an x and y array
		// x holds labels as mm:ss format
		// y holds lux as nnnnn.n format

		$light_axis_data = $this->_buildXYLightreadingData();
		//dd($light_axis_data);

		// load and prep all the gasflows data
		$this->getGasflowData($id);

		// put the data in the correct form for the charts JS library
		// generate an x and y array
		// x holds labels as mm:ss format
		// y holds lux as nn.nnn format

		$gasflow_axis_data = $this->_buildXYGasflowData();
		//dd($gasflow_axis_data);


		// pass data it to the view

	    return view('Global.show', [ 'route'			=> 'single',
		                             'id'				=> $id,
	                                 'header_title'		=> 'Single BioReactor Status',
									 'bioreactor'		=> $bioreactor,
 									 'end_datetime'     => $end_datetime->toDateTimeString(),
									 'x_temperature_data'	=> $temp_axis_data['x_data'],
									 'y_temperature_data'	=> $temp_axis_data['y_data'],
									 'x_lightreading_data'	=> $light_axis_data['x_data'],
									 'y_lightreading_data'	=> $light_axis_data['y_data'],
									 'x_gasflow_data'	=> $gasflow_axis_data['x_data'],
									 'y_gasflow_data'	=> $gasflow_axis_data['y_data'],

									 ]);	
	}

}