<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTripPlansTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('trip_plans', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->unsigned();
			$table->string('title');
			$table->string('type');
			$table->timestamp('start_time');
			$table->timestamp('end_time');
			$table->string('offset');
			$table->text('description');
			$table->decimal('checkIn_lat', 8, 6)->signed();
			$table->decimal('checkIn_long', 9, 6)->signed();
			$table->time('alert_timer');
			$table->boolean('active');
			$table->boolean('notified');
			$table->timestamps();

			//user_id as foreign key
			$table->foreign('user_id')->references('id')->on('users');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('trip_plans');
	}

}
