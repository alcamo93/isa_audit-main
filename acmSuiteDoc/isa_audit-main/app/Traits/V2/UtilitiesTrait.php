<?php

namespace App\Traits\V2;

use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use Config;

trait UtilitiesTrait
{
	/**
	 * Allow web to control relationships in models
	 * @param Builder $query
	 * Define allow_included properties in model
	 * Example: domain.com?included=relation_a,relation_b
	 */
	public function scopeIncluded(Builder $query)
	{
		$included = request('included');
		if (empty($this->allow_included) || empty($included)) {
			return;
		}

		$allow_included = collect($this->allow_included);
		// Validate relations
		$relations = explode(',', $included);
		foreach ($relations as $key => $relation) {
			if (!$allow_included->contains($relation)) {
				unset($relations[$key]);
			}
		}
		$query->with($relations);
	}

	/**
	 * Allow web to control relationships in models
	 * @param Builder $query
	 * Define allow_filter properties in model
	 * Example: domain.com?filters[field_a]=value&filters[field_b]=value
	 */
	public function scopeFilter(Builder $query)
	{
		$filters = request('filters');
		if (empty($this->allow_filter) || empty($filters)) {
			return;
		}

		$allow_filter = $this->allow_filter;
		// validate filed and add filter in query
		foreach ($filters as $filter => $value) {
			if (isset($allow_filter[$filter])) {
				$attrs = $allow_filter[$filter];
				$custom_where = $this->buildWhere($attrs, $filter, $value, $query);
				$query->where($custom_where);
			}
		}
	}

	/**
	 * Allow web to control relationships in models
	 * @param Builder $query
	 * Define allow_filter properties in model
	 * Example: domain.com?scope=scope_a,scope_b
	 */
	public function scopeWithScopes($query)
	{
		$scopeRequest = request('scope');

		if (empty($this->allow_scope) || empty($scopeRequest)) {
			return $query;
		}

		$allow_scope = collect($this->allow_scope);
		$scopes = explode(',', $scopeRequest);

		$allRelations = [];

		foreach ($scopes as $scope) {
			$scope = trim($scope);
			$scopeMethod = 'scopeWith' . ucfirst($scope);

			if (method_exists($this, $scopeMethod) && $allow_scope->contains($scope)) {
				// call scope method to get relations
				$tempQuery = $this->{'with' . ucfirst($scope)}(clone $query);
				// get all eager loads relations of tempQuery
				$relations = $tempQuery->getEagerLoads();
				// merge scopemethods
				$allRelations = array_merge($allRelations, $relations);
			}
		}

		if (!empty($allRelations)) {
			$query = $query->with($allRelations);
		}

		return $query;
	}


	/**
	 * @param array $attrs
	 * @param string $filter
	 * @param string $value
	 * @param Builder $queryParent
	 * @return Builder $query
	 * Build dynamic where, validate type and relation
	 */
	public static function buildWhere($attrs, $filter, $value, $queryParent)
	{
		if (!is_null($attrs['relation'])) { // apply where relations are used
			$where = function ($whereHas) use ($attrs, $filter, $value) {
				$whereHas->whereHas($attrs['relation'], function ($query) use ($attrs, $filter, $value) {
					if ($attrs['type'] == 'string') {
						$query->where($attrs['field'], 'LIKE', '%' . $value . '%');
					}
					if ($attrs['type'] == 'number') {
						$query->where($attrs['field'], $value);
					}
					if ($attrs['type'] == 'date') {
						$query->whereDate($attrs['field'], $value);
					}
					// example: filters[field_a]=value_1,value_2...
					if ($attrs['type'] == 'array_in') {
						$array = explode(',', $value);
						$query->whereIn($attrs['field'], $array);
					}
					// example: filters[field_a]=boolean_value [0 -> whereNull, 1 -> whereNotNull]
					if ($attrs['type'] == 'value_null') {
						if (boolval($value)) {
							$query->whereNull($attrs['field']);
						} else {
							$query->whereNotNull($attrs['field']);
						}
					}
					if ($attrs['type'] == 'boolean') {
						$status_bool = collect([0, 1]);
						if ($status_bool->contains($value)) {
							$query->where($attrs['field'], $value);
						}
					}
					if ($attrs['type'] == 'date_range') {
						$dates = explode(',', $value);
						$query->whereDate($attrs['field'], '>=', $dates[0])
								->whereDate($attrs['field'], '<=', $dates[1]);
					}
					if ($attrs['type'] == 'date_range_custom') {
						$dates = explode(',', $value);
						$fileds = explode(',', $attrs['field']);
						$query->whereDate($fileds[0], '>=', $dates[0])
								->whereDate($fileds[1], '<=', $dates[1]);
					}
					if ($attrs['type'] == 'class_name') {
						$query->where($attrs['field'], 'LIKE', '%' . $value . '%');
					}
				});
			};
		} else { // apply where relations are not used
			$where = null;
			if ($attrs['type'] == 'string') {
				$where = [
					[$attrs['field'], 'LIKE', '%' . $value . '%'],
				];
			}
			if ($attrs['type'] == 'number') {
				$where = [
					[$attrs['field'], $value],
				];
			}
			if ($attrs['type'] == 'init_date') {
				$queryParent->whereDate($attrs['field'], '>', $value);
			}
			if ($attrs['type'] == 'end_date') {
				$queryParent->whereDate($attrs['field'], '<', $value);
			}
			// example: filters[field_a]=value_1,value_2...
			if ($attrs['type'] == 'array_in') {
				$array = explode(',', $value);
				$queryParent->whereIn($attrs['field'], $array);
				return;
			}
			// example: filters[field_a]=value_1,value_2...
			if ($attrs['type'] == 'json') {
				$array = explode(',', $value);
				$queryParent->whereJsonContains($attrs['field'], $array);
				return;
			}
			// example: filters[field_a]=boolean_value [0 -> whereNull, 1 -> whereNotNull]
			if ($attrs['type'] == 'value_null') {
				if (boolval($value)) {
					$queryParent->whereNotNull($attrs['field']);
					return;
				} else {
					$queryParent->whereNull($attrs['field']);
					return;
				}
			}
			if ($attrs['type'] == 'boolean') {
				$status_bool = collect([0, 1]);
				if ($status_bool->contains($value)) {
					$queryParent->where($attrs['field'], $value);
					return;
				}
			}
			if ($attrs['type'] == 'date_range') {
				$dates = explode(',', $value);
				$queryParent->whereDate($attrs['field'], '>=', $dates[0])
					->whereDate($attrs['field'], '<=', $dates[1]);
			}
			if ($attrs['type'] == 'date_range_custom') {
				$dates = explode(',', $value);
				$fileds = explode(',', $attrs['field']);
				$queryParent->whereDate($fileds[0], '<=', $dates[0])
					->whereDate($fileds[1], '>=', $dates[1]);
			}
			if ($attrs['type'] == 'class_name') {
				$queryParent->where($attrs['field'], 'LIKE', '%' . $value . '%');
			}
		}
		return $where;
	}

