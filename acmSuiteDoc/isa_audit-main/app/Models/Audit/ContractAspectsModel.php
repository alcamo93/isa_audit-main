<?php

namespace App\Models\Audit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException as Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Classes\StatusConstants;

class ContractAspectsModel extends Model
{
    protected $table = 'r_contract_aspects';
    protected $primaryKey = 'id_contract_aspect';

    /**
     * Get the audit_process that owns the ContractAspects
     */
    public function audit_process()
    {
        return $this->belongsTo('App\Models\Admin\ProcessesModel', 'id_audit_processes', 'id_audit_processes');
    }

    

    public function scopeGetContractAspectsDT($query, $page, $rows, $search, $draw, $order,
            $idContractMatter, $filterIdStatus, $idAuditProcess){
        $query->join('c_status', 'r_contract_aspects.id_status', 'c_status.id_status')
            ->join('c_aspects', 'r_contract_aspects.id_aspect', 'c_aspects.id_aspect')
            ->join('c_matters', 'c_matters.id_matter', 'r_contract_aspects.id_matter')
            ->leftJoin('c_application_types', 'c_application_types.id_application_type', 'r_contract_aspects.id_application_type')
            ->join('c_states', 'c_states.id_state', 'r_contract_aspects.id_state')
            ->join('r_contract_matters', 'r_contract_matters.id_contract_matter', 'r_contract_aspects.id_contract_matter')
            ->select('r_contract_aspects.id_contract_aspect', 'r_contract_aspects.self_audit', 'r_contract_aspects.form_id',
                'r_contract_aspects.id_contract_matter', 'r_contract_aspects.id_contract', 'r_contract_aspects.id_matter', 
                'r_contract_aspects.id_aspect', 'r_contract_aspects.id_status', 'c_status.status', 'c_aspects.aspect',
                'c_matters.matter', 'r_contract_aspects.id_application_type', 'c_application_types.application_type',
                'r_contract_aspects.id_state', 'c_states.state', 'r_contract_matters.id_aplicability_register', 'r_contract_aspects.id_audit_processes')
            ->where('r_contract_aspects.id_audit_processes', $idAuditProcess)
            ->orderBy('c_matters.order', 'ASC')->orderBy('c_aspects.order', 'ASC');
        if ($idContractMatter != 0) {
            $query->where('r_contract_aspects.id_contract_matter', $idContractMatter);
        }
        // Add filters
        if ($filterIdStatus != 0) {
            $query->where('r_contract_aspects.id_status', $filterIdStatus);
        }
        //Order by
        switch ($order[0]['column']) {
            case 0:
                $columnSwitch = 'c_aspects.aspect';
                break;
            case 1:
                $columnSwitch = 'c_status.status';
                break;
            case 2:
                $columnSwitch = 'c_application_types.application_type';
                break;
            default:
                $columnSwitch = 'r_contract_aspects.id_contract_aspect';
                break;
        }
        $column = $columnSwitch;   
        $dir = (isset($order[0]['dir']) ? $order[0]['dir'] : 'desc');            
        $query->orderBy($column, $dir);
        
        /*Search*/
        $query->where(function($query) use ($search){
            $query->where('c_aspects.aspect','LIKE','%'.$search['value'].'%')
            ->orWhere('c_status.status','LIKE','%'.$search['value'].'%'); 
        });

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
     * Set evaluationg quiz by apsect
     */
    public function scopeSetEvaluatingAspectQuiz($query, $idContractAspect){
        try {
            $model = ContractAspectsModel::find($idContractAspect);
            try {
                $model->id_status = StatusConstants::EVALUATING;
                $model->save();
                return StatusConstants::SUCCESS;
            } catch (Exception $e) {
                return StatusConstants::ERROR;
            }
        } catch (ModelNotFoundException $th) {
            return StatusConstants::WARNING;
        }
    }
    /**
     * Set finalize quiz by apsect
     */
    public function scopeSetClassifyAspectQuiz($query, $idContractAspect, $idApplicationType){
        try {
            $model = ContractAspectsModel::find($idContractAspect);
            try {
                $model->id_application_type = $idApplicationType;
                $model->id_status = StatusConstants::CLASSIFIED;
                $model->save();
                $data['status'] = StatusConstants::SUCCESS;
                $data['id_aspect'] = $model->id_aspect;
                $data['id_contract_aspect'] = $model->id_contract_aspect;
                return $data;
            } catch (Exception $e) {
                return $data['status'] = StatusConstants::ERROR;
            }
        } catch (ModelNotFoundException $th) {
            return $data['status'] = StatusConstants::WARNING;
        }
    }
    /**
     * Set current status aspects
     */
    public function scopeSetFinishedAspects($query, $idAuditProcess){
        $model = ContractAspectsModel::where([
            ['r_contract_aspects.id_audit_processes', $idAuditProcess],
            ['r_contract_aspects.id_status', StatusConstants::CLASSIFIED]
        ]);
        try {
            $model->update(['r_contract_aspects.id_status' => StatusConstants::FINISHED]);
            return StatusConstants::SUCCESS;
        } catch (Exception $e) {
            return StatusConstants::ERROR;
        }
    }
    /**
     * Count aspects by status
     */
    public function scopeCountAspectsByStataus($query, $idContractMatter, $idStatus){
        $count = ContractAspectsModel::where([
            ['id_contract_matter', $idContractMatter],
            ['id_status', $idStatus]
        ])->count();
        return $count;
    }
    /**
     * Update application type
     */
    public function scopeUpdateApplicationType($query, $idContractAspect, $idAapplicationType){
        $model = ContractAspectsModel::find($idContractAspect);
        try {
            $model->id_application_type = $idAapplicationType;
            $model->save();
            return StatusConstants::SUCCESS;
        } catch (Exception $e) {
            return StatusConstants::ERROR;
        }
    }
    /**
     * Get aspects in contract
     */
    public function scopeGetContractAspects($query, $idContract, $idMatter){
        $query->select('r_contract_aspects.id_contract_aspect', 'r_contract_aspects.self_audit', 
                'r_contract_aspects.id_contract_matter', 'r_contract_aspects.id_contract', 'r_contract_aspects.id_matter', 
                'r_contract_aspects.id_aspect', 'r_contract_aspects.id_status', 'r_contract_aspects.id_application_type', 
                'r_contract_aspects.id_state')
            ->where('r_contract_aspects.id_contract', $idContract)
            ->where('r_contract_aspects.id_matter', $idMatter);
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Get aspects by 
     */
    public function scopeGetContractAspectsByProcess($query, $idAuditProcess){
        $query->join('c_aspects', 'c_aspects.id_aspect', 'r_contract_aspects.id_aspect')
            ->join('c_matters', 'c_matters.id_matter', 'r_contract_aspects.id_matter')
            ->where('r_contract_aspects.id_audit_processes', $idAuditProcess);
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Set aspect 
     */
    // public function scopeSetAspect($query, $data, $idState){
    //     $model = new ContractAspectsModel();
    //     try {
    //         $model->self_audit = 0;
    //         $model->id_contract_matter = $data['idContractMatter'];
    //         $model->id_contract = $data['idContract'];
    //         $model->id_matter = $data['idMatter'];
    //         $model->id_aspect = $data['idAspect'];
    //         $model->id_status = StatusConstants::NOT_CLASSIFIED;
    //         $model->id_state = $idState;
    //         $model->save();
    //         return StatusConstants::SUCCESS;
    //     } catch (Exception $e) {
    //         return StatusConstants::ERROR;
    //     }
    // }

    public function scopeSetAspect($query, $idContractMatter, $idContract, $idMatter, $idAspect, $idProcess, $idState) {
        $model = new ContractAspectsModel();
        $model->self_audit = 0;
        $model->id_contract_matter = $idContractMatter;
        $model->id_contract = $idContract;
        $model->id_matter = $idMatter;
        $model->id_aspect = $idAspect;
        $model->id_audit_processes = $idProcess;
        $model->id_status = StatusConstants::NOT_CLASSIFIED;
        $model->id_state = $idState;
        try {
            $model->save();
            $data['status'] = StatusConstants::SUCCESS;
            $data['idContractAspect'] = $model->id_contract_aspect;
        } catch (\Throwable $th) {
            $data['status'] = StatusConstants::ERROR;
        }
        return $data;
    }
    /**
     * Delete aspect 
     */
    public function scopeDeleteAspect($query, $data){
        try {
            $model = ContractAspectsModel::where([
                ['id_contract', $data['idContract'] ],
                ['id_aspect', $data['idAspect'] ]
            ]);
            $idStatus = $model->get()->toArray();
            if ($idStatus[0]['id_status'] == StatusConstants::NOT_CLASSIFIED) {
                try {
                    $model->delete();
                    return StatusConstants::SUCCESS;
                } catch (Exception $e) {
                    return StatusConstants::WARNING;
                }
            }
            else{
                return StatusConstants::WARNING;
            }
        } catch (Exception $e) {
            return StatusConstants::ERROR;
        }
    }
     /**
     * Get aspects in contract
     */
    public function scopeGetContractAspectsByStatus($query, $idAuditProcess, $idMatter, $idStatus){
        $query->where('r_contract_aspects.id_application_type', '!=', StatusConstants::NO_APPLICABLE)
            ->where('r_contract_aspects.id_audit_processes', $idAuditProcess)
            ->where('r_contract_aspects.id_matter', $idMatter)
            ->where('r_contract_aspects.id_status', $idStatus);
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Delete contract asepct
     */
    public function scopeDeleteContractAspect($query, $idContractAspect){
        try {
            $model = ContractAspectsModel::findOrFail($idContractAspect);
        } catch (Exception $e) {
            return StatusConstants::ERROR;
        }
        try {
            $model->delete();
            return StatusConstants::SUCCESS;
        } catch (\Throwable $th) {
            return StatusConstants::WARNING;
        }
    }

    /**
     * @param $idAuditProcesses
     * @param array $status
     * @return mixed
     */
    public function scopeGetAspectsByAuditProcesses($query, $idAuditProcesses){
        $query
        ->join('c_aspects', 'c_aspects.id_aspect', 'r_contract_aspects.id_aspect')
        ->where('r_contract_aspects.id_audit_processes', $idAuditProcesses);

        return $query->get();
    }

    /**
     * Get contract aspect by id_contract_aspect
     */
    public function scopeGetAspectById($query, $idContractAspect){
        $query->where('r_contract_aspects.id_contract_aspect', $idContractAspect);
        $data = $query->get()->toArray();
        return $data;
    }
}
