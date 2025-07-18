<?php

namespace App\Rules;

use App\Traits\V2\UtilitiesTrait;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;

class DatesInDateRange implements Rule
{
	use UtilitiesTrait;

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
		
		$date = Carbon::parse($value);

		if ( $date->lessThan($initDate) || $date->greaterThan($endDate) ) return false;

		return true;
	}

	public function message()
	{
		$initFormat = $this->getFormatDate($this->init_date);
		$endFormat = $this->getFormatDate($this->end_date);
		return "Las fechas deben ser mayores o iguales a {$initFormat} y menores que {$endFormat}.";
	}
}
