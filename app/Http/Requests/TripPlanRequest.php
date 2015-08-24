<?php namespace App\Http\Requests;

use App\Http\Requests\Request;

class TripPlanRequest extends Request {

	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'title' => 'required',
			'type' => 'required|in:land,sea,air',
			'start_time' => 'required|date',
			'end_time' => 'required|date|after:start_time',
			'alert_timer' => 'required|date_format:"H:i"'
		];
	}

}
