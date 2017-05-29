<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Bioreactor;

use Carbon\Carbon;

class MylightreadingsController extends Controller
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
	 * Show the users light readings
	 *
	 *
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

		// load the data for this site
		// returns recorded_on date of last (most recent) record

		$end_datetime = $this->getLightreadingData($id, $hrs);
		if (is_null($this->lightreadings))
		{
		 $this->lightreadings = array();
		}

		// put the data in the correct form for the charts JS library
		// generate an x and y array
		// x holds labels as mm:ss format
		// y holds µmol photons / (m^2 S) as nnnnn.n format

		$axis_data = $this->_buildXYLightreadingData();
		//dd($axis_data);

		// pass data it to the view

	    return view('LightReadings.mylightreadings', ['route' => 'mylightreadings',
		                             'id'				=> $id,
									 'bioreactor'		=> $bioreactor,
									 'end_datetime'     => $end_datetime->toDateTimeString(),
									 'x_lightreading_data'	=> $axis_data['x_data'],
									 'y_lightreading_data'	=> $axis_data['y_data'],
									 'dbdata'			=> $this->lightreadings
									]);

    }

}
