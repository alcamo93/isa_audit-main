<?php

namespace App\Models\Audit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException as Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Classes\StatusConstants;

class EvaluateRequirementModel extends Model
{
    use \Awobaz\Compoships\Compoships;

    protected $table = 't_evaluate_requirement';
    protected $primaryKey = 'id_evaluate_requirement';

    public function answerAudit() {
        // Use Compoships trait packege external to eloquent. Support eager loading
        return $this->hasOne('App\Models\Audit\AuditModel', 
            ['id_requirement', 'id_subrequirement', 'id_audit_aspect'],
            ['id_requirement', 'id_subrequirement', 'id_audit_aspect']
        );
    }

    public function requirement() {
        return $this->hasOne('App\Models\Catalogues\RequirementsModel', 'id_requirement', 'id_requirement');
    }

    public function subrequirement() {
        return $this->hasOne('App\Models\Catalogues\SubrequirementsModel', 'id_subrequirement', 'id_subrequirement');
    }

    public function scopeSetEvaluate($query, $idContractAspect, $idAuditAspect, $idRequirement, $idSubrequirement, $idAplicabilityRegister) {
        $model = new EvaluateRequirementModel();
        $model->id_contract_aspect = $idContractAspect;
        $model->id_requirement = $idRequirement;
        $model->id_subrequirement = $idSubrequirement;
        $model->aplicability_register_id = $idAplicabilityRegister;
        try{
            $model->save();
            return StatusConstants::SUCCESS;
        }
        catch (Exception $e) {
            return StatusConstants::ERROR;
        }
    }
}