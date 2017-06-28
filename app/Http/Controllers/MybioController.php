<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Bioreactor;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Lang;

use Carbon\Carbon;
use Route;

class MybioController extends Controller
{
  /**
   * {@inheritdoc}
   */
  public function __construct()
  {
    parent::__construct();
    $this->middleware('auth');
  }


  /**
   * Show the users bioreactor summary view
   */
  public function index() {
    list($view_spec, $view_parm) = $this->bioreactorSiteView( Auth::user()->deviceid );
    // dd($view_spec, $view_parm);
    return view( $view_spec, array_replace([], $view_parm, [
      'header_title'        => Lang::get('bioreactor.all_graph_page_title'),
      'show_excel'          => true,
      'show_button'         => true,
      'show_inline'         => false,
      'show_graph'          => true,
    ]));
  }// ./index()


  /**
   * Show a block of sensor measurements for the current user´s bioreactor
   *
   * @param int $hrs default 3. number of hours of data to view.
   * @param int $end default now. the most recent (recorded_on) measurement to show
   */
  public function graph( $hrs=3, $end='now' )
  {
    $route_base = explode( '/', Route::current()->uri())[0];
    $sensor = $this->route_to_sensor[ $route_base ];

    list($view_spec, $view_parm) = $this->sensorGraphFullView( Auth::user()->deviceid, $sensor, $hrs, $end );
    return view( $view_spec, $view_parm );
  }// ./graph(…)
}