	/**
	 * Paginate per number of records specified by the user
	 * @param Builder $query
	 * Example: domain.com?perPage=3
	 */
	public function scopeGetOrPaginate(Builder $query)
	{
		$perPage = intval(request('perPage'));
		if ($perPage) {
			$limitRecords = 100;
			$limit = ($perPage <= $limitRecords) ? $perPage : $limitRecords;
			return $query->paginate($limit);
		}
		return $query->get();
	}

	/**
	 * Change format for date
	 * @param string|null $date
	 */
	public function getFormatDate($date)
	{
		if ( is_null($date) ) return null;
		$timezone = Config('enviroment.time_zone_carbon');
		$dateStr = strlen($date) <= 10 ? "{$date} 00:00:00" : $date;
		$dateFormat = Carbon::createFromFormat('Y-m-d H:i:s', $dateStr, $timezone)->format('d/m/Y');
		return $dateFormat;
	}

	/**
	 * Change format for text date
	 * @param string|null $date
	 */
	public function getFormatDateText($date)
	{
		if ( is_null($date) ) return null;
		$timezone = Config('enviroment.time_zone_carbon');
		$dateStr = strlen($date) <= 10 ? "{$date} 00:00:00" : $date;
		$carbonDate = Carbon::createFromFormat('Y-m-d H:i:s', $dateStr, $timezone)->locale('es');
		$dateFormat = $carbonDate->translatedFormat('d \d\e F \d\e Y');
		return $dateFormat;
	}

	/**
	 * Change format for text date short
	 * @param string|null $date
	 */
	public function getFormatDateTextShort($date)
	{
		if ( is_null($date) ) return null;
		$timezone = Config('enviroment.time_zone_carbon');
		$dateStr = strlen($date) <= 10 ? "{$date} 00:00:00" : $date;
		$carbonDate = Carbon::createFromFormat('Y-m-d H:i:s', $dateStr, $timezone)->locale('es');
		$dateFormat = $carbonDate->translatedFormat('d F Y');
		return $dateFormat;
	}

	/**
	 * Change format for date
	 * @param string|null $date
	 */
	public function getFormatDatetime($date)
	{
		if ( is_null($date) ) return null;
		$timezone = Config('enviroment.time_zone_carbon');
		$dateStr = strlen($date) <= 10 ? "{$date} 00:00:00" : $date;
		$dateFormat = Carbon::createFromFormat('Y-m-d H:i:s', $dateStr, $timezone)->format('d/m/Y H:i:s');
		return $dateFormat;
	}

	/**
	 * Change format for date
	 * @param string|null $date
	 */
	public function getFormatDatetimeSystem($date)
	{
		if ( is_null($date) ) return null;
		$timezone = Config('enviroment.time_zone_carbon');
		$dateFormat = strlen($date) <= 10 ? "{$date} 00:00:00" : $date;
		return $dateFormat;
	}

