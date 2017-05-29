<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bioreactor extends Model
{
	/**
	 * correct id if in the wrong format (or missing)!!
	 *
	 * @param $id device id
	 * @return String id as 5 digits with leading zeros
	 */
	public static function formatDeviceid( $id ) {
		return sprintf( "%05d", $id );
	}

	/**
	 * Attempts to suggest the next deviceid for a new bioreactor
	 * Gets the highest deviceid and then adds one to the deviceid
	 *
	 * @return string - the suggested deviceid ex. 00008
	 */
	public function getNextDeviceID()
	{
		try {
			$bioreactor = Bioreactor::orderBy('deviceid', 'desc')->first();
		}
		catch (\Exception $e) {
			// no records ??

			return $this->formatDeviceid(1);
		}
		if ( is_numeric($bioreactor->deviceid)) {
			return $this->formatDeviceid($bioreactor->deviceid + 1);
		}
		else {
			return $this->formatDeviceid(1);
		}
	}

}
