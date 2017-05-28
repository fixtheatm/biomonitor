<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Bioreactor;

use Carbon\Carbon;

class MyphreadingsController extends Controller
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
   * Show the users ph readings
   *
   * @param int $hrs default 3. number of hours of data to view. (only 3 or 24 right now)
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

    // load the data for this bioreactor (device) site
    // returns recorded_on date of last (most recent) record

    $end_datetime = $this->getPhreadingData($id, $hrs);
    if (is_null($this->phreadings))
    {
     $this->phreadings = array();
    }

    // put the data in the correct form for the charts JS library
    // generate an x and y array
    // x holds labels as mm:ss format
    // y holds ph as nnnnn.n format

    $axis_data = $this->_buildXYPhreadingData();
    //dd($axis_data);

    // pass data into the view

    return view('PhReadings.myphreadings', ['route' => 'myphreadings',
      'id'                => $id,
      'bioreactor'        => $bioreactor,
      'end_datetime'      => $end_datetime->toDateTimeString(),
      'x_phreading_data'  => $axis_data['x_data'],
      'y_phreading_data'  => $axis_data['y_data'],
      'dbdata'            => $this->phreadings
      ]);

    }

}
