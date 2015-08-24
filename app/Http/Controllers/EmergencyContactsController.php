<?php namespace App\Http\Controllers;

use Auth;
use Mail;

use App\EmergencyContact;
use App\Http\Requests\EmergencyContactRequest;
use App\User;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use DB;
use Carbon\Carbon;

class EmergencyContactsController extends Controller {

	/**
	 * Display a listing of the resource.
	 */
	public function index()
	{
		$e_contacts = Auth::user()->EmergencyContacts()->get();

		return view('contacts.index', compact('e_contacts'));
	}

	/**
	 * Show the form for creating a new resource.
	 */
	public function create()
	{
		return view('contacts.create');
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(EmergencyContactRequest $request)
	{
		//drop everything not a digit from phone
		$request['phone'] = preg_replace("/[^0-9,.]/", "", $request->phone);
		//create contact
		$contact = $this->createEmergencyContact($request);
		//notify contact
		$this->notifyEmergencyContact($request, $contact->id);

		return redirect('/contacts')->with([
			'flash_message' => $request->name.' has been added as an Emergency Contact.',
			'alert_class' => 'alert-success'
		]);
	}

	/**
	* Create a new Emergency Contact.
	*/
	private function createEmergencyContact(EmergencyContactRequest $request) 
	{
		$e_contact = Auth::user()->EmergencyContacts()->create($request->all());
		return $e_contact;
	}

	/**
	* Notify an Emergency Contact that they have been added
	**/
	private function notifyEmergencyContact(EmergencyContactRequest $request, $contact_id) 
	{
		$user = Auth::user()->first_name." ".Auth::user()->last_name;
		$token = $this->generateConfirmationToken($contact_id);
		Mail::send('emails.confirmContact', ['user' => $user, 'token' => $token], function($message) use ($request)
		{
		    $message->to($request->email, $request->name)
		    ->from('alerts@adventurealert.ca', 'AdventureAlert Notifications')
		    ->subject('You have been added as an Emergency Contact!');
		});
	}

	/**
	* create and store a verification token for an added Emergency Contact
	**/
	private function generateConfirmationToken($contact_id) 
	{
		//create a token: hash_hmac('sha256', str_random(15), 'tunafish');
		$token = hash_hmac('sha256', str_random(15), 'tunafish');
		//toss this token into ecConfirmationTokens table
		DB::table('ec_confirmation_tokens')->insert([
			'contact_id' => $contact_id, 
			'token' => $token,
			'created_at' => Carbon::now()
		]);
		//return the token.
		return $token;
	}

	/**
	* remove token from database when Emergency Contact accepts
	**/
	public function contactAccept($token)
	{
		//check token still exists
		if ($this->tokenExists($token))
		{
			//grab associated user's name
			$contact_id = $this->getContactID($token);
			$user = $this->getUser($contact_id);
			//look up token in DB and delete
			$this->deleteToken($token);

			//NOTIFY USER
			$this->notifyUser($contact_id, 1);
			
			/* THIS SHOULD NOT PASS TO HOME, NEEDS SEPARATE MESSAGING PAGE */
			return redirect('/')->with([
				'flash_message' => 'Thank you, you are now an Emergency Contact for '.$user->first_name.' '.$user->last_name.'.',
				'alert_class' => 'alert-success'
			]);
		}
		else
		{
			return redirect('/')->with([
				'flash_message' => 'Your information is no longer in our system. The user may have deleted you.',
				'alert_class' => 'alert-warning'
			]);
		}
		
	}

	/**
	* remove Emergency Contact from database when Emergency Contact declines
	**/
	public function contactDecline($token)
	{
		//check token still exists
		if ($this->tokenExists($token))
		{
			//look up token in DB to get contact ID
			$contact_id = $this->getContactID($token);

			//NOTIFY USER
			$this->notifyUser($contact_id, 0);
			
			//look up token in DB and delete
			$this->deleteToken($token);
			
			//delete contact where ID = id
			$e_contact = EmergencyContact::find($contact_id);
			if ($e_contact->delete())
			{	
				/* THIS SHOULD NOT PASS TO HOME, NEEDS SEPARATE MESSAGING PAGE */
				return redirect('/')->with([
					'flash_message' => 'Your information has been removed from our system.',
					'alert_class' => 'alert-success'
				]);
			}
		}
		else
		{
			return redirect('/')->with([
				'flash_message' => 'Your information is no longer in our system. The user may have deleted you.',
				'alert_class' => 'alert-warning'
			]);
		}
	}

	/**
	* checks for tokens >= 2 days old and deletes associated Emergency Contact
	**/
	public function expiredTokens() 
	{
		$tokens = DB::table('ec_confirmation_tokens')->get();

		foreach ($tokens as $token)
		{
			$created = Carbon::parse($token->created_at);
			if ($created->diffInHours(Carbon::now()) >= 48)
			{	
				$contact_id = $this->getContactID($token->token);
				$e_contact = EmergencyContact::find($contact_id);
				$e_contact->delete();
				$this->deleteToken($token->token);
			}
		}
	}

	/**
	* delete a token
	**/
	private function deleteToken($token)
	{
		DB::table('ec_confirmation_tokens')->where('token', '=', $token)->delete();	
	}
	/**
	* check a token exists
	**/
	private function tokenExists($token)
	{
		return DB::table('ec_confirmation_tokens')->where('token', $token)->first();
	}
	/**
	* fetch the contact_id of a token
	**/
	private function getContactID($token)
	{
		return DB::table('ec_confirmation_tokens')->where('token', $token)->pluck('contact_id');
	}
	/**
	* fetch the user of a contact_id
	**/
	private function getUser($contact_id)
	{
		$e_contact = EmergencyContact::find($contact_id);
		return $e_contact->user;
	}

	/**
	* Notify the user that the Emergency Contact has accepted or declined
	**/
	private function notifyUser($contact_id, $emailToSend)
	{
		//get associated user
		$user = $this->getUser($contact_id);
		//get contact
		$e_contact = EmergencyContact::find($contact_id);

		//check which email to send
		if ($emailToSend == 1)
		{
			//send email
			Mail::send('emails.ecAccept', ['user' => $user, 'e_contact' => $e_contact], function($message) use ($user, $e_contact)
			{
			    $message->to($user->email, $user->name)
			    		->from('alerts@adventurealert.ca', 'AdventureAlert Notifications')
			    		->subject($e_contact->name.' has accepted!');
			});
		}
		else
		{
			//send email
			Mail::send('emails.ecDecline', ['user' => $user, 'e_contact' => $e_contact], function($message) use ($user, $e_contact)
			{
			    $message->to($user->email, $user->name)
			    		->from('alerts@adventurealert.ca', 'AdventureAlert Notifications')
			    		->subject($e_contact->name.' has declined!');
			});
		}
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
	 */
	public function edit($id)
	{
		$e_contact = EmergencyContact::find($id);

		//check contact owner
		if (($e_contact->user_id != Auth::user()->id))
		{
			return redirect('/contacts')->with([
				'flash_message' => 'You can only edit your own Emergency Contacts.',
				'alert_class' => 'alert-warning'
			]);
		}

		return view('contacts.edit', compact('e_contact'));
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update($id, EmergencyContact $e_contact, EmergencyContactRequest $request)
	{
		$e_contact->where('id', '=', $id)->update($request->except('_method','_token'));
		return redirect('/contacts')->with([
			'flash_message' => $request->name.' has been updated',
			'alert_class' => 'alert-success'
		]);
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy($id)
	{
		$e_contact = EmergencyContact::find($id);
		//delete associated confirmation token, if it exists
		DB::table('ec_confirmation_tokens')->where('contact_id', '=', $id)->delete();

		if ($e_contact->delete())
		{
			//flash success message to Session
			Session::flash('flash_message', 'Contact has been deleted.');
			Session::flash('alert_class', 'alert-success');
			return "success";
		}
	}

}
