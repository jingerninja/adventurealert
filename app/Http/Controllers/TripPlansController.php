<?php namespace App\Http\Controllers;

use Auth;
use Carbon\Carbon;
use Mail;

use App\TripPlan;
use App\Http\Requests\TripPlanRequest;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class TripPlansController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$trips = Auth::user()->TripPlans()->get();
		//instantiate as a Collection so that I don't have to use array notation in the view
		$activeTrips = new \Illuminate\Database\Eloquent\Collection;
		foreach ($trips as $trip)
		{
			if ($trip->active == 1)
			{
				$activeTrips->add($trip);
			}
		}

		return view('trips.index', compact(['trips','activeTrips']));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return view('trips.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(TripPlanRequest $request)
	{
		//convert datetimepicker objects to proper Carbon instances
		$request['start_time'] = Carbon::createFromFormat('Y/m/d g:i A', $request->start_time);
		$request['end_time'] = Carbon::createFromFormat('Y/m/d g:i A', $request->end_time);

		//create trip
		$this->createTripPlan($request);
		
		return redirect('/trips')->with([
			'flash_message' => 'Your Trip Plan has been Saved.',
			'alert_class' => 'alert-success'
		]);
	}
	public function createTripPlan(TripPlanRequest $request)
	{
		$trip = Auth::user()->TripPlans()->create($request->all());
		return $trip;
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$trip = TripPlan::find($id);

		//check contact owner
		if (($trip->user_id != Auth::user()->id))
		{
			return redirect('/trips')->with([
				'flash_message' => 'You can only edit your own Trip Plans.',
				'alert_class' => 'alert-warning'
			]);
		}

		return view('trips.edit', compact('trip'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id, TripPlan $trip, TripPlanRequest $request)
	{
		$request['start_time'] = Carbon::createFromFormat('Y/m/d g:i A', $request->start_time);
		$request['end_time'] = Carbon::createFromFormat('Y/m/d g:i A', $request->end_time);

		$trip->where('id', '=', $id)->update($request->except('_method','_token'));
		return redirect('/trips')->with([
			'flash_message' => $request->title.' has been updated',
			'alert_class' => 'alert-success'
		]);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$trip = TripPlan::find($id);

		if ($trip->delete())
		{
			//flash success message to Session
			Session::flash('flash_message', 'Trip has been deleted.');
			Session::flash('alert_class', 'alert-success');
			return "success";
		}
	}

	/**
	* Check in to trip
	**/
	public function checkIn($id)
	{
		$trip = TripPlan::find($id);
		$trip->active = 1;
		if($trip->save())
		{
			return redirect('/trips')->with([
				'flash_message' => 'You have checked in to '.$trip->title,
				'alert_class' => 'alert-success'
			]);
		}
		else
		{
			return redirect('/trips')->with([
				'flash_message' => 'There has been an error',
				'alert_class' => 'alert-danger'
			]);
		}
	}
	/**
	* Check out of trip
	**/
	public function checkOut($id)
	{
		$trip = TripPlan::find($id);
		$trip->active = 0;
		if($trip->save())
		{
			return redirect('/trips')->with([
				'flash_message' => 'You have checked out of '.$trip->title,
				'alert_class' => 'alert-success'
			]);
		}
		else
		{
			return redirect('/trips')->with([
				'flash_message' => 'There has been an error',
				'alert_class' => 'alert-danger'
			]);
		}
	}

	/**
	* check for missed trip check outs
	**/
	public function missedTrips()
	{
		$trips = TripPlan::active()->notified()->get();

		foreach ($trips as $trip)
		{
			//capture current time
			$now = Carbon::now();
			//set up values for trip end and timer, accounting for User's UTC offset
			$end_time = new Carbon($trip->end_time, $trip->offset);
			//parse alert_timer into hours and minutes
			$alert_timer = explode(":",$trip->alert_timer);
			//add hours and minutes from timer to end_time
			$notifyTime = $end_time->addHours($alert_timer[0])->addMinutes($alert_timer[1]);
			//compare current time to notification threshold
			if ($now->gte($notifyTime))
			{
				//notify users emergency contacts
				$this->notifyEmergencyContacts($trip);
				
				//set notified flag
				$trip->notified = 1;
				$trip->save();
			}
		}
	}

	/**
	* Notify a User's Emergency Contacts of a missed check out
	**/
	private function notifyEmergencyContacts(TripPlan $trip)
	{
		//fetch user
		$user = $trip->user;
		//fetch emergency contacts
		$e_contacts = $user->EmergencyContacts;
		
		foreach ($e_contacts as $e_contact)
		{
			Mail::send('emails.missedTrip', ['user' => $user, 'e_contact' => $e_contact, 'e_contacts' => $e_contacts, 'trip' => $trip], function($message) use ($user, $e_contact, $e_contacts, $trip)
			{
			    $message->to($e_contact->email, $e_contact->name)
			    		->from('alerts@adventurealert.ca', 'AdventureAlert Notifications')
			    		->subject($user->first_name.' has not checked back in from a trip!');
			});
		}
	}
}
