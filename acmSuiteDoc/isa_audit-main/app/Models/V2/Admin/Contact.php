<?php

namespace App\Models\V2\Admin;

use App\Models\V2\Admin\Corporate;
use App\Traits\V2\UtilitiesTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
class Contact extends Model
{
  use Notifiable, UtilitiesTrait;

  protected $table = 't_contacts';
  protected $primaryKey = 'id_contact';

  /**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
    'id_corporate',
    'ct_email',
    'ct_phone_office',
    'ct_ext',
    'ct_cell',
    'ct_first_name',
    'ct_second_name',
    'ct_last_name',
    'degree',
  ];

  /*
	 * Attributes
	 */
	protected $appends = [
		'full_name',
	];

  /**
   * Route notifications for the mail channel.
   *
   * @param  \Illuminate\Notifications\Notification  $notification
   * @return array|string
   */
  public function routeNotificationForMail($notification)
  {
    return $this->ct_email;
  }

  /**
	 * Get Full Name
	 */
	public function getFullNameAttribute()
	{
		$shortName = "{$this->ct_first_name} {$this->ct_second_name}";
		$fullName = "{$this->ct_first_name} {$this->ct_second_name} {$this->ct_last_name}";
		return is_null($this->ct_last_name) ? $shortName : $fullName;
	}
}