	/**
	 * Change format for date
	 * @param string|null $init_date
	 * @param string|null $end_date
	 */
	public function dateInRange($init_date, $end_date)
	{
		if ($init_date == null || $end_date == null) return false;

		$timezone = Config('enviroment.time_zone_carbon');
		$todayDate = Carbon::now($timezone);
		// init
		$initDateStr = strlen($init_date) <= 10 ? "{$init_date} 00:00:00" : $init_date;
		$initDate = Carbon::createFromFormat('Y-m-d H:i:s', $initDateStr, $timezone);
		// end
		$endDateStr = strlen($end_date) <= 10 ? "{$end_date} 00:00:00" : $end_date;
		$endDate = Carbon::createFromFormat('Y-m-d H:i:s', $endDateStr, $timezone);

		return $todayDate->greaterThanOrEqualTo($initDate) && $todayDate->lessThanOrEqualTo($endDate);
	}

	/**
	 * evaluate date range
	 * @param string|null $init_date
	 * @param string|null $end_date
	 */
	public function newDateInRange($init_date, $end_date)
	{
		if ($init_date == null || $end_date == null) return false;

		$timezone = Config('enviroment.time_zone_carbon');
		$todayDate = Carbon::now($timezone);
		// init
		$initDateStr = strlen($init_date) <= 10 ? "{$init_date} 00:00:00" : $init_date;
		$initDate = Carbon::createFromFormat('Y-m-d H:i:s', $initDateStr, $timezone);
		// end
		$endDateStr = strlen($end_date) <= 10 ? "{$end_date} 00:00:00" : $end_date;
		$endDate = Carbon::createFromFormat('Y-m-d H:i:s', $endDateStr, $timezone);

		$todayDate = $todayDate->startOfDay();
		$initDate = $initDate->startOfDay();
		$endDate = $endDate->startOfDay();

		return $todayDate->greaterThanOrEqualTo($initDate) && $todayDate->lessThanOrEqualTo($endDate);
	}

	/**
	 * evaluate date range history
	 * @param string|null $init_date
	 * @param string|null $end_date
	 */
	public function newDateInRangeHistory($init_date, $end_date)
	{
		if ($init_date == null || $end_date == null) return false;

		$timezone = Config('enviroment.time_zone_carbon');
		$todayDate = Carbon::now($timezone);
		// init
		$initDateStr = strlen($init_date) <= 10 ? "{$init_date} 00:00:00" : $init_date;
		$initDate = Carbon::createFromFormat('Y-m-d H:i:s', $initDateStr, $timezone);
		// end
		$endDateStr = strlen($end_date) <= 10 ? "{$end_date} 00:00:00" : $end_date;
		$endDate = Carbon::createFromFormat('Y-m-d H:i:s', $endDateStr, $timezone);

		$todayDate = $todayDate->startOfDay();
		$initDate = $initDate->startOfDay();
		$endDate = $endDate->startOfDay();

		return $todayDate->greaterThanOrEqualTo($initDate) && $todayDate->lessThanOrEqualTo($endDate);
	}


	/**
	 * Change format for date
	 * @param string|null $init_date
	 * @param string|null $end_date
	 */
	public function infoDates($init_date, $end_date)
	{
		$data['date_not_yet_reached'] = false;
    $data['within_range_date'] = false;
    $data['expired_date'] = false;

		if ($init_date == null || $end_date == null) return $data;

    $timezone = Config('enviroment.time_zone_carbon');
		$todayDate = Carbon::now($timezone);

    $initDateStr = strlen($init_date) <= 10 ? "{$init_date} 00:00:00" : $init_date;
		$initDate = Carbon::createFromFormat('Y-m-d H:i:s', $initDateStr, $timezone);
		
		$endDateStr = strlen($end_date) <= 10 ? "{$end_date} 00:00:00" : $end_date;
		$endDate = Carbon::createFromFormat('Y-m-d H:i:s', $endDateStr, $timezone);
    
    $greaterThanOrEqualTo = $todayDate->greaterThanOrEqualTo($initDate); 
    $lessThanOrEqualTo = $todayDate->lessThanOrEqualTo($endDate);
    $greaterThan = $todayDate->greaterThan($end_date);
    $lessThan = $todayDate->lessThan($initDate);

    $data['date_not_yet_reached'] = $lessThan;
    $data['within_range_date'] = $greaterThanOrEqualTo && $lessThanOrEqualTo;
    $data['expired_date'] = $greaterThan;

		return $data;
	}

	/**
	 * Range dates in current year
	 * @param string|null $init_date
	 * @param string|null $end_date
	 */
	public function isInCurrentYear($init_date, $end_date)
    {
			if ($init_date == null || $end_date == null) return false;

			$timezone = Config('enviroment.time_zone_carbon');
			$currentYear = Carbon::now($timezone)->format('Y');

			$initDateStr = strlen($init_date) <= 10 ? "{$init_date} 00:00:00" : $init_date;
			$endDateStr = strlen($end_date) <= 10 ? "{$end_date} 00:00:00" : $end_date;

			$initDate = Carbon::parse($initDateStr);
			$endDate = Carbon::parse($endDateStr);

			return $initDate->year <= $currentYear && $endDate->year >= $currentYear;
    }
}
