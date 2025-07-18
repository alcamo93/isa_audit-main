<?php

namespace App\Models\Audit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException as Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Classes\StatusConstants;
use Illuminate\Support\Facades\DB;

class ContractMattersModel extends Model
{
    protected $table = 'r_contract_matters';
    protected $primaryKey = 'id_contract_matter';

    public function aspects() {
        return $this->hasMany('App\Models\Audit\ContractAspectsModel', 'id_contract_matter', 'id_contract_matter');
    }

    public function scopeGetContractMatters($query, $idAplicabilityRegister){
        $query->join('c_matters', 'c_matters.id_matter', 'r_contract_matters.id_matter')
            ->select('r_contract_matters.*', 'c_matters.matter')
            ->where('r_contract_matters.id_aplicability_register', $idAplicabilityRegister);
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Set current status quiz by apsect
     */
    public function scopeSetStatusMatter($query, $idContractMatter, $idStatus){
        try {
            $model = ContractMattersModel::findOrFail($idContractMatter);
            try {
                $model->id_status = $idStatus;
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
     * Set current status matter
     */
    public function scopeSetFinishedMatters($query, $idAplicabilityRegister){
        $model = ContractMattersModel::where([
            ['r_contract_matters.id_aplicability_register', $idAplicabilityRegister],
            ['r_contract_matters.id_status', StatusConstants::CLASSIFIED]
        ]);
        try {
            $model->update(['r_contract_matters.id_status' => StatusConstants::FINISHED]);
            return StatusConstants::SUCCESS;
        } catch (Exception $e) {
            return StatusConstants::ERROR;
        }
    }
    /**
     * Count matter by status
     */
    public function scopeCountMattersByStataus($query, $idAplicabilityRegister, $idStatus){
        $count = ContractMattersModel::where([
            ['id_aplicability_register', $idAplicabilityRegister],
            ['id_status', $idStatus]
        ])->count();
        return $count;
    }
    /**
     * Set matter 
     */
    // public function scopeSetMatter($query, $data){
    //     $model = new ContractMattersModel();
    //     try {
    //         $model->self_audit = 0;
    //         $model->id_aplicability_register = $data['idAplicabilityRegister'];
    //         $model->id_contract = $data['idContract'];
    //         $model->id_matter = $data['idMatter'];
    //         $model->id_audit_processes = $idProcesses;
    //         $model->id_status = StatusConstants::NOT_CLASSIFIED;
    //         $model->save();
    //         return StatusConstants::SUCCESS;
    //     } catch (Exception $e) {
    //         return StatusConstants::ERROR;
    //     }
    // }


    public function scopeSetMatter($query, $idContract, $idMatter, $idProcesses, $idAplicabilityRegister){
        $model = new ContractMattersModel();
        $model->self_audit = 0;
        $model->id_aplicability_register = $idAplicabilityRegister;
        $model->id_contract = $idContract;
        $model->id_matter = $idMatter;
        $model->id_audit_processes = $idProcesses;
        $model->id_status = StatusConstants::NOT_CLASSIFIED;
        try {
            $model->save();            
            $data['status'] = StatusConstants::SUCCESS;
            $data['id_contract_matter'] = $model->id_contract_matter;
        } catch (Exception $e) {
            $data['status'] = StatusConstants::ERROR;
        }
        return $data;
    }
    /**
     * Delete matter 
     */
    public function scopeDeleteMatter($query, $data){
        try {
            $model = ContractMattersModel::where([
                ['id_contract', $data['idContract'] ],
                ['id_matter', $data['idMatter'] ]
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
     * Get contracts to set in audit
     */
    public function scopeGetContractMattersByStataus($query, $idAplicabilityRegister, $idStatus){
        $query
            ->where('r_contract_matters.id_status', $idStatus)
            ->where('r_contract_matters.id_aplicability_register', $idAplicabilityRegister);
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Delete Matters by id contract matter
     */
    public function scopeDeleteContractMatter($query, $idContractMatter){
        try {
            $model = ContractMattersModel::findOrFail($idContractMatter);
        } catch (ModelNotFoundException $th) {
            return StatusConstants::WARNING;
        }
        try {
            $model->delete();
            return StatusConstants::SUCCESS;
        } catch (QueryException $th) {
            return StatusConstants::ERROR;
        }
    }
    /**
     * Delete Matters by audit process
     */
    public function scopeDeleteMattersByProcess($query, $idProcess){
        try {
            $model = ContractMattersModel::where('id_audit_processes', $idProcess);
        } catch (ModelNotFoundException $th) {
            return StatusConstants::WARNING;
        }
        try {
            $model->delete();
            return StatusConstants::SUCCESS;
        } catch (QueryException $th) {
            return StatusConstants::ERROR;
        }
    }
    public function scopeGetMatterId($query, $idProcesses, $idAplicability){
        $query->select('r_contract_matters.id_contract_matter')
            ->where('r_contract_matters.id_audit_processes', $idProcesses)
            ->where('r_contract_matters.id_aplicability_register', $idAplicability);
        $data = $query->get()->toArray();
        return $data;
    }
    public function scopeGetMattersAudit($query, $idAplicabilityRegister){
        $query
            ->where('r_contract_matters.id_aplicability_register', $idAplicabilityRegister);
        $data = $query->get()->toArray();
        return $data;
    }
    public function scopeGetContractMattersByProcesses($query, $idProcesses){
        $query->join('c_matters', 'c_matters.id_matter', 'r_contract_matters.id_matter')
            ->select('r_contract_matters.*', 'c_matters.matter')
            ->where('r_contract_matters.id_audit_processes' ,$idProcesses);
        $data = $query->get()->toArray();
        return $data;
    }

    public function scopeGetQuestionsAplicabilityByAplicabilityRegister($query, $idAplicabilityRegister){
        $query->select([
            'r_contract_matters.id_aplicability_register',
            'r_contract_aspects.id_audit_processes',
            'r_contract_aspects.id_matter',
            'r_contract_aspects.id_aspect',
            'c_matters.matter',
            'c_aspects.aspect',
            't_questions.order',
            't_questions.id_question',
            't_questions.question',
        ])
        ->join('r_contract_aspects', 'r_contract_aspects.id_contract_matter', 'r_contract_matters.id_contract_matter')
        ->join('c_matters', 'r_contract_aspects.id_matter', 'c_matters.id_matter')
        ->join('c_aspects', 'r_contract_aspects.id_aspect', 'c_aspects.id_aspect')
        ->leftJoin('t_questions', 't_questions.id_aspect', 'r_contract_aspects.id_aspect')
        ->where('r_contract_matters.id_aplicability_register', $idAplicabilityRegister)
        ->where('t_questions.id_status', StatusConstants::ACTIVE)
        ->orderBy('c_matters.id_matter', 'ASC')
        ->orderBy('c_aspects.order', 'ASC')
        ->orderBy('t_questions.order', 'ASC');

        return $query->get();
    }

    public function scopeCountResponseQuestionsAplicabilityByAplicabilityRegister($query, $idAplicabilityRegister){
        $query->select([
            DB::raw("case 
                when t_aplicability.id_answer_value = ".StatusConstants::AFFIRMATIVE." THEN 'Afirmativo'
                when t_aplicability.id_answer_value = ".StatusConstants::NEGATIVE." THEN 'Negativo'
                else 'Pendiente' end as response"),
            DB::raw("case 
                when t_aplicability.id_answer_value = ".StatusConstants::AFFIRMATIVE." THEN count(*)
                when t_aplicability.id_answer_value = ".StatusConstants::NEGATIVE." THEN count(*)
                else count(*) end as total")
        ])
        ->join('r_contract_aspects', 'r_contract_aspects.id_contract_matter', 'r_contract_matters.id_contract_matter')
        ->join('c_matters', 'r_contract_aspects.id_matter', 'c_matters.id_matter')
        ->join('c_aspects', 'r_contract_aspects.id_aspect', 'c_aspects.id_aspect')
        ->leftJoin('t_questions', 't_questions.id_aspect', 'r_contract_aspects.id_aspect')
        ->leftJoin('t_aplicability', function ($join){
            $join->on('t_questions.id_question', '=', 't_aplicability.id_question')
                ->on('t_aplicability.id_audit_processes', '=', 'r_contract_matters.id_audit_processes');
        })
        ->where('r_contract_matters.id_aplicability_register', $idAplicabilityRegister)
        ->groupBy('t_aplicability.id_answer_value');

        return $query->get();
    }
}
