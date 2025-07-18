<?php

namespace App\Models\Catalogues;

use Illuminate\Database\Eloquent\Model;
use App\Classes\StatusConstants;

class RequirementsLegalBasiesModel extends Model
{
    protected $table = 'r_requirements_legal_basies';
    protected $primaryKey = 'id_requirement_lb';
    /**
     * Get legal basis
     */
    public function scopeGetBasiesByRequirement($query, $idRequirement){
        $query->join('t_legal_basises', 't_legal_basises.id_legal_basis', 'r_requirements_legal_basies.id_legal_basis')
            ->join('c_application_types', 't_legal_basises.id_application_type', 'c_application_types.id_application_type')
            ->join('t_guidelines', 't_guidelines.id_guideline', 't_legal_basises.id_guideline')
            ->join('c_legal_classification', 'c_legal_classification.id_legal_c', 't_guidelines.id_legal_c')
            ->where('r_requirements_legal_basies.id_requirement', $idRequirement);
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Get basis to datatables obtained by a id's requirement
     */
    public function scopeGetBasisByRequirementDT($query, $info) {
        $query->join('t_legal_basises', 'r_requirements_legal_basies.id_legal_basis', 't_legal_basises.id_legal_basis')
            ->join('t_guidelines', 't_guidelines.id_guideline', 't_legal_basises.id_guideline')
            ->select('t_guidelines.id_guideline', 't_guidelines.initials_guideline', 't_guidelines.guideline', 
                't_legal_basises.id_legal_basis', 't_legal_basises.legal_basis', 't_legal_basises.order');
        $where = [];
        array_push($where, ['r_requirements_legal_basies.id_requirement', $info['id']]);
        if($info['filterGuideline'] != null) array_push($where, ['t_legal_basises.id_guideline', $info['filterGuideline']]);
        if($info['filterArt'] != null) array_push($where, ['t_legal_basises.legal_basis', 'LIKE', '%'.$info['filterArt'].'%']);
        $query->where($where);
        //Order by
        $arrayOrder = [
            't_guidelines.guideline',
            't_legal_basises.order',
            't_legal_basises.legal_basis'
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
}