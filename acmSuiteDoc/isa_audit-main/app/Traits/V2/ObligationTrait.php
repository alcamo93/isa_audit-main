<?php

namespace App\Traits\V2;

use App\Models\V2\Audit\Obligation;
use Carbon\Carbon;

trait ObligationTrait
{
  /**
   * Change status.
   *
   * @param  App\Models\V2\Audit\Obligation $obligation
   * @param  App\Models\V2\Audit\Library $library
   * @return Boolean
   */
  public function setStatusObligation($obligation, $library)
  {
    try {
      if ( is_null($library) ) {
        $data['id_status'] = Obligation::NO_EVIDENCE_OBLIGATION;
        $data['success'] = true;
        $data['notification'] = false;
        $obligation->update(['id_status' => $data['id_status']]);
        return $data;
      }
      
      if ( is_null($library->end_date) ) {
        $data['id_status'] = Obligation::NO_DATES_OBLIGATION;
        $data['success'] = true;
        $data['notification'] = false;
        $obligation->update(['id_status' => $data['id_status']]);
        return $data;
      }
      $timezone = Config('enviroment.time_zone_carbon');
      $today = Carbon::now($timezone)->startOfDay();
      $endDateStr = strlen($library->end_date) == 10 ? "{$library->end_date} 00:00:00" : $library->end_date;
      $endDate = Carbon::createFromFormat('Y-m-d H:i:s', $endDateStr, $timezone);
      $subEndDate = Carbon::createFromFormat('Y-m-d H:i:s', $endDateStr, $timezone)
        ->subDay($library->days);
      $notification = $today->eq($subEndDate);
      
      $isExpired = $today->greaterThan($endDate);
      if ( $isExpired ) {
        $data['id_status'] = Obligation::EXPIRED_OBLIGATION;
        $data['success'] = true;
        $data['notification'] = $notification;
        $obligation->update(['id_status' => $data['id_status']]);
        return $data;
      }

      $forExpired = $today->greaterThanOrEqualTo($subEndDate) && $today->lessThanOrEqualTo($endDate);
      if ( $forExpired ) {
        $data['id_status'] = Obligation::FOR_EXPIRED_OBLIGATION;
        $data['success'] = true;
        $data['notification'] = $notification;
        $obligation->update(['id_status' => $data['id_status']]);
        return $data;
      }
      
      $isCurrent = $today->lessThan($subEndDate);
      if ( $isCurrent ) {
        $data['id_status'] = Obligation::APPROVED_OBLIGATION;
        $data['success'] = true;
        $data['notification'] = $notification;
        $obligation->update(['id_status' => $data['id_status']]);
        return $data;
      }
      
      return $data['success'] = false;
    } catch (\Throwable $th) {
      return $data['success'] = false;
    }
  }
}