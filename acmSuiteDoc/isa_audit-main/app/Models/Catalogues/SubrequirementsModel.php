<?php

namespace App\Models\Catalogues;

use Illuminate\Database\Eloquent\Model;
use App\Classes\StatusConstants;
use App\Models\Catalogues\RequirementsModel;

class SubrequirementsModel extends Model
{
    protected $table = 't_subrequirements';
    protected $primaryKey = 'id_subrequirement';

    public function requirement() {
        return $this->hasOne('App\Models\Catalogues\RequirementsModel', 'id_requirement', 'id_requirement');
    }
    /**
     * Set requirements
     */
    public function scopeSetSubrequirement($query, $subrequirement, $requirement) {
        $model = new SubrequirementsModel();
        $model->no_subrequirement = $subrequirement['noSubrequirement'];
        $model->subrequirement = $subrequirement['subrequirement'];
        $model->description = $subrequirement['subrequirementDesc'];
        $model->help_subrequirement = $subrequirement['subrequirementHelp'];
        $model->acceptance = $subrequirement['subrequirementAcceptance'];
        $model->id_evidence = $subrequirement['IdEvidence'];
        if ($subrequirement['IdEvidence'] == StatusConstants::SPECIFIC) {
            $model->document = $subrequirement['document'];
        }
        else {
            $model->document = null;
        }
        $model->id_obtaining_period = $subrequirement['IdObtainingPeriod'];
        $model->id_update_period = $subrequirement['IdUpdatePeriod'];
        $model->id_condition = $subrequirement['IdCondition'];
        $model->id_requirement_type = $subrequirement['IdRequirementType'];
        $model->order = $subrequirement['order'];
        // herency requirement
        $model->id_requirement = $requirement['id_requirement'];
        $model->id_matter = $requirement['id_matter'];
        $model->id_aspect = $requirement['id_aspect'];
        $model->id_application_type = $requirement['id_application_type'];
        $model->id_state = $requirement['id_state'];
        $model->id_city = $requirement['id_city'];
        try {
            $model->save();
            $response['status'] = StatusConstants::SUCCESS;
            $response['idSubrequirement'] = $model->id_subrequirement;
        } catch (\Exception $e) {
            $response['status'] = StatusConstants::ERROR;
        }
        return $response;
    }
    /**
     * Update requirements
     */
    public function scopeUpdateSubrequirement($query, $subrequirement, $requirement) {
        try {
            $model = SubrequirementsModel::findOrFail($subrequirement['idSubrequirement']);
        } catch (ModelNotFoundException $th) {
            return StatusConstants::ERROR;
        }
        $model->no_subrequirement = $subrequirement['noSubrequirement'];
        $model->subrequirement = $subrequirement['subrequirement'];
        $model->id_evidence = $subrequirement['IdEvidence'];
        if ($subrequirement['IdEvidence'] == StatusConstants::SPECIFIC) {
            $model->document = $subrequirement['document'];
        }
        else {
            $model->document = null;
        }
        $model->description = $subrequirement['subrequirementDesc'];
        $model->help_subrequirement = $subrequirement['subrequirementHelp'];
        $model->acceptance = $subrequirement['subrequirementAcceptance'];
        $model->id_obtaining_period = $subrequirement['IdObtainingPeriod'];
        $model->id_update_period = $subrequirement['IdUpdatePeriod'];
        $model->id_condition = $subrequirement['IdCondition'];
        $model->id_requirement_type = $subrequirement['IdRequirementType'];
        $model->order = $subrequirement['order'];
        // herency requirement
        $model->id_requirement = $requirement['id_requirement'];
        $model->id_matter = $requirement['id_matter'];
        $model->id_aspect = $requirement['id_aspect'];
        $model->id_application_type = $requirement['id_application_type'];
        $model->id_state = $requirement['id_state'];
        $model->id_city = $requirement['id_city'];

        try {
            $model->save();
            $response['status'] = StatusConstants::SUCCESS;
            $response['idSubrequirement'] = $model->id_subrequirement;
        } catch (\Exception $e) {
            $response['status'] = StatusConstants::ERROR;
        }
        return $response;
    }
    /**
     * Get requirements for datatables
     */
    public function scopeGetSubrequirementsDT($query, $info) {
        $where = [];
        array_push($where, ['t_subrequirements.id_requirement', $info['idRequirement']]);
        if ($info['filterSubrequirementNumber'] != null) array_push($where, ['t_subrequirements.no_subrequirement','LIKE','%'.$info['filterSubrequirementNumber'].'%']);
        if ($info['filterSubrequirement'] != null) array_push($where, ['t_subrequirements.subrequirement','LIKE','%'.$info['filterSubrequirement'].'%']);
        if ($info['IdMatter'] != null) array_push($where, ['t_subrequirements.id_matter', $info['IdMatter']]);
        if ($info['IdAspect'] != null) array_push($where, ['t_subrequirements.id_aspect', $info['IdAspect']]);
        if ($info['IdEvidence'] != null) array_push($where, ['t_subrequirements.id_evidence', $info['IdEvidence']]);
        if ($info['IdObtainingPeriod'] != null) array_push($where, ['t_subrequirements.id_obtaining_period', $info['IdObtainingPeriod']]);
        if ($info['IdUpdatePeriod'] != null) array_push($where, ['t_subrequirements.id_update_period', $info['IdUpdatePeriod']]);
        if ($info['IdCondition'] != null) array_push($where, ['t_subrequirements.id_condition', $info['IdCondition']]);
        // if ($info[IdRequirementType] != null)
        // {
        //     if(gettype($info[IdRequirementType]) == 'array')  $query->whereIn('t_subrequirements.id_requirement_type', $info[IdRequirementType]);
        //     if(gettype($info[IdRequirementType]) == 'string') array_push($where, ['t_subrequirements.id_requirement_type', $info[IdRequirementType]]);  
        // }
        if ($info['IdApplicationType'] != null) array_push($where, ['t_subrequirements.id_application_type', $info['IdApplicationType']]);
        if ($info['IdState'] != null) array_push($where, ['t_subrequirements.id_state', $info['IdState']]);
        if ($where) $query->where($where);
        //Order by
        $arrayOrder = [
            0 => 't_subrequirements.order',
            1 => 't_subrequirements.no_subrequirement',
            2 => 't_subrequirements.subrequirement'
        ];
        $column = $arrayOrder[$info['order'][0]['column']];
        $dir = (isset($info['order'][0]['dir']) ? $info['order'][0]['dir'] : 'desc');            
        $query->orderBy($column, $dir);
        // Paginate
        $totalRecords = $query->count('t_subrequirements.id_subrequirement');
        $paginate = $query->skip($info['start'])->take($info['length'])->get();
        $data['data'] = $paginate;
        $data['recordsTotal'] = $totalRecords;
        $data['draw'] = intval($info['draw']);
        $data['recordsFiltered'] = $totalRecords;
        return $data;
    }
    /**
     * Get data subrequirement by id
     */
    public function scopeGetSubrequirementById($query, $idSubrequirement){
        $query->where('id_subrequirement', $idSubrequirement);
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Get info by group requirement
     */
    public function scopeGetGroupSubrequirement($query, $idSubrequirementsArray){
        $query->join('c_conditions', 'c_conditions.id_condition', 't_subrequirements.id_condition')
            ->join('c_aspects', 'c_aspects.id_aspect', 't_subrequirements.id_aspect')
            ->join('c_matters', 'c_matters.id_matter', 'c_aspects.id_matter')
            ->join('c_application_types', 'c_application_types.id_application_type', 't_subrequirements.id_application_type')
            ->whereIn('t_subrequirements.id_subrequirement', $idSubrequirementsArray)
            ->orderBy('t_subrequirements.id_application_type', 'ASC')
            ->orderBy('t_subrequirements.order', 'ASC')
            ->orderBy('t_subrequirements.id_requirement_type', 'ASC');
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * delete subrequirements
     */
    public function scopeDeleteSubrequirement($query, $idSubrequirement) {
        $model = SubrequirementsModel::find($idSubrequirement);
        try {
            $model->delete();
            return StatusConstants::SUCCESS;
        } catch (\Throwable $th) {
            return StatusConstants::ERROR;
        }
    }
    /**
     * Get all basis related to a requiriment
     */
    public function scopeGetSubrequirementBasis($query, $idSubrequirement)
    {
        $data = \DB::table('r_subrequirements_legal_basies')
            ->select('id_legal_basis')
            ->where('id_subrequirement', $idSubrequirement)
            ->get()
            ->toArray();
        return $data;
    }
    /**
     * sets or deletes a relation between a basis and requirement
     */
    public function scopeUpdateSubrequirementBasis($query, $idSubrequirement, $idBasis, $status)
    {
        if($status == 'true') $update = \DB::table('r_subrequirements_legal_basies')
            ->insert([
                'id_subrequirement' => $idSubrequirement,
                'id_legal_basis' => $idBasis
            ]);
        
        else  $update = \DB::table('r_subrequirements_legal_basies')
                ->where([
                    ['id_subrequirement', '=',$idSubrequirement],
                    ['id_legal_basis', '=',$idBasis]
                ])
                ->delete();
        if($update) $response = StatusConstants::SUCCESS;
        else $response = StatusConstants::ERROR;
        return $response;
    }
    /**
     *  Gets Subrequirements relationed to a question and requirement ids
     */
    public function scopeGetSubrequirementsByIdQuestion($query, $idQuestion, $idRequirement)
    {
        return \DB::table('r_question_requirements')
        ->select('id_requirement', 'id_subrequirement')    
        ->where([
            ['id_question', $idQuestion],
            ['id_requirement', $idRequirement]
        ])
        ->get();
    }
    /**
     * Get subrequiremnts by requiremnt
     */
    public function scopeGetSubrequirements($query, $idRequirement){
        $query->join('c_conditions', 'c_conditions.id_condition', 't_subrequirements.id_condition')
            ->join('c_application_types', 'c_application_types.id_application_type', 't_subrequirements.id_application_type')
            ->where('t_subrequirements.id_requirement', $idRequirement)
            ->orderBy('t_subrequirements.no_subrequirement', 'ASC');
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Get info by subrequirement
     */
    public function scopeGetSubrequirement($query, $idSubrequirement){
        $query->where('t_subrequirements.id_subrequirement', $idSubrequirement);
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Get Subrequirements for Audit
     */
    public function scopeGetSubrequirementsAudit($query, $idSubequirementArray){
        $query->join('c_conditions', 'c_conditions.id_condition', 't_subrequirements.id_condition')
            ->join('c_application_types', 'c_application_types.id_application_type', 't_subrequirements.id_application_type')
            ->whereIn('t_subrequirements.id_subrequirement', $idSubequirementArray)
            ->orderBy('t_subrequirements.order', 'ASC');
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Get subrequiremnts by requiremnt with type
     */
    public function scopeGetSubrequirementsByType($query, $idRequirement, $requirementType){
        $query->join('c_conditions', 'c_conditions.id_condition', 't_subrequirements.id_condition')
            ->where('t_subrequirements.id_requirement', $idRequirement)
            ->Where('t_subrequirements.id_requirement_type', $requirementType)
            ->orderBy('t_subrequirements.no_subrequirement', 'ASC');
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Get All data subrequirements
     */
    public function scopeGetAllDataSubrequirement($query, $idSubrequirement){
        $query->join('c_matters', 'c_matters.id_matter', 't_subrequirements.id_matter')
            ->join('c_aspects', 'c_aspects.id_aspect', 't_subrequirements.id_aspect')
            ->leftJoin('c_evidences', 'c_evidences.id_evidence', 't_subrequirements.id_evidence')
            ->join('c_conditions', 'c_conditions.id_condition', 't_subrequirements.id_condition')
            ->join('c_periods', 'c_periods.id_period', 't_subrequirements.id_obtaining_period')
            ->leftJoin('c_periods as c_periods_update', 'c_periods.id_period', 't_subrequirements.id_update_period')
            ->join('c_requirement_types', 'c_requirement_types.id_requirement_type', 't_subrequirements.id_requirement_type')
            ->join('c_application_types', 'c_application_types.id_application_type', 't_subrequirements.id_application_type')
            ->leftJoin('c_states', 'c_states.id_state', 't_subrequirements.id_state')
            ->leftJoin('c_cities', 'c_cities.id_city', 't_subrequirements.id_city')
            ->select(
                'c_matters.matter',
                'c_aspects.aspect',
                'c_evidences.evidence',
                't_subrequirements.document',
                'c_conditions.condition',
                'c_periods.period as period_obtaining',
                'c_periods_update.period as period_update',
                't_subrequirements.order',
                'c_requirement_types.requirement_type',
                'c_application_types.application_type',
                'c_states.state',
                'c_cities.city',
                't_subrequirements.no_subrequirement',
                't_subrequirements.subrequirement',
                't_subrequirements.description',
                't_subrequirements.help_subrequirement',
                't_subrequirements.acceptance'
            )
            ->where('t_subrequirements.id_subrequirement', $idSubrequirement);
        $data = $query->get()->toArray();
        return $data;
    }

    public function scopeGetEstatalSubrequirement($query, $idAspect){
        $query->join('c_conditions', 'c_conditions.id_condition', 't_subrequirements.id_condition')
            ->where('t_subrequirements.id_aspect', $idAspect)
            ->where('t_subrequirements.id_application_type', StatusConstants::STATE)
            ->orderBy('t_subrequirements.order', 'ASC');
        $data = $query->get()->toArray();
        return $data;
    }

    public function scopeGetLocalSubrequirement($query, $idAspect){
        $query->join('c_conditions', 'c_conditions.id_condition', 't_subrequirements.id_condition')
            ->where('t_subrequirements.id_aspect', $idAspect)
            ->where('t_subrequirements.id_application_type', StatusConstants::LOCAL)
            ->orderBy('t_subrequirements.order', 'ASC');
        $data = $query->get()->toArray();
        return $data;
    }

     /**
     * Get only requirements without relation to question
     */
    public function scopeGetOnlySubrequirementByForm($query, $idRequirementArray, $idApplicationType, $idState, $idCity){
        if ($idState != null) $query->where('t_subrequirements.id_state', $idState);
        else $query->whereNull('t_subrequirements.id_state');
        if ($idCity != null) $query->where('t_subrequirements.id_city', $idCity);
        else $query->whereNull('t_subrequirements.id_city');
        $query->whereIn('t_subrequirements.id_requirement', $idRequirementArray)
            ->where('t_subrequirements.id_application_type', $idApplicationType)
            ->select('t_subrequirements.id_requirement', 't_subrequirements.id_subrequirement')
            ->orderBy('t_subrequirements.order', 'ASC');
        $data = $query->get()->toArray();
        return $data;
    }
}