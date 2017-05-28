<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePhReadingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('phreadings', function (Blueprint $table) {
            $table->increments('id');

            $table->string('deviceid');
            $table->double('ph', 15, 7)->default(0);
            $table->dateTime('recorded_on'); // date & time recorded on users rPi

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
        Schema::drop('phreadings');
    }
}
