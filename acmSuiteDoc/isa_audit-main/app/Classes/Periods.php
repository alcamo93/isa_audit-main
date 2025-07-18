<?php

namespace App\Classes;

use Carbon\Carbon;
use App\Models\Catalogues\PeriodModel;

class Periods
{
    /**
     * Create dates
     */
    public function createDates($initDate, $idPeriod) {
        $periods = PeriodModel::GetPeriod($idPeriod);
        $data['initDate'] = $initDate;
        // Get close Date
        $initDateA = Carbon::createFromFormat('Y-m-d H:i:s', $initDate.' 00:00:00', 'America/Mexico_City');
        if ($periods[0]['lastMonth'] == 0 && $periods[0]['lastYear'] == 0 && $periods[0]['lastDay'] == 0) {
            $closeDate = NULL;
        }else{
            if($periods[0]['lastYear'] != 0){
                $closeDate = $initDateA->addYears($periods[0]['lastYear']);
            }
            if ($periods[0]['lastMonth'] != 0) {
                $closeDate = $initDateA->addMonths($periods[0]['lastMonth']);
            }
            if ($periods[0]['lastDay'] != 0) {
                $closeDate = $initDateA->addDays($periods[0]['lastDay']);
            }
        }
        $data['closeDate'] = $closeDate->toDateString();
        // Get Real close date
        $initDateB = Carbon::createFromFormat('Y-m-d H:i:s', $initDate.' 00:00:00', 'America/Mexico_City');
        if ($periods[0]['lastRealMonth'] == 0 && $periods[0]['lastRealYear'] == 0 && $periods[0]['lastRealDay'] == 0) {
            $realCloseDate = NULL;
        }else{
            if($periods[0]['lastRealYear'] != 0){
                $realCloseDate = $initDateB->addYears($periods[0]['lastRealYear']);
            }
            if ($periods[0]['lastRealMonth'] != 0) {
                $realCloseDate = $initDateB->addMonths($periods[0]['lastRealMonth']);
            }
            if ($periods[0]['lastRealDay'] != 0) {
                $realCloseDate = $initDateB->addDays($periods[0]['lastRealDay']);
            }
            $realCloseDate = $realCloseDate->toDateString();
        }
        $data['realCloseDate'] = $realCloseDate; 
        // Get Real init date
        $initDateC = Carbon::createFromFormat('Y-m-d H:i:s', $initDate.' 00:00:00', 'America/Mexico_City');
        if ($periods[0]['lastMonth'] == 0 && $periods[0]['lastYear'] == 0 && $periods[0]['lastDay'] == 0) {
            $realInitDate = NULL;
        }else{
            if($periods[0]['lastYear'] != 0){
                $realInitDate = $initDateC->addYears($periods[0]['lastYear']);
            }
            if ($periods[0]['lastMonth'] != 0) {
                $realInitDate = $initDateC->addMonths($periods[0]['lastMonth']);
            }
            if ($periods[0]['lastDay'] != 0) {
                $realInitDate = $initDateC->addDays($periods[0]['lastDay']);
            }
            $realInitDate = $realInitDate->addDays(1)->toDateString();
        }
        $data['realInitDate'] = $realInitDate;
        return $data;
    }
}