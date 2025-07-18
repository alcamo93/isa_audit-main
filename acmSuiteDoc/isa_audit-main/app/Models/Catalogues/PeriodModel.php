<?php

namespace App\Models\Catalogues;

use Illuminate\Database\Eloquent\Model;
use App\Classes\StatusConstants;

class PeriodModel extends Model
{
       protected $table = 'c_periods';
       protected $primaryKey = 'id_period';
       /**
        * Return periods
        */
       public function scopeGetPeriods($query){
              $query->select('id_period', 'period');
              $data = $query->get()->toArray();
              return $data;
       }
       /**
        * Return periods for datatable
        */
       public function scopeGetPeriodsDT($query, $page, $rows, $search, $draw, $order, $filterName){
              $where = [];
              if ($filterName != null) array_push($where, ['c_periods.period','LIKE','%'.$filterName.'%']);
              if ($search['value'] != null) array_push($where, ['c_periods.period','LIKE','%'.$search['value'].'%']);
              if($where) $query->where($where);
              //Order by
              $query->orderBy('period', 'ASC');

              $queryCount = $query->get();
              $result = $query->limit($rows)->offset($page)->get()->toArray();
              $total = $queryCount->count();
              $data['data'] = ( sizeof($result) > 0) ? $result : array();
              $data['recordsTotal'] = $total;
              $data['draw'] = (int) $draw;
              $data['recordsFiltered'] = $total;
              return $data;
       }
       /**
        * set period
        */
       public function scopeSetPeriod($query, $period, $lastDay, $lastMonth, $lastYear, $lastRealDay, $lastRealMonth, $lastRealYear )
       {
              $response = StatusConstants::ERROR;
              $query
              ->select('id_period')
              ->where('period', $period);
              $data = $query->first();
              if($data) $response = StatusConstants::WARNING;
              else {
                     $insert = [];
                     if( $period ) $insert['period'] = $period;
                     if( $lastDay ) $insert['lastDay'] = $lastDay;
                     if( $lastMonth ) $insert['lastMonth'] = $lastMonth;
                     if( $lastYear ) $insert['lastYear'] = $lastYear;
                     if( $lastRealDay ) $insert['lastRealDay'] = $lastRealDay;
                     if( $lastRealMonth ) $insert['lastRealMonth'] = $lastRealMonth;
                     if( $lastRealYear ) $insert['lastRealYear'] = $lastRealYear;

                     if(
                            $query
                            ->insert($insert)
                     ) $response = StatusConstants::SUCCESS;
              }
              return $response;
       }
       /**
       * Update periods from add catalogs module
       */
       public function scopeUpdatePeriod($query, $idPeriod , $period, $lastDay, $lastMonth, $lastYear, $lastRealDay, $lastRealMonth, $lastRealYear )
       {
              $response = StatusConstants::ERROR;
              $query
              ->select('id_period')
              ->where([
                     ['period', $period],
                     ['id_period','<>',$idPeriod]
              ]);
              $data = $query->first(); 
              if($data) $response = StatusConstants::WARNING;
              else {
                     try {

                            $update = [];
                            if( $period ) $update['period'] = $period;
                            if( $lastDay ) $update['lastDay'] = $lastDay;
                            if( $lastMonth ) $update['lastMonth'] = $lastMonth;
                            if( $lastYear ) $update['lastYear'] = $lastYear;
                            if( $lastRealDay ) $update['lastRealDay'] = $lastRealDay;
                            if( $lastRealMonth ) $update['lastRealMonth'] = $lastRealMonth;
                            if( $lastRealYear ) $update['lastRealYear'] = $lastRealYear;

                            \DB::table('c_periods')
                            ->where('id_period', $idPeriod)
                            ->update($update);
                            $response = StatusConstants::SUCCESS;
                     } 
                     catch (\Exception $e) {
                            $response =  StatusConstants::ERROR;
                     }
              }
              return $response;
       }
       /**
       * delete periods
       */
       public function scopeDeletePeriod($query, $idPeriod)
       {
              $response = StatusConstants::ERROR;
              try {
                     $query
                     ->where('id_period', $idPeriod)
                     ->delete();
                     $response = StatusConstants::SUCCESS;
              }
              catch (\Exception $e) {
                     if($e->getCode() == '23000') $response =  StatusConstants::WARNING;
                     else $response =  StatusConstants::ERROR;
              }
              return $response;
       }
       /**
        * Return period by id
        */
        public function scopeGetPeriod($query, $idPeriod){
              $query->select('id_period', 'period', 
                     'lastDay', 'lastMonth', 'lastYear',
                     'lastRealDay', 'lastRealMonth', 'lastRealYear')
                     ->where('id_period', $idPeriod);
              $data = $query->get()->toArray();
              return $data;
       }
       /**
        * Get Allowed Periods
        */
       public function scopeGetAllowedPeriods($query, $lastDay, $lastMonth, $lastYear, $periodUpdate = null){
              $query->select('id_period', 'period', 
                     'lastDay', 'lastMonth', 'lastYear',
                     'lastRealDay', 'lastRealMonth', 'lastRealYear');
              if ( ($lastYear == 0 && $lastYear == null) && ($lastMonth == 0 && $lastMonth == null) ) {
                     $query->whereRaw('(lastYear = 0 OR lastYear IS NULL) AND (lastMonth = 0 OR lastMonth IS NULL) AND (lastDay <='.$lastDay.' OR lastDay IS NULL)');
              }
              elseif ($lastYear == 0 && $lastYear == null) {
                     $query->whereRaw('(lastYear = 0 OR lastYear IS NULL) AND (lastMonth <='.$lastMonth.' OR lastMonth IS NULL)');
              }
              elseif ($lastYear != 0 && $lastYear != null) {
                     $query->where('lastYear', '<=' ,$lastYear)->orWhereNull('lastYear');
              }
              if ($periodUpdate != null) {
                     $query->where('id_period', $periodUpdate);
              }
              $data = $query->get()->toArray();
              return $data;
       }
}