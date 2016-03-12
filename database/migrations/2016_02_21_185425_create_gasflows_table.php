<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGasflowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gasflows', function (Blueprint $table) {
            $table->increments('id');

            $table->string('deviceid');
            $table->double('flow', 15, 7)->default(0);
			$table->dateTime('recorded_on'); // date & time recorded on users raspberry 

            $table->timestamps();

			// indices
			$table->index(array('deviceid', 'recorded_on'));

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('gasflows');
    }
}
