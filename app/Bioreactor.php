<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bioreactor extends Model
{
	/*
	 * correct id if in the wrong format (or missing)!!
	 *
	 *
	 *
	 */
	public static function formatDeviceid($id) {
		return sprintf("%05d", $id);
	}
}
