<?php

namespace App\Models\V2\Admin;

use Illuminate\Database\Eloquent\Model;
use App\Models\V2\Admin\User;

class Person extends Model
{
	protected $table = 't_people';
	protected $primaryKey = 'id_person';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'first_name',
		'second_name',
		'last_name',
		'rfc',
		'gender',
		'phone',
		'birthdate',
	];

	/*
	 * Attributes
	 */
	protected $appends = [
		'full_name',
	];
	
	/**
	 * Get the user that owns the Person
	 */
	public function user() 
	{
		return $this->belongsTo(User::class, 'id_person', 'id_person');
	}

	/**
	 * Get Full Name
	 */
	public function getFullNameAttribute()
	{
		$shortName = "{$this->first_name} {$this->second_name}";
		$fullName = "{$this->first_name} {$this->second_name} {$this->last_name}";
		return is_null($this->last_name) ? $shortName : $fullName;
	}
}