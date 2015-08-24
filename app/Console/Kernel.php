<?php namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel {

	/**
	 * The Artisan commands provided by your application.
	 *
	 * @var array
	 */
	protected $commands = [
		'App\Console\Commands\Inspire',
	];

	/**
	 * Define the application's command schedule.
	 *
	 * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
	 * @return void
	 */
	protected function schedule(Schedule $schedule)
	{
		$schedule->command('inspire')
				 ->hourly();

		//check 'expired' confirmation tokens
		$schedule->call('\App\Http\Controllers\EmergencyContactsController@expiredTokens')
				 ->hourly();

		//check active trips
		$schedule->call('\App\Http\Controllers\TripPlansController@missedTrips')
				 ->everyFiveMinutes();
	}

}
