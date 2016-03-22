<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Export Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during export to excel, etc for various
    | messages that we need to display to the user. 
    |
    */

    'solar_biocells'			=> 'Solar BioCells',
    'starting_on'				=> 'Starting On',
    'ending_on'					=> 'Ending On',
    'date_recorded_col_title'	=> 'Date Recorded',
    'time_recorded_col_title'	=> 'Time Recorded',
    'bioreactor_id_col_title'	=> 'Bioreactor ID',
    'created_on_col_title'		=> 'Created On',
    'last_updated_col_title'	=> 'Last Updated',

	// temperature data export

    'temperatures_filename'		=> 'temperatures',
    'temperature_data'			=> 'Temperature Data',
	'temperature_col_title'		=> 'Temperature',
	'temperature_description'	=> 'Temperatures for Bioreactor',
	'temperature_sheet_name'	=> 'Temperature Data',

	// light readings data export

    'lightreadings_filename'	=> 'lightreadings',
    'lightreadings_data'		=> 'Light Reading Data',
	'lightreadings_description'	=> 'Light Readings for Bioreactor',
	'lightreadings_sheet_name'	=> 'Light Reading Data',
	'lux_col_title'				=> 'Lux',

	// gas flow data export

    'gasflows_filename'			=> 'gasflows',
    'gasflows_data'				=> 'Gas Flow Data',
	'gasflows_description'		=> 'Gas Flow for Bioreactor',
	'gasflows_sheet_name'		=> 'Gas Flow Data',
	'flow_col_title'			=> 'Flow (x10)',

	// users data export

    'users_filename'			=> 'users',
    'users_list'				=> 'User List',
	'users_description'			=> 'List of users registered for Bioreactor login',
	'users_sheet_name'			=> 'User List',
	'users_name_col_title'		=> 'Name',
	'users_email_col_title'		=> 'Email',
	'users_isadmin_col_title'	=> 'Is Admin?',


	// bioreactor data export

    'bioreactors_filename'			=> 'bioreactors',
    'bioreactors_list'				=> 'Bioreactor List',
	'bioreactors_description'		=> 'List of Bioreactors',
	'bioreactors_sheet_name'		=> 'Bioreactor List',
	'bioreactors_name_col_title'	=> 'Name',
	'bioreactors_city_col_title'	=> 'City',
	'bioreactors_country_col_title'	=> 'Country',
	'bioreactors_last_datasync_col_title'	=> 'Last Data Sync',
	'bioreactors_latitude_col_title'	=> 'Latitude',
	'bioreactors_longitude_col_title'	=> 'Longitude',

];
