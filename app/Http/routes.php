<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

//auth & register
Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);

//home page
Route::get('/', 'PagesController@index');

//emergency contact accept & decline
Route::get('contacts/accept/{token}', 'EmergencyContactsController@contactAccept');
Route::get('contacts/decline/{token}', 'EmergencyContactsController@contactDecline');

Route::group(['middleware' => 'auth'], function()
{
	//emergency contact management
	Route::resource('contacts', 'EmergencyContactsController');
	//trip management
	Route::post('trips/{trips}/checkin', 'TripPlansController@checkIn');
	Route::get('trips/{trips}/checkout', 'TripPlansController@checkOut');
	//TEST ROUTE
	Route::get('trips/missed', 'TripPlansController@missedTrips');
	Route::resource('trips', 'TripPlansController');
});
