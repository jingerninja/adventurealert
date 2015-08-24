<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class EmergencyContact extends Model {

	protected $fillable = ['name','email','phone'];

	protected $hidden = ['user_id'];

	public function user()
	{
		return $this->belongsTo('App\User');
	}

}
