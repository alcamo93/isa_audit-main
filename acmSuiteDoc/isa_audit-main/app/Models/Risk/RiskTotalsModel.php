<?php

namespace App\Models\Risk;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException as Exception;
use App\Classes\StatusConstants;

class RiskTotalsModel extends Model
{
    use \Awobaz\Compoships\Compoships;

    protected $table = 't_risk_totals'; 
    protected $primaryKey = 'id_risk_total';
    /**
     * Set total
     */
    public function scopeSetTotal($query, $total, $dataRequest) {
        try {
            $model = RiskTotalsModel::where([
                ['id_audit', $dataRequest['idAudit'] ],
                ['id_audit_processes', $dataRequest['idAuditProcess'] ],
                ['id_requirement', $dataRequest['idRequirement'] ],
                ['id_subrequirement', $dataRequest['idSubrequirement'] ],
                ['id_risk_category', $dataRequest['idRiskCategory'] ]
            ])->firstOrFail();
        } catch (ModelNotFoundException $th) {
            $model = new RiskTotalsModel;
            $model->id_audit = $dataRequest['idAudit'];
            $model->id_audit_processes = $dataRequest['idAuditProcess'];
            $model->id_requirement = $dataRequest['idRequirement'];
            $model->id_subrequirement = $dataRequest['idSubrequirement'];
            $model->id_risk_category = $dataRequest['idRiskCategory'];
        }
        // Save data
        try {
            $model->total = $total;
            $model->save();
            $data['riskTotal'] = $total;
            $data['status'] = StatusConstants::SUCCESS;
            return $data;
        } catch (Exception $e) {
            $data['status'] = StatusConstants::ERROR;
            return $data;
        }
    }
    /**
     * Get total by requirement or subrequirement
     */
    public function scopeGetData($query, $idAuditProcess, $idRequirement, $idSubrequirement) {
        $query->join('c_risk_categories', 'c_risk_categories.id_risk_category','t_risk_totals.id_risk_category')
            ->where([
                ['id_audit_processes', $idAuditProcess],
                ['id_requirement', $idRequirement ],
                ['id_subrequirement', $idSubrequirement ]
            ]);
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Delete total by requirement or subrequirement
     */
    public function scopeDeleteRiskTotal($query, $idAuditProcess, $idRequirement, $idSubrequirement) {
        try {
            $model = RiskTotalsModel::where([
                ['id_audit_processes', $idAuditProcess],
                ['id_requirement', $idRequirement],
                ['id_subrequirement', $idSubrequirement]
            ]);
        } catch (ModelNotFoundException $nf) {
            return StatusConstants::WARNING;
        }
        try {
            $model->delete();
            return StatusConstants::SUCCESS;
        } catch (Exception $e) {
            return StatusConstants::ERROR;
        }
    }
}