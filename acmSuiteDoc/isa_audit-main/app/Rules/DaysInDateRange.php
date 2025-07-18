<?php

namespace App\Rules;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;

class DaysInDateRange implements Rule
{
	protected $init_date;
	protected $end_date;
	protected $is_required;

	public function __construct($initDate, $endDate, $isRequired)
	{
		$this->init_date = $initDate;
		$this->end_date = $endDate;
		$this->is_required = filter_var($isRequired, FILTER_VALIDATE_BOOLEAN);
	}

	public function passes($attribute, $value)
	{
		if ( !$this->is_required ) return true;

		$initDate = Carbon::parse($this->init_date);
		$endDate = Carbon::parse($this->end_date);

		$maxDays = $initDate->diffInDays($endDate);
		return $value <= $maxDays;
	}

	public function message()
	{
		return 'Los días de notificación deben ser menores o iguales a los días de diferencia entre las fechas.';
	}
}
