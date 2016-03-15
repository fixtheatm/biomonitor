<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
	/*
	 * Show all users. Only availble if the logged in user
	 * is an admin
	 *
	 *
	 */
	public function index() {	

		// get all the users to show on the map

		$users = User::all();

		//dd($users->toJson());

	    return view('User.index', ['route'			=> 'users',
		                             'header_title'		=> 'All Users',
									 'dbdata'			=> $users
									]);	
	}

	public function show($id)
    {

		// load the record from the table
		try {
			$user = User::where('id', '=', $id)->firstOrFail();
		}
		catch (\Exception $e) {
			$message = 'Sorry! Invalid id';
			dd($message);
			//return Redirect::to('error')->with('message', $message);
		}
		//dd($user);

	    return view('User.user', [	'route'				=> 'user',
 									'header_title'		=> 'Edit User',
									'user'				=> $user
								]);	
    }



	public function create()
    {
        return view('User.user');
    }

    public function update(Request $request)
    {

		if ( $request->id !="")	{

			// load the record from the table
			try {
				$user = User::where('id', '=', $request->id)->firstOrFail();
			}
			catch (\Exception $e) {
				$message = 'Sorry! Invalid id';
				dd($message);
				//return Redirect::to('error')->with('message', $message);
			}
			$user->name = $request->name;
			$user->email = $request->email;
			$user->deviceid = $request->deviceid;

			if ($request->isadmin===null)
				$user->isadmin='0';
			else
				$user->isadmin='1';

			//dd($user);

			$user->save();

			return redirect('/users');
		}
		else {
		}
	}
}
