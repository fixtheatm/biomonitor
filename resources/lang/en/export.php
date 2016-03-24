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

    'you_are_not_an_admin'	=> 'Sorry! You are NOT an admin and cannot perform this function',
    'cannot_delete_yourself'	=> 'Sorry! You cannot delete yourself. Bad idea and existentially wrong',

	// BioReactor Controller strings 

    'all_bioreactors'			=> 'All Bioreactors',
    'add_bioreactor'			=> 'Add Bioreactor',
    'edit_bioreactor'			=> 'Edit Bioreactor',
    'invalid_bioreactor_id'		=> 'Sorry! Invalid bioreactor id',
    'cannot_add_bioreactors'	=> 'Sorry! You are NOT an admin and cannot add bioreactors',

	// User Controller strings 

    'all_users'			=> 'All Users',
    'add_user'			=> 'Add User',
    'edit_user'			=> 'Edit User',
    'invalid_user_id'	=> 'Sorry! Invalid user id',
    'cannot_add_users'	=> 'Sorry! You are NOT an admin and cannot add users',

	// Controller strings

    'invalid_deviceid'				=> 'Sorry! Invalid deviceid',
    'no_temperature_data_found'		=> 'Sorry! No temperature data was found',
    'no_lightreading_data_found'	=> 'Sorry! No light reading data was found',
    'no_gasflow_data_found'			=> 'Sorry! No gas flow data was found',



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
