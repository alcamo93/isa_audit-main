<?php

namespace App\Models\Catalogues;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Classes\StatusConstants;

class RequirementsModel extends Model
{
    protected $table = 't_requirements';
    protected $primaryKey = 'id_requirement';
    
    public function subrequirements(){
        return $this->hasMany('App\Models\Catalogues\SubrequirementsModel', 'id_requirement', 'id_requirement');
    }

    public function condition(){
        return $this->hasOne('App\Models\Catalogues\ConditionsModel', 'id_condition', 'id_condition');
    }

    public function aspect(){
        return $this->hasOne('App\Models\Catalogues\AspectsModel', 'id_aspect', 'id_aspect');
    }

    public function applicationType(){
        return $this->hasOne('App\Models\Catalogues\ApplicationTypesModel', 'id_application_type', 'id_application_type');
    }
    /**
     * Set requirements
     */
    public function scopeSetRequirement($query, $info) {
        $model = new RequirementsModel();
        $model->form_id = $info['IdForm'];
        $model->no_requirement = $info['noRequirement'];
        $model->requirement = $info['requirement'];
        $model->description = $info['requirementDesc'];
        $model->help_requirement = $info['requirementHelp'] ?? null;
        $model->acceptance = $info['requirementAcceptance'] ?? null;
        $model->id_matter = $info['IdMatter'];
        $model->id_aspect = $info['IdAspect'];
        $model->id_evidence = $info['IdEvidence'] ?? null;
        if (isset($info['IdEvidence']) && $info['IdEvidence'] == StatusConstants::SPECIFIC) {
            $model->document = $info['document'];
        }
        else {
            $model->document = null;
        }
        if ($info['IdRequirementType'] == StatusConstants::COMPOSITE_REQUIREMENTS || 
            $info['IdRequirementType'] == StatusConstants::COMPOSITE_REQUIREMENTS_IDENTIFICATION) {
            $model->has_subrequirement = 1;
        }
        else {
            $model->has_subrequirement = 0;
        }
        $model->id_obtaining_period = $info['IdObtainingPeriod'] ?? null;
        $model->id_update_period = $info['IdUpdatePeriod'] ?? null;
        $model->id_condition = $info['IdCondition'];
        $model->id_requirement_type = $info['IdRequirementType'];
        $model->id_application_type = $info['IdAplicationType'];
        $model->id_state = $info['IdState'];
        $model->id_city = $info['IdCity'];
        $model->order = $info['order'];
        $model->id_customer = ($info['idCustomer'] == 0 || $info['idCustomer'] == null) ? NULL : $info['idCustomer'];
        $model->id_corporate = ($info['idCorporate'] == 0 || $info['idCorporate'] == null) ? NULL : $info['idCorporate'];
        try {
            $model->save();
            $response['status'] = StatusConstants::SUCCESS;
            $response['idRequirement'] = $model->id_requirement;
            $response['model'] = $model;
        } catch (\Exception $e) {
            if ($e->errorInfo[1] == 1062) $response['status'] = StatusConstants::WARNING;
            $response['status'] = StatusConstants::ERROR;
        }
        return $response;
    }
    /**
     * Get requirements for datatables
     */
    public function scopeGetRequirementsDT($query, $info) {
        $where = [];
        if ($info['IdForm'] != null) array_push($where, ['t_requirements.form_id', $info['IdForm']]);
        if ($info['filterRequirementNumber'] != null) array_push($where, ['t_requirements.no_requirement','LIKE','%'.$info['filterRequirementNumber'].'%']);
        if ($info['filterRequirement'] != null) array_push($where, ['t_requirements.requirement','LIKE','%'.$info['filterRequirement'].'%']);
        if ($info['IdMatter'] != null) array_push($where, ['t_requirements.id_matter', $info['IdMatter']]);
        if ($info['IdAspect'] != null) array_push($where, ['t_requirements.id_aspect', $info['IdAspect']]);
        if ($info['IdEvidence'] != null) array_push($where, ['t_requirements.id_evidence', $info['IdEvidence']]);
        if ($info['IdObtainingPeriod'] != null) array_push($where, ['t_requirements.id_obtaining_period', $info['IdObtainingPeriod']]);
        if ($info['IdUpdatePeriod'] != null) array_push($where, ['t_requirements.id_update_period', $info['IdUpdatePeriod']]);
        if ($info['IdCondition'] != null) array_push($where, ['t_requirements.id_condition', $info['IdCondition']]);
        if ($info['IdRequirementType'] != null) {
            if (gettype($info['IdRequirementType']) == 'array')  $query->whereIn('t_requirements.id_requirement_type', $info['IdRequirementType']);
            if (gettype($info['IdRequirementType']) == 'string') array_push($where, ['t_requirements.id_requirement_type', $info['IdRequirementType']]);  
        }
        else {
            $query->whereNotIn('t_requirements.id_requirement_type', [11]);
        }
        if ($info['IdAplicationType'] != null) array_push($where, ['t_requirements.id_application_type', $info['IdAplicationType']]);
        if ($info['IdState'] != '0' && $info['IdState'] != null) array_push($where, ['t_requirements.id_state', $info['IdState']]);
        if ($info['IdCity'] != '0' && $info['IdCity'] != null) array_push($where, ['t_requirements.id_city', $info['IdCity']]);
        if ($info['filterIdCustomer'] != '0') array_push($where, ['t_requirements.id_customer', $info['filterIdCustomer']]);
        if ($info['filterIdCorporate'] != '0') array_push($where, ['t_requirements.id_corporate', $info['filterIdCorporate']]);
        if ($where) $query->where($where);
        //Order by
        $arrayOrder = [
            0 => 't_requirements.order',
            1 => 't_requirements.no_requirement',
            2 => 't_requirements.requirement'
        ];
        $column = $arrayOrder[$info['order'][0]['column']];   
        $dir = (isset($info['order'][0]['dir']) ? $info['order'][0]['dir'] : 'desc');            
        $query->orderBy($column, $dir);
        // Paginate
        $totalRecords = $query->count();
        $paginate = $query->skip($info['start'])->take($info['length'])->get();
        $data['data'] = $paginate;
        $data['recordsTotal'] = $totalRecords;
        $data['draw'] = intval($info['draw']);
        $data['recordsFiltered'] = $totalRecords;
        return $data;
    }
    /**
     * Get data requirement by id
     */
    public function scopeGetRequirementByIdRequirement($query, $idRequirement){
        $query->where('t_requirements.id_requirement', $idRequirement);
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Get data requirement by id obligation
     */
    public function scopeGetRequirementById($query, $idRequirement){
        $query->where('t_requirements.id_requirement', $idRequirement)
        ->leftJoin('r_obligation_requirements', 'r_obligation_requirements.id_requirement','=', 't_requirements.id_requirement');
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Update requirements
     */
    public function scopeUpdateRequirement($query, $info) {
        $model = RequirementsModel::find($info['idRequirement']);
        $model->form_id = $info['IdForm'];
        $model->no_requirement = $info['noRequirement'];
        $model->requirement = $info['requirement'];
        $model->description = $info['requirementDesc'];
        $model->help_requirement = $info['requirementHelp'] ?? null;
        $model->acceptance = $info['requirementAcceptance'] ?? null;
        $model->id_matter = $info['IdMatter'];
        $model->id_aspect = $info['IdAspect'];
        $model->id_evidence = $info['IdEvidence'] ?? null;
        if (isset($info['IdEvidence']) && $info['IdEvidence'] == StatusConstants::SPECIFIC) {
            $model->document = $info['document'];
        }
        else {
            $model->document = null;
        }
        if ($info['IdRequirementType'] == StatusConstants::COMPOSITE_REQUIREMENTS || 
            $info['IdRequirementType'] == StatusConstants::COMPOSITE_REQUIREMENTS_IDENTIFICATION) {
            $model->has_subrequirement = 1;
        }
        else {
            $model->has_subrequirement = 0;
        }
        $model->id_obtaining_period = $info['IdObtainingPeriod'] ?? null;
        $model->id_update_period = $info['IdUpdatePeriod'] ?? null;
        $model->id_condition = $info['IdCondition'];
        $model->id_requirement_type = $info['IdRequirementType'];
        $model->id_application_type = $info['IdAplicationType'];
        $model->id_state = $info['IdState'];
        $model->id_city = $info['IdCity'];
        $model->order = $info['order'];
        $model->id_customer = ($info['idCustomer'] == 0 || $info['idCustomer'] == null) ? NULL : $info['idCustomer'];
        $model->id_corporate = ($info['idCorporate'] == 0 || $info['idCorporate'] == null) ? NULL : $info['idCorporate'];
        try {
            $model->save();
            $response['status'] = StatusConstants::SUCCESS;
            $response['idRequirement'] = $model->id_requirement;
        } catch (\Exception $e) {
            $response['status'] = StatusConstants::ERROR;
        }
        return $response;
    }
    /**
     * Update requirements set has subrequirements
     */
    public function scopeUpdateSetHasSubrequirements($query, $idRequirement )
    {
        $subRCount = \DB::table('t_subrequirements')
            ->where('id_requirement', $idRequirement)
            ->count();

        if($subRCount < 1) $query
            ->where('id_requirement', $idRequirement)
            ->update([
                'has_subrequirement' => 0
            ]);
        else $query
            ->where('id_requirement', $idRequirement)
            ->update([
                'has_subrequirement' => 1
            ]);
        return true;
    }
    /**
     * delete requirements
     */
    public function scopeDeleteRequirement($query, $idRequirement)
    {
        $response = StatusConstants::ERROR;
        try{
            $query
            ->where('id_requirement', $idRequirement)
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
     * Get all basis related to a requiriment
     */
    public function scopeGetRequirementBasis($query, $idRequirement)
    {
        $data = \DB::table('r_requirements_legal_basies')
            ->select('id_legal_basis')
            ->where('id_requirement', $idRequirement)
            ->get()
            ->toArray();
        return $data;
    }
    /**
     * sets or deletes a relation between a basis and requirement
     */
    public function scopeUpdateRequirementBasis($query, $idRequirement, $idBasis, $status)
    {
        if($status == 'true') $update = \DB::table('r_requirements_legal_basies')
            ->insert([
                'id_requirement' => $idRequirement,
                'id_legal_basis' => $idBasis
            ]);
        
        else  $update = \DB::table('r_requirements_legal_basies')
                ->where([
                    ['id_requirement', '=',$idRequirement],
                    ['id_legal_basis', '=',$idBasis]
                ])
                ->delete();
        if($update) $response = StatusConstants::SUCCESS;
        else $response = StatusConstants::ERROR;
        return $response;
    }
    /**
     * Get info byrequirement
     */
    public function scopeGetRequirement($query, $idRequirement){
        $query->where('t_requirements.id_requirement', $idRequirement);
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Get info by group requirement
     */
    public function scopeGetGroupRequirement($query, $idRequirementsArray){
        $query
            ->select(
                't_requirements.*',
                'c_conditions.*',
                'c_matters.matter',
                'c_aspects.aspect',
                'c_application_types.*'
            )
            ->join('c_conditions', 'c_conditions.id_condition', 't_requirements.id_condition')
            ->join('c_aspects', 'c_aspects.id_aspect', 't_requirements.id_aspect')
            ->join('c_matters', 'c_matters.id_matter', 'c_aspects.id_matter')
            ->join('c_application_types', 'c_application_types.id_application_type', 't_requirements.id_application_type')
            ->whereIn('t_requirements.id_requirement', $idRequirementsArray)
            ->orderBy('t_requirements.id_application_type', 'ASC')
            ->orderBy('t_requirements.order', 'ASC')
            ->orderBy('t_requirements.id_requirement_type', 'ASC');
        $data = $query->get()->toArray();

        return $data;
    }
    /**
     * Get only requirements without relation to question
     */
    public function scopeGetOnlyRequirement($query, $idRequirementTypeArray, $idApplicationType, $idState, $idCity, $idAspect){
        if ($idState != null) $query->where('t_requirements.id_state', $idState);
        else $query->whereNull('t_requirements.id_state');
        if ($idCity != null) $query->where('t_requirements.id_city', $idCity);
        else $query->whereNull('t_requirements.id_city');
        $query->whereIn('t_requirements.id_requirement_type', $idRequirementTypeArray)
            ->where('t_requirements.id_application_type', $idApplicationType)
            ->where('t_requirements.id_aspect', $idAspect)
            ->orderBy('t_requirements.order', 'ASC');
        $data = $query->pluck('t_requirements.id_requirement')->toArray();
        return $data;
    }
    /**
     * Get only requirements without relation to question
     */
    public function scopeGetOnlyRequirementByForm($query, $idRequirementTypeArray, $idApplicationType, $idState, $idCity, $idForm){
        if ($idState != null) $query->where('t_requirements.id_state', $idState);
        else $query->whereNull('t_requirements.id_state');
        if ($idCity != null) $query->where('t_requirements.id_city', $idCity);
        else $query->whereNull('t_requirements.id_city');
        $query->whereIn('t_requirements.id_requirement_type', $idRequirementTypeArray)
            ->where('t_requirements.id_application_type', $idApplicationType)
            ->where('t_requirements.form_id', $idForm)
            ->orderBy('t_requirements.order', 'ASC');
        $data = $query->pluck('t_requirements.id_requirement')->toArray();
        return $data;
    }
    /**
     * Get only specific requirement 
     */
    public function scopeGetOnlySpecificRequirements($query, $idRequirementType, $idAspect, $idCorporate){
        $query->where('t_requirements.id_requirement_type', $idRequirementType)
            ->where('t_requirements.id_aspect', $idAspect)
            ->where('t_requirements.id_corporate', $idCorporate)
            ->orderBy('t_requirements.order', 'ASC');
        $data = $query->pluck('t_requirements.id_requirement')->toArray();
        return $data;
    }
    /**
     * Count requirements in aspect
     */
    public function scopeCountRequirementByAspect($query, $idAspect, $idRequirementType, $idState, $idCity){
        if ($idState != null) $query->where('t_requirements.id_state', $idState);
        else $query->whereNull('t_requirements.id_state');
        if ($idCity != null) $query->where('t_requirements.id_city', $idCity);
        else $query->whereNull('t_requirements.id_city');
        $query->where('t_requirements.id_aspect', $idAspect)
            ->where('t_requirements.id_requirement_type', $idRequirementType)
            ->orderBy('t_requirements.order', 'ASC');
        $data = $query->get()->count();
        return $data;
    }
    /**
     * Get all data Requirement
     */
    public function scopeGetAllDataRequirement($query, $idRequirement){
        $query->leftJoin('t_customers', 't_customers.id_customer', 't_requirements.id_customer')
            ->leftJoin('t_corporates', 't_corporates.id_corporate', 't_requirements.id_corporate')
            ->join('c_matters', 'c_matters.id_matter', 't_requirements.id_matter')
            ->join('c_aspects', 'c_aspects.id_aspect', 't_requirements.id_aspect')
            ->leftJoin('c_evidences', 'c_evidences.id_evidence', 't_requirements.id_evidence')
            ->leftJoin('c_conditions', 'c_conditions.id_condition', 't_requirements.id_condition')
            ->leftJoin('c_periods', 'c_periods.id_period', 't_requirements.id_obtaining_period')
            ->leftJoin('c_periods as c_periods_update', 'c_periods.id_period', 't_requirements.id_update_period')
            ->leftJoin('c_requirement_types', 'c_requirement_types.id_requirement_type', 't_requirements.id_requirement_type')
            ->leftJoin('c_application_types', 'c_application_types.id_application_type', 't_requirements.id_application_type')
            ->leftJoin('c_states', 'c_states.id_state', 't_requirements.id_state')
            ->leftJoin('c_cities', 'c_cities.id_city', 't_requirements.id_city')
            ->select(
                't_customers.cust_trademark',
                't_corporates.corp_tradename',
                'c_matters.matter',
                'c_aspects.aspect',
                'c_evidences.evidence',
                't_requirements.document',
                'c_conditions.condition',
                'c_periods.period as period_obtaining',
                'c_periods_update.period as period_update',
                't_requirements.order',
                'c_requirement_types.requirement_type',
                'c_application_types.application_type',
                'c_states.state',
                'c_cities.city',
                't_requirements.no_requirement',
                't_requirements.requirement',
                't_requirements.description',
                't_requirements.help_requirement',
                't_requirements.acceptance'
            )
            ->where('t_requirements.id_requirement', $idRequirement);
        $data = $query->get()->toArray();
        return $data;
    }
}
