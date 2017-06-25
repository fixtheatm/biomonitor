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

  'solar_biocells'            => 'Solar BioCells',
  'starting_on'               => 'Starting On',
  'ending_on'                 => 'Ending On',
  'enter_start_date'          => 'Start Date',
  'enter_end_date'            => 'End Date',
  'date_recorded_col_title'   => 'Date Recorded',
  'time_recorded_col_title'   => 'Time Recorded',
  'bioreactor_id_col_title'   => 'Bioreactor ID',
  'created_on_col_title'      => 'Created On',
  'last_updated_col_title'    => 'Last Updated',
  'spreadsheet_subject'       => 'Biomonitor Data Export',
  'spreadsheet_keywords'      => 'maatwebsite, excel, export, sensor, measurement',
  'utc_suffix'                => ' UTC',

  // Move the following to ?messages? (not export anyway)
  'you_are_not_an_admin'      => 'Sorry! You are NOT an admin and cannot perform this function',
  'cannot_delete_yourself'    => 'Sorry! You cannot delete yourself. Bad idea and existentially wrong',

  // BioReactor Controller strings

  'all_bioreactors'           => 'All Bioreactors',
  'add_bioreactor'            => 'Add Bioreactor',
  'edit_bioreactor'           => 'Edit Bioreactor',
  'invalid_bioreactor_id'     => 'Sorry! Invalid bioreactor id',
  'cannot_add_bioreactors'    => 'Sorry! You are NOT an admin and cannot add bioreactors',

  // User Controller strings

  'all_users'                 => 'All Users',
  'add_user'                  => 'Add User',
  'edit_user'                 => 'Edit User',
  'invalid_user_id'           => 'Sorry! Invalid user id',
  'cannot_add_users'          => 'Sorry! You are NOT an admin and cannot add users',

  // Controller strings

  'invalid_deviceid'          => 'Sorry! Invalid deviceid',
  'no_oxygen_data_found'      => 'Sorry! No gas production data was found',
  'no_light_data_found'       => 'Sorry! No light reading data was found',
  'no_ph_data_found'          => 'Sorry! No pH reading data was found',
  'no_temperature_data_found' => 'Sorry! No temperature data was found',

  // common detail

  'bioreactor_name_label'     => 'Name: ',
  'bioreactor_city_label'     => 'City: ',
  'bioreactor_cntry_label'    => 'Country: ',
  'bioreactor_id_label'       => 'ID#: ',
  'bioreactor_email_label'    => 'Email: ',
  'raw_to_spreadsheet_title'  => 'Raw Data Export to Excel',
  'raw_to_spreadsheet_btn'    => 'Raw Data to Excel',
  'start_export'              => 'Go',

  // oxygen production sensor data export

  'oxygen_filename'           => 'gasproduction',
  'oxygen_select'             => 'Gas Production',
  'oxygen_data'               => 'Gas Production Data',
  'oxygen_description'        => 'Gas Production for Bioreactor',
  'oxygen_sheet_name'         => 'Oxygen Data',
  'oxygen_col_title'          => 'mL',

  // light sensor data export

  'light_filename'            => 'lightreadings',
  'light_select'              => 'Light',
  'light_data'                => 'Light Reading Data',
  'light_description'         => 'Light intensity sensor readings for bioreactor',
  'light_sheet_name'          => 'Light Reading Data',
  'light_col_title'           => 'µmol photons/(m^2 S)',

  // ph sensor data export

  'ph_filename'               => 'phreadings',
  'ph_select'                 => 'pH',
  'ph_data'                   => 'pH Reading Data',
  'ph_description'            => 'pH Readings for Bioreactor',
  'ph_sheet_name'             => 'pH Reading Data',
  'ph_col_title'              => 'pH',

  // temperature sensor data export

  'temperature_filename'      => 'temperatures',
  'temperature_select'        => 'Temperature',
  'temperature_data'          => 'Temperature Data',
  'temperature_description'   => 'Temperatures for Bioreactor',
  'temperature_sheet_name'    => 'Temperature Data',
  'temperature_col_title'     => 'Degrees Celsius',

  // users data export

  'users_filename'            => 'users',
  'users_list'                => 'User List',
  'users_description'         => 'List of users registered for Bioreactor login',
  'users_sheet_name'          => 'User List',
  'users_name_col_title'      => 'Name',
  'users_email_col_title'     => 'Email',
  'users_isadmin_col_title'   => 'Is Admin?',

  // bioreactor data export

  'bioreactors_filename'      => 'bioreactors',
  'bioreactors_list'          => 'Bioreactor List',
  'bioreactors_description'   => 'List of Bioreactors',
  'bioreactors_sheet_name'    => 'Bioreactor List',
  'bioreactors_name_col_title'=> 'Name',
  'bioreactors_city_col_title'=> 'City',
  'bioreactors_country_col_title'       => 'Country',
  'bioreactors_last_datasync_col_title' => 'Last Data Sync',
  'bioreactors_latitude_col_title'      => 'Latitude',
  'bioreactors_longitude_col_title'     => 'Longitude',

];
