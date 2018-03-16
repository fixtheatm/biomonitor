<?php

use Illuminate\Database\Seeder;
// use Illuminate\Database\Eloquent\Model;
// use App\User as User; // to use Eloquent Model
use Carbon\Carbon;

class UserTableSeeder extends Seeder
{
    /**
     * Seed the user table.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
          'name' => 'useradmin',
          'email' => 'dbadmin@solarbiocells.com',
          'password' => bcrypt('StartHere'),
          'remember_token' => str_random(18),
          'deviceid' => '00000',
          'isadmin' => '1',
          'created_at' => Carbon::now()->toDateTimeString(),
          'updated_at' => Carbon::now()->toDateTimeString()
        ]);

        DB::table('bioreactors')->insert([
          'name' => 'Solarbiocells',
          'city' => 'Calgary',
          'country' => 'Canada',
          'email' => 'info@solarbiocells.com',
          'description' => 'dummy bioreactor to support administration operations',
          'deviceid' => '00000',
          'active' => '0',
          'latitude' => '51.079948',
          'longitude' => '-114.125534',
          'created_at' => Carbon::now()->toDateTimeString(),
          'updated_at' => Carbon::now()->toDateTimeString()
        ]);
        // User::create( [
        // ]);
    }
}
