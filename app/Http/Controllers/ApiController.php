<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Bioreactor;
use App\Temperature;
use App\Gasflow;
use App\Lightreading;
use App\Phreading;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Response;
use Carbon\Carbon;


class ApiController extends Controller
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
  * Process request for a data dump of json results back
  * to the user
  *
  * @example http://laravel.dev/api?dtype=temp
  *
  * In development. DO NOT USE
  */
  public function api(Request $request) {

  dd('In development');

    // temperature readings
    $temp_data = array( [
      'deviceid'   => '34234',
      'temp'       =>  '21.012',
      'date'       =>  '2016-01-23 12:34:00'
      ],
      [
      'deviceid'   => '34234',
      'temp'       =>  '21.112',
      'date'       =>  '2016-01-23 12:35:00'
      ]
      );

    // light intensity readings
    $light_data = array( [
      'deviceid'   => '34234',
      'light'      =>  '21.012',
      'date'       =>  '2016-01-23 12:34:00'
      ],
      [
      'deviceid'   => '34234',
      'light'      =>  '21.112',
      'date'       =>  '2016-01-23 12:35:00'
      ]
      );

    // gas flow «volume¦rate» readings
    $flow_data = array( [
      'deviceid'   => '34234',
      'flow'       =>  '21.012',
      'date'       =>  '2016-01-23 12:34:00'
      ],
      [
      'deviceid'   => '34234',
      'flow'       =>  '21.112',
      'date'       =>  '2016-01-23 12:35:00'
      ]
      );

    // potential of hydrogen: acidity or basicity of aqueous solution
    $ph_data = array( [
      'deviceid'   => '34234',
      'ph'         =>  '21.012',
      'date'       =>  '2016-01-23 12:34:00'
      ],
      [
      'deviceid'   => '34234',
      'ph'         =>  '21.112',
      'date'       =>  '2016-01-23 12:35:00'
      ]
      );

    //dd (Request::input('dtype'));

    //dd($request->all());

    $dtype = $request->input('dtype');

    switch( $dtype) {
      case 'temp':
        return $temp_data;
        break;
      case 'light':
        return $light_data;
        break;
      case 'flow':
        return $flow_data;
        break;
      case 'ph':
        return $ph_data;
        break;
      default:
        return Lang::get('messages.no_api_datatype');
        break;
    }
  }
}
