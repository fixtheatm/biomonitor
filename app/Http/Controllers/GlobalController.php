<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Bioreactor;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Response;
use Lang;

use Carbon\Carbon;


class GlobalController extends Controller
{

  /**
   * return all bioreactors as JSON
   *
   * @return json containing information about all bioreactors
   */
  public function getjson()
  {
    // get all the bioreactors to show on the map
    $bioreactors = Bioreactor::all();

    return Response::json( array('markers' => $bioreactors->toArray() , 200 ));
  }

  /**
   * Show all bioreactors. Default view is the map of the world.
   *
   * The user can also view them in a list form
   *
   * @TODO Add filter to get rid of inactive Bioreactors
   */
  public function index()
  {
    // get all the bioreactors to show on the map
    $bioreactors = Bioreactor::all();
    //dd($bioreactors->toJson());

    return view( 'Global.index', [
      'route'      => 'global',
      'header_title' => 'All Bioreactors',
      'dbdata'     => $bioreactors
    ]);
  }// ./index(…)


  /**
   * Show current sensor graphs for a single bioreactor
   *
   * Selected from the global map or from the global list
   *
   * @param string $id deviceid of the bioreactor ex. 00001
   */
  public function show( $id )
  {
    list($view_spec, $view_parm) = $this->bioreactorSiteView( $id );
    // dd($view_spec, $view_parm);
    return view( $view_spec, array_replace([], $view_parm, [
      'header_title'        => 'Bioreactor #' . $id,
      'show_excel'          => false,
      'show_button'         => false,
      'show_inline'         => true,
      'show_graph'          => true,
    ]));
  }// ./show(…)


  /**
   * Graph a block of sensor measurements for a bioreactor based on form data
   *
   * @param Request $request graph configuration information from the form
   * @param $id string bioreactor id
   */
  public function formgraph( Request $request, $id )
  {
    $this->validate($request, [
      'sensor_to_graph' => "required|issensor:{$this->getKnownSensors()}|either:hours,hours2|either:graph_end_date,graph_end_date2",
      'graph_interval' => 'required|only_custom:hours,hours2',
      'graph_end_date' => 'date',
      'graph_end_date2' => 'date',
    ]);
    $form_attributes = $request->input();
    $sensor_name = $request->input('sensor_to_graph');
    $tz_offset = $request->input('timezone_offset', '0');
    $browser_tzo = $tz_offset / -60;// Negate hours offset

    // dd(array_key_exists( 'hours', $form_attributes), array_key_exists( 'hours2', $form_attributes), array_key_exists( 'graph_end_date', $form_attributes), array_key_exists( 'graph_end_date2', $form_attributes), array_key_exists( 'submit_inline', $form_attributes), array_key_exists( 'submit_graph', $form_attributes));

    $max_date = Carbon::parse(
      $request->input('graph_end_date',
      $request->input('graph_end_date2', 'noenddate')), $browser_tzo);
    $max_date->setTimeZone(0);
    $hrs = intval(($request->input('graph_interval') === 'custom') ?
      $request->input('hours', $request->input('hours2', 0)) :
      $request->input('graph_interval'));

    // dd([$id, $sensor_name, $hrs, $max_date, $request]);
    list($view_spec, $view_parm) = $this->sensorGraphFullView( $id, $sensor_name, $hrs, $max_date );
    return view( $view_spec, $view_parm );
  }// ./formgraph(…)
}
