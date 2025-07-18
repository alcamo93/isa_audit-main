<?php

namespace App\Models\V2\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Traits\V2\UtilitiesTrait;

class Notification extends Model
{
	use UtilitiesTrait;

	protected $table = 'notifications';
	protected $primaryKey = 'id';
	public $incrementing = false;
	protected $keyType = 'string';
	
	/*
	 * Attributes
	 */
	protected $appends = [
		'title',
		'link',
		'body',
		'date_format',
		'active',
	];

	/**
	 * Get title for json data
	 */
	public function getTitleAttribute()
	{
		$data = json_decode($this->data);
		return $data->title;
	}

	/**
	 * Get link for json data
	 */
	public function getLinkAttribute()
	{
		$data = json_decode($this->data);
		return url($data->link);
	}

	/**
	 * Get body for json data
	 */
	public function getBodyAttribute()
	{
		$data = json_decode($this->data);
		return $data->body;
	}

	/**
	 * Get Full Name
	 */
	public function getActiveAttribute()
	{
		return is_null($this->read_at);
	}

	/*
	 * Get date format
	 */
	public function getDateFormatAttribute()
	{
		return $this->getFormatDate($this->created_at);
	}

	/**
	 * Get resources by auth user
	 */
	public function scopeAuthFilters($query)
	{
		$query->where('notifiable_id', Auth::id())->orderBy('created_at', 'DESC');
	}

	/**
	 * get resources by unread notifications
	 */
	public function scopeUnreadNotifications($query)
	{
		$query->whereNull('read_at');
	}
}