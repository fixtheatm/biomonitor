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
	public function show()
	{

		$user = Auth::user();

		//dd($user);

	    return view('Password.password', [	'route'				=> 'password',
 									'header_title'		=> 'Change Password',
									'user'				=> $user
								]);	
	
	}

	public function update(Request $request)
	{
		$user = Auth::user();

		$user->password = Hash::make($request->password1);
		$user->updated_at = Carbon::now();

		//dd ($request->GoBackTo);

		$user->save();

		return redirect($request->GoBackTo);
	
	}
}