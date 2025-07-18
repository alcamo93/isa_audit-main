<?php

namespace App\Models\Catalogues;

use Illuminate\Database\Eloquent\Model;
use App\Classes\StatusConstants;

class BasisModel extends Model
{
    protected $table = 't_legal_basises';
    protected $primaryKey = 'id_legal_basis';
    /**
     * Set Basis
     */
    public function scopeSetBasis($query, $info) {
        $model = new BasisModel();
        $model->legal_basis = $info['basis'];
        // $model->legal_quote = $info['quote'];
        $model->id_guideline = $info['guideline'];
        $model->id_application_type = $info['applicationType'];
        $model->publish = $info['publish'];
        $model->order = $info['order'];
        try {
            $model->save();
            $data['idBasis'] = $model->id_legal_basis;
            $data['status'] = StatusConstants::SUCCESS;
            return $data;
        } catch (\Throwable $th) {
            $data['status'] = StatusConstants::ERROR;
            return $data;
        }
    }
    /**
     * Set data quote
     */
    public function scopeUpdateQuote($query, $idLegalBasis, $quote){
        try {
            $model = BasisModel::findOrFail($idLegalBasis);
        } catch (\ModelNotFoundException $nf) {
            return StatusConstants::WARNING;
        }
        $model->legal_quote = $quote;
        try {
            $model->save();
            $data['idBasis'] = $model->id_legal_basis;
            $data['status'] = StatusConstants::SUCCESS;
            return $data;
        } catch (\Throwable $th) {
            $data['status'] = StatusConstants::ERROR;
            return $data;
        }
    }
    /**
     * Update Basis
     */
    public function scopeUpdateBasis($query, $info, $quote) {
        try {
            $model = BasisModel::findOrFail($info['idBasis']);
        } catch (\ModelNotFoundException $nf) {
            return StatusConstants::WARNING;
        }
        $model->legal_basis = $info['basis'];
        $model->legal_quote = $quote;
        $model->id_guideline = $info['guideline'];
        $model->id_application_type = $info['applicationType'];
        $model->publish = $info['publish'];
        $model->order = $info['order'];
        try {
            $model->save();
            $data['idBasis'] = $model->id_legal_basis;
            $data['status'] = StatusConstants::SUCCESS;
            return $data;
        } catch (\Throwable $th) {
            $data['status'] = StatusConstants::ERROR;
            return $data;
        }
    }
    /**
     * delete Basis
     */
    public function scopeDeleteBasis($query, $idBasis) {
        try {
            $model = BasisModel::findOrFail($idBasis);
        } catch (\ModelNotFoundException $nf) {
            $data['status'] = StatusConstants::WARNING;
            return $data;
        }
        try{
            $model->delete();
            $data['status'] = StatusConstants::SUCCESS;
            return $data;
        }
        catch (\Exception $e) {
            $data['status'] = StatusConstants::ERROR;
            return $data;
        }
    }
    /**
     * Get basis to datatables
     */
    public function scopeGetBasisDT($query, $info){
        $query->join('t_guidelines', 't_guidelines.id_guideline', 't_legal_basises.id_guideline')
            ->select('t_guidelines.id_guideline', 't_guidelines.initials_guideline', 't_guidelines.guideline', 
                't_legal_basises.id_legal_basis', 't_legal_basises.legal_basis', 't_legal_basises.order');
        $where = [];
        array_push($where, ['t_legal_basises.id_guideline', $info['id_guideline']]);
        if ($info['filterName'] != null) array_push($where, ['t_legal_basises.legal_basis','LIKE','%'.$info['filterName'].'%']);
        if($info['filterArt'] != null) array_push($where, ['t_legal_basises.legal_basis', 'LIKE', '%'.$info['filterArt'].'%']);
        if($where) $query->where($where);
        // Order Modules
        $arrayOrder = [
            'main' => [
                't_legal_basises.order',
                't_legal_basises.legal_basis',
            ],
            'questions' => [
                't_guidelines.guideline',
                't_legal_basises.order',
                't_legal_basises.legal_basis'
            ],
            'requirements' => [
                't_guidelines.guideline',
                't_legal_basises.order',
                't_legal_basises.legal_basis'
            ]
        ];
        $column = $arrayOrder[$info['filterOrder']][$info['order'][0]['column']];
        $dir = (isset($info['order'][0]['dir']) ? $info['order'][0]['dir'] : 'desc');            
        $query->orderBy($column, $dir);
        $query->orderBy('t_legal_basises.order', 'ASC');
        // Paginate
        $totalRecords = $query->count('t_legal_basises.id_guideline');
        $paginate = $query->skip($info['start'])->take($info['length'])->get();
        $data['data'] = $paginate;
        $data['recordsTotal'] = $totalRecords;
        $data['draw'] = intval($info['draw']);
        $data['recordsFiltered'] = $totalRecords;
        return $data;
    }

    public function scopeGetUpdatesBasis($query, $dates){
        $query->join('t_guidelines','t_guidelines.id_guideline','t_legal_basises.id_guideline')
            ->select('t_guidelines.guideline','t_legal_basises.legal_basis', 
                \DB::raw('DATE_FORMAT(t_legal_basises.updated_at, "%d/%m/%Y") AS update_format'))
            ->whereBetween('t_legal_basises.updated_at',[$dates['init'],$dates['finish']])
            ->where('t_legal_basises.publish', 1);
        $data = $query->get()->toArray();
        return $data;
    }
}
