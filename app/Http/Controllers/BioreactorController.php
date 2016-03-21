<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Hash;
use App\Bioreactor;
use Excel;
use Carbon\Carbon;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class BioreactorController extends Controller
{
	/**
	 * Show all bioreactors. Only available if the logged in user
	 * is an admin
	 *
	 *
	 */
	public function index() {	

		// get all the bioreactors to show

		$bioreactors = Bioreactor::all();

		//dd($bioreactors->toJson());

	    return view('Bioreactor.index', ['route'			=> 'bioreactors',
		                             'header_title'		=> 'All Bioreactors',
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

		// get all the bioreactors to show

		//$bioreactors = Bioreactor::all();
		//dd($bioreactors);

		$bioreactors = Bioreactor::select('name', 'deviceid', 'city', 'country', 'last_datasync_at', 'created_at', 'updated_at', 'latitude', 'longitude')->get();

		Excel::create('bioreactors', function($excel) use ($bioreactors) {

			// Set the title
			$excel->setTitle('Bioreactor List');

			// Chain the setters
			$excel->setCreator('Solar BioCells')
					->setCompany('Solar BioCells');

			// Call them separately
			$excel->setDescription('List of Bioreactors');


			$excel->sheet('Bioreactor List', function ($sheet) use ($bioreactors) {
				$sheet->row(1, array('Name','BioReactor ID', 'City', 'Country', 'Last Data Sync', 'Created On', 'Last Updated', 'Latitude', 'Longitude'));

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

		// load the record from the table
		try {
			$bioreactor = Bioreactor::where('id', '=', $id)->firstOrFail();
		}
		catch (\Exception $e) {
			$message = 'Sorry! Invalid id';
			dd($message);
			//return Redirect::to('error')->with('message', $message);
		}
		//dd($bioreactor);

	    return view('Bioreactor.bioreactor', [	'route'				=> 'bioreactor',
 									'header_title'		=> 'Edit Bioreactor',
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

		// load the record from the table
		try {
			$bioreactor = Bioreactor::where('id', '=', $id)->firstOrFail();
		}
		catch (\Exception $e) {
			$message = 'Sorry! Invalid id';
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
		// get the record of the logged in bioreactor
		// and make sure they are an admin

		if ( !Auth::user()->isadmin)
		{
			$message = 'Sorry! You are NOT an admin and cannot add bioreactors';
			dd($message);
		}


		$bioreactor = new Bioreactor();

		// look into the table and try to guess at the next deviceid
		// Admin user can change this on the screen.
		$bioreactor->deviceid = $bioreactor->getNextDeviceID();

	    return view('Bioreactor.bioreactor', [	'route'				=> 'bioreactor',
 									'header_title'		=> 'Add Bioreactor',
									'bioreactor'				=> $bioreactor
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
		// the id will be non-empty for editing an existing bioreactor.
		//
		if ( $request->id !="")	{

			// load the record from the table
			try {
				$bioreactor = Bioreactor::where('id', '=', $request->id)->firstOrFail();
			}
			catch (\Exception $e) {
				$message = 'Sorry! Invalid id';
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
