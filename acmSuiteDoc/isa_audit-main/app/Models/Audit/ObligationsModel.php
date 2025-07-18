<?php

namespace App\Models\Audit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException as Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Classes\StatusConstants;
use Illuminate\Support\Facades\Session;

class ObligationsModel extends Model
{
    protected $table = 't_obligations';
    protected $primaryKey = 'id_obligation';

    protected $fillable = ['id_status', 'init_date', 'renewal_date', 'last_renewal_date'];

    const NO_STARTED = 20;
    const REVIEW = 21;
    const EXPIRED = 22;
    const APPROVED = 23;
    const REJECTED = 24;

    public function process() {
        return $this->belongsTo('App\Models\Admin\ProcessesModel', 'id_audit_processes', 'id_audit_processes');
    }

    public function type() {
        return $this->hasOne('App\Models\Catalogues\ObligationTypesModel', 'id_obligation_type', 'id_obligation_type');
    }
    
    public function status() {
        return $this->hasOne('App\Models\Catalogues\StatusModel', 'id_status', 'id_status');
    }

    public function period() {
        return $this->hasOne('App\Models\Catalogues\PeriodModel', 'id_period', 'id_period');
    }

    public function users() {
        return $this->hasMany('App\Models\Audit\ObligationUserModel', 'id_obligation', 'id_obligation');
    }
    
    public function file() {
        return $this->hasOne('App\Models\Files\FilesModel', 'id_obligation', 'id_obligation');
    }

    /**
     * update obligation status
     */
    function scopeUpdateObligationStatus($query, $status, $idObligation)
    {
        $response = StatusConstants::ERROR;
        $q = $query
            ->where('id_obligation', $idObligation)
            ->update([
                'id_status' => $status
            ]);        
        if($q) $response = StatusConstants::SUCCESS;
        return $response;
    }

    /**
    * Set Aspect from add catalogs module
    */
    public function scopeSetObligation($query, $info, $idActionRegister) {
        $model = new ObligationsModel();
        $model->title = $info['title'];
        $model->obligation = $info['obligation'];
        $model->id_status = ObligationsModel::NO_STARTED;
        $model->id_period = $info['idPeriod'];
        $model->id_condition = $info['idCondition'];
        $model->id_obligation_type = $info['idObligationType'];
        $model->id_action_register = $idActionRegister;
        $model->id_audit_processes = $info['idAuditProcess'];
        try{
            $model->save();
            return StatusConstants::SUCCESS;
        }
        catch (Exception $e) {
            return StatusConstants::ERROR;
        }
    }
    /**
    * Set Aspect from add catalogs module
    */
    public function scopeUpdateObligation($query, $info, $idActionRegister){
        $model = ObligationsModel::find($info['idObligation']);
        $model->title = $info['title'];
        $model->obligation = $info['obligation'];
        $model->id_period = $info['idPeriod'];
        $model->id_condition = $info['idCondition'];
        $model->id_obligation_type = $info['idObligationType'];
        $model->id_action_register = $idActionRegister;
        $model->id_audit_processes = $info['idAuditProcess'];
        try{
            $model->save();
            return StatusConstants::SUCCESS;
        }
        catch (Exception $e) {
            return StatusConstants::ERROR;
        }
    }
    /** 
     * delete aspect from add catalogs module
    */
    public function scopeDeleteObligation($query, $idObligation){
        $model = ObligationsModel::find($idObligation);
        try{
            $model->delete();
            return StatusConstants::SUCCESS;
        }
        catch (Exception $e) {
            return StatusConstants::ERROR;
        }
    }
    /**
     * Get data obligation by id
     */
    public function scopeGetObligation($query, $idObligation){
        $query->join('c_periods', 'c_periods.id_period', 't_obligations.id_period')
            ->join('c_obligation_types', 'c_obligation_types.id_obligation_type', 't_obligations.id_obligation_type')
            ->select('t_obligations.id_obligation', 't_obligations.title', 't_obligations.obligation', 
                't_obligations.init_date', 't_obligations.renewal_date', 't_obligations.last_renewal_date', 
                't_obligations.id_status', 't_obligations.id_period', 't_obligations.id_condition', 't_obligations.id_action_register', 
                't_obligations.id_user', 't_obligations.id_user_asigned', 'c_periods.period', 't_obligations.permit', 't_obligations.id_obligation_type',
                \DB::raw('DATE_FORMAT(t_obligations.init_date, "%Y-%m-%d") AS initDate'),
                \DB::raw('DATE_FORMAT(t_obligations.renewal_date, "%Y-%m-%d") AS renewalDate'),
                \DB::raw('DATE_FORMAT(t_obligations.last_renewal_date, "%Y-%m-%d") AS lastRenewalDate'))
            ->where('id_obligation', $idObligation);
        $data = $query->get()->toArray();
        return $data;
    }
    // /**
    //  * update dates in obligation
    //  */
    // public function scopeUpdateObligationDates($query, $idObligation, $renewalDate, $lastRenewalDate, $initDate){
    //     $model = ObligationsModel::find($idObligation);
    //     if ($initDate != null) $model->init_date = $initDate;
    //     if ($initDate != null) $model->renewal_date = $renewalDate;
    //     if ($initDate != null) $model->last_renewal_date = $lastRenewalDate;
    //     try {
    //         $model->save();
    //         return StatusConstants::SUCCESS;
    //     }
    //     catch (Exception $e) {
    //         return StatusConstants::ERROR;
    //     }
    // }
}