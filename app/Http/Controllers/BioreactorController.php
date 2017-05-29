<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Hash;
use App\Bioreactor;
use Excel;
use Lang;
use Carbon\Carbon;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class BioreactorController extends Controller
{

	/**
	 * Get the record of the logged in user
	 * and make sure they are an admin. Die if they are not
	 *
	 *
	 */
	protected function checkIfIsAnAdmin() {

		if ( !Auth::user()->isadmin )
		{
			$message = Lang::get('export.you_are_not_an_admin');
			dd($message);
		}
	}

	/**
	 * Show all bioreactors. Only available if the logged in user
	 * is an admin
	 *
	 *
	 */
	public function index() {

		// die of this is not an admin
		$this->checkIfIsAnAdmin();

		// get all the bioreactors to show

		$bioreactors = Bioreactor::all();

		//dd($bioreactors->toJson());

	    return view('Bioreactor.index', ['route'		=> 'bioreactors',
		                             'header_title'		=> Lang::get('export.all_bioreactors'),
									 'dbdata'			=> $bioreactors
									]);
	}


	/**
	 * Download all Bioreactors as Excel spreadsheet. Only available if the logged in user
	 * is an admin
	 *
	 *
	 */
	public function excel() {

		// die of this is not an admin
		$this->checkIfIsAnAdmin();

		// get all the bioreactors to show

		//$bioreactors = Bioreactor::all();
		//dd($bioreactors);

		$bioreactors = Bioreactor::select('name', 'deviceid', 'city', 'country', 'last_datasync_at', 'created_at', 'updated_at', 'latitude', 'longitude')->get();

		$excel_filename = Lang::get('export.bioreactors_filename');

		Excel::create($excel_filename, function($excel) use ($bioreactors) {

			$creator		= Lang::get('export.solar_biocells');
			$company		= Lang::get('export.solar_biocells');

			$title			= Lang::get('export.bioreactors_list');

			$description	= Lang::get('export.bioreactors_description');
			$sheet_name		= Lang::get('export.bioreactors_sheet_name');

			// Set the title, etc
			$excel->setTitle($title)
				  ->setCreator($creator)
				  ->setCompany($company)
				  ->setDescription($description);

			$excel->sheet($sheet_name, function ($sheet) use ($bioreactors) {

				$bioreactor_id_col_title	= Lang::get('export.bioreactor_id_col_title');
				$created_on_col_title		= Lang::get('export.created_on_col_title');
				$last_updated_col_title		= Lang::get('export.last_updated_col_title');

				$bioreactors_name_col_title		= Lang::get('export.bioreactors_name_col_title');
				$bioreactors_city_col_title		= Lang::get('export.bioreactors_city_col_title');
				$bioreactors_country_col_title	= Lang::get('export.bioreactors_country_col_title');
				$bioreactors_last_datasync_col_title	= Lang::get('export.bioreactors_last_datasync_col_title');
				$bioreactors_latitude_col_title	= Lang::get('export.bioreactors_latitude_col_title');
				$bioreactors_longitude_col_title	= Lang::get('export.bioreactors_longitude_col_title');

				$sheet->row(1, array($bioreactors_name_col_title,$bioreactor_id_col_title,
						$bioreactors_city_col_title, $bioreactors_country_col_title,
						$bioreactors_last_datasync_col_title,
						$created_on_col_title, $last_updated_col_title,
						$bioreactors_latitude_col_title, $bioreactors_longitude_col_title));

				$sheet->fromArray($bioreactors, null, 'A2', false, false);
			});

		})->export('xls');

	}


	/**
	 * Show a single bioreactor record for editing
	 *
	 * @param int $id The numeric id of the bioreactor
	 *
	 */
	public function show($id)
    {
		// die of this is not an admin
		$this->checkIfIsAnAdmin();

		// load the record from the table
		try {
			$bioreactor = Bioreactor::where('id', '=', $id)->firstOrFail();
		}
		catch (\Exception $e) {
			$message = Lang::get('export.invalid_bioreactor_id');
			dd($message);
			//return Redirect::to('error')->with('message', $message);
		}
		//dd($bioreactor);

	    return view('Bioreactor.bioreactor', [	'route'			=> 'bioreactor',
 									'header_title'				=> Lang::get('export.edit_bioreactor'),
									'bioreactor'				=> $bioreactor
								]);
    }

	/**
	 * Delete a single bioreactor record
	 *
	 * @param int $id The numeric id of the bioreactor
	 *
	 */
	public function delete($id)
    {
		// die of this is not an admin
		$this->checkIfIsAnAdmin();

		// load the record from the table
		try {
			$bioreactor = Bioreactor::where('id', '=', $id)->firstOrFail();
		}
		catch (\Exception $e) {
			$message = Lang::get('export.invalid_bioreactor_id');
			dd($message);
			//return Redirect::to('error')->with('message', $message);
		}

		//dd($bioreactor);

		$bioreactor->delete();

		// finish by sending the user back to the list of all bioreactors
		return redirect('/bioreactors');
	}


	/**
	 * Show the editing form for a new bioreactor
	 *
	 *
	 *
	 */
	public function create()
    {
		// die of this is not an admin
		$this->checkIfIsAnAdmin();

		$bioreactor = new Bioreactor();

		// look into the table and try to guess at the next deviceid
		// Admin user can change this on the screen.
		$bioreactor->deviceid = $bioreactor->getNextDeviceID();

	    return view('Bioreactor.bioreactor', [	'route'		=> 'bioreactor',
 									'header_title'			=> Lang::get('export.add_bioreactor'),
									'bioreactor'			=> $bioreactor
								]);
    }

	/**
	 * Process a post from editing a bioreactor or creating a new
	 *  bioreactor.
	 *
	 * @param Request $request the posted data
	 */
    public function update(Request $request)
    {
		// die of this is not an admin
		$this->checkIfIsAnAdmin();

		// the id will be non-empty for editing an existing bioreactor.
		//
		if ( $request->id !="")	{

			// load the record from the table
			try {
				$bioreactor = Bioreactor::where('id', '=', $request->id)->firstOrFail();
			}
			catch (\Exception $e) {
				$message = Lang::get('export.invalid_bioreactor_id');
				dd($message);
				//return Redirect::to('error')->with('message', $message);
			}
		}
		else { // a new bioreactor
			$bioreactor = new Bioreactor();
			// deviceid can only be set on creation
			$bioreactor->deviceid = $request->deviceid;
		}

		// set the common data updates
		$bioreactor->name		= $request->name;
		$bioreactor->city		= $request->city;
		$bioreactor->country	= $request->country;
		$bioreactor->latitude	= $request->latitude;
		$bioreactor->longitude	= $request->longitude;
		$bioreactor->email		= $request->email;


		// set the last updated date to now
		$bioreactor->updated_at = Carbon::now();

		//dd($bioreactor);

		$bioreactor->save();

		// finish by sending the bioreactor back to the list of all bioreactors
		return redirect('/bioreactors');
	}
}
