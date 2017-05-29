<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Hash;
use App\User;
use Carbon\Carbon;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class PasswordController extends Controller
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
	 * Show the current logged in user record for editing
	 * They can only set the password
	 *
	 */
	public function show()
	{
		// get the record of the logged in user
		$user = Auth::user();

		//dd($user);

	    return view('Password.password', [	'route'				=> 'password',
									'user'				=> $user
								]);

	}

	/**
	 * Process a post from editing the logged in user
	 *
	 *
	 * @param Request $request the posted data
	 */
	public function update(Request $request)
	{
		// get the record of the logged in user
		$user = Auth::user();

		// hash the new password
		$user->password = Hash::make($request->password1);

		// set the last updated date to now
		$user->updated_at = Carbon::now();

		//dd ($request->GoBackTo);

		$user->save();

		// the location of the redirect is variable
		// depending on whether the user is changing their own password
		// of the admin is doing it from the global user list
		//
		return redirect($request->GoBackTo);

	}
}
