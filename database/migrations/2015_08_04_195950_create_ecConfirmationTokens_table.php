<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEcConfirmationTokensTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ec_confirmation_tokens', function(Blueprint $table)
		{
			$table->integer('contact_id')->unsigned();
			$table->string('token');
			$table->timestamp('created_at');

			//contact_id as foreign key
			$table->foreign('contact_id')->references('id')->on('emergency_contacts');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ec_confirmation_tokens');
	}

}
