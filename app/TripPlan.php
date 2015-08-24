<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class TripPlan extends Model {

	protected $fillable = ['user_id','title','type','start_time','end_time','offset','checkIn_lat','checkIn_long','alert_timer'];

	protected $hidden = ['user_id'];

	public function user()
	{
		return $this->belongsTo('App\User');
	}

	public function getDates()
	{
		return array('start_time', 'end_time');
	}

	public function scopeActive($query)
	{
		return $query->where('active', '=', '1');
	}

	public function scopeNotified($query)
	{
		return $query->where('notified', '=', '0');
	}

}
