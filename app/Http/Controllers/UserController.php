<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Hash;
use App\User;
use App\Bioreactor;
use Carbon\Carbon;
use DB;
use Excel;
use Lang;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class UserController extends Controller
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
	 * Show all users. Only available if the logged in user
	 * is an admin
	 *
	 *
	 */
	public function index() {

		// die of this is not an admin
		$this->checkIfIsAnAdmin();

		// get all the users to show

		$users = User::all();

		//dd($users->toJson());

	    return view('User.index', [	'route'				=> 'users',
									'header_title'		=> Lang::get('export.all_users'),
									'dbdata'			=> $users
									]);
	}

	/**
	 * Download all users as Excel spreadsheet. Only available if the logged in user
	 * is an admin
	 *
	 *
	 */
	public function excel() {

		// die of this is not an admin
		$this->checkIfIsAnAdmin();

		// get all the users to show

		//$users = User::all();
		//dd($users);

		$users = User::select('name', 'email', 'deviceid', 'isadmin', 'created_at', 'updated_at')->get();

		$excel_filename = Lang::get('export.users_filename');

		Excel::create($excel_filename, function($excel) use ($users) {

			$creator		= Lang::get('export.solar_biocells');
			$company		= Lang::get('export.solar_biocells');

			$title			= Lang::get('export.users_list');

			$description	= Lang::get('export.users_description');
			$sheet_name		= Lang::get('export.users_sheet_name');

			// Set the title, etc
			$excel->setTitle($title)
				  ->setCreator($creator)
				  ->setCompany($company)
				  ->setDescription($description);

			$excel->sheet($sheet_name, function ($sheet) use ($users) {

				$bioreactor_id_col_title	= Lang::get('export.bioreactor_id_col_title');
				$created_on_col_title		= Lang::get('export.created_on_col_title');
				$last_updated_col_title		= Lang::get('export.last_updated_col_title');
				$users_name_col_title		= Lang::get('export.users_name_col_title');
				$users_email_col_title		= Lang::get('export.users_email_col_title');
				$users_isadmin_col_title	= Lang::get('export.users_isadmin_col_title');

				$sheet->row(1, array($users_name_col_title, $users_email_col_title, $bioreactor_id_col_title,
								$users_isadmin_col_title,
								$created_on_col_title, $last_updated_col_title));

				$sheet->fromArray($users, null, 'A2', false, false);
			});

		})->export('xls');

	}

	/**
	 * Show a single users record for editing
	 *
	 * @param int $id The numeric id of the user
	 *
	 */
	public function show($id)
    {

		// die of this is not an admin
		$this->checkIfIsAnAdmin();

		// load the record from the table
		try {
			$user = User::where('id', '=', $id)->firstOrFail();
		}
		catch (\Exception $e) {
			$message = Lang::get('export.invalid_user_id');
			dd($message);
			//return Redirect::to('error')->with('message', $message);
		}
		//dd($user);

		$bioreactors = Bioreactor::select('deviceid', DB::raw("coalesce(deviceid,'')||' '||coalesce(name,'') as wholename"))->orderBy('wholename')->lists('wholename', 'deviceid');

	    return view('User.user', [	'route'				=> 'user',
 									'header_title'		=> Lang::get('export.edit_user'),
									'user'				=> $user,
									'bioreactors'		=> $bioreactors
								]);
    }

	/**
	 * Delete a single users record
	 *
	 * @param int $id The numeric id of the user
	 *
	 */
	public function delete($id)
    {

		// die of this is not an admin
		$this->checkIfIsAnAdmin();

		// make sure the user does not try to delete themselves
		if ($id == Auth::user()->id) {
			dd(Lang::get('export.cannot_delete_yourself'));
		}

		// load the record from the table
		try {
			$user = User::where('id', '=', $id)->firstOrFail();
		}
		catch (\Exception $e) {
			$message = Lang::get('export.invalid_user_id');
			dd($message);
			//return Redirect::to('error')->with('message', $message);
		}

		//dd($user);

		$user->delete();

		// finish by sending the user back to the list of all users
		return redirect('/users');
	}


	/**
	 * Show the editing form for a new user
	 *
	 *
	 *
	 */
	public function create()
    {
		// die of this is not an admin
		$this->checkIfIsAnAdmin();

		$user = new User();

		// setup the default password for a new user
		$user->setDefaultPassword();

		$bioreactors = Bioreactor::select('deviceid', DB::raw("coalesce(deviceid,'')||' '||coalesce(name,'') as wholename"))->orderBy('wholename')->lists('wholename', 'deviceid');

	    return view('User.user', [	'route'				=> 'user',
 									'header_title'		=> Lang::get('export.add_user'),
									'user'				=> $user,
									'bioreactors'		=> $bioreactors
								]);
    }

	/**
	 * Process a post from editing a user or creating a new
	 *  user.
	 *
	 * @param Request $request the posted data
	 */
    public function update(Request $request)
    {
		// die of this is not an admin
		$this->checkIfIsAnAdmin();

		// the id will be non-empty for editing an existing user.
		//
		if ( $request->id !="")	{

			// load the record from the table
			try {
				$user = User::where('id', '=', $request->id)->firstOrFail();
			}
			catch (\Exception $e) {
				$message = Lang::get('export.invalid_user_id');
				dd($message);
				//return Redirect::to('error')->with('message', $message);
			}
		}
		else { // a new user
			$user = new User();
			// hash the new user password for saving
			$user->password = Hash::make($request->password);
		}

		// set the common data updates
		$user->name = $request->name;
		$user->email = $request->email;
		$user->deviceid = $request->deviceid;

		// since isadmin is a checkbox, it won't be passed if it wasn't
		// checked off
		if ($request->isadmin===null)
			$user->isadmin='0';
		else
			$user->isadmin='1';

		// set the last updated date to now
		$user->updated_at = Carbon::now();

		//dd($user);

		$user->save();

		// finish by sending the user back to the list of all users
		return redirect('/users');
	}
}
