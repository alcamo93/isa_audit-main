<?php

namespace App\Models\V2\Catalogs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Traits\V2\UtilitiesTrait;

class ProfileType extends Model
{
	use HasFactory, UtilitiesTrait;

	protected $table = 't_profile_types';
	protected $primaryKey = 'id_profile_type';

	const ADMIN_GLOBAL = 1;
	const ADMIN_OPERATIVE = 2;
	const CORPORATE = 3;
	const COORDINATOR = 4;
	const OPERATIVE = 5;  

	/**
	 * The relation that allow use in api request.
	 *
	 * @var array
	 */
	protected $allow_filter = [
		'owner' => ['field' => 'owner', 'type' => 'string', 'relation' => null],
	];

	/**
	 * 
	 */
	public function scopeFilterPerUserAuth($query) 
	{
		if ( !Auth::check() ) return;
		$level = Session::get('user')['profile_level'];
		$types = collect([1,2,3,4,5]);
		if ($level == $this->ADMIN_GLOBAL) return;
		$allows = $types->filter(fn($item) => $item >= $level)->values()->toArray();
		$query->whereIn('id_profile_type', $allows);
	}
}
