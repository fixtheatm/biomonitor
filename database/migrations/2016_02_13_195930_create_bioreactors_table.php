<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBioreactorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bioreactors', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name');
            $table->string('city')->nullable();
            $table->string('country')->nullable();
			$table->string('email')->nullable();		// master email address
			$table->text('description')->nullable();	// optional user generated project description

			$table->binary('picture')->nullable();		// optional user uploaded log or picture

            $table->string('deviceid');		// 5 digit unique identifier ex. 00001

			$table->boolean('active')->default(true);		// active or not

			$table->double('latitude', 15, 7)->default(0);
			$table->double('longitude', 15, 7)->default(0);

            $table->string('version')->nullable();		// current known device software version
			$table->dateTime('last_versionupdate_at')->nullable(); // last time software version was done

			$table->dateTime('last_datasync_at')->nullable(); // last time data was synced

            $table->timestamps();

			// indices
			$table->unique('deviceid');
			$table->index('name');
			$table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('bioreactors');
    }
}
