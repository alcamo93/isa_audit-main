<?php

namespace App\Models\Catalogues;

use Illuminate\Database\Eloquent\Model;
use App\Classes\StatusConstants;

class QuestionLegalBasiesModel extends Model
{
    protected $table = 'r_question_legal_basies';
    protected $primaryKey = 'id_question_lb';
    /**
     * Get relation
     */
    public function scopeGetBasisByIdQuestion($query, $idQuestion) {
        $query->select('id_legal_basis')
        ->where('id_question', $idQuestion);
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Get basis to datatables
     */
    public function scopeGetBasisQuestionsDT($query, $info) {
        $query->join('t_legal_basises', 'r_question_legal_basies.id_legal_basis', 't_legal_basises.id_legal_basis')
            ->join('t_guidelines', 't_guidelines.id_guideline', 't_legal_basises.id_guideline')
            ->select('t_guidelines.id_guideline', 't_guidelines.initials_guideline', 't_guidelines.guideline', 
                't_legal_basises.id_legal_basis', 't_legal_basises.legal_basis', 't_legal_basises.order');
        $where = [];
        if($info['filterGuideline'] != null) array_push($where, ['t_legal_basises.id_guideline', $info['filterGuideline']]);
        if($info['filterArtSelected'] != null) array_push($where, ['t_legal_basises.legal_basis', 'LIKE', '%'.$info['filterArtSelected'].'%']);
        if($where) $query->where($where);
        $query->where('r_question_legal_basies.id_question', $info['idQuestion']);
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
    /**
     * Set question basis by question id
     */
    public function scopeUpdateQuestionBasis($query, $idQuestion, $idBasis, $status){
        if($status == 'true') {
            $model = new QuestionLegalBasiesModel();
            $model->id_question = $idQuestion;
            $model->id_legal_basis = $idBasis;
            try {
                $model->save();
                return StatusConstants::SUCCESS;
            } catch (\Throwable $th) {
                return StatusConstants::ERROR;
            }
        }   
        else {
            $model = QuestionLegalBasiesModel::where([
                ['id_question' , $idQuestion],
                ['id_legal_basis' , $idBasis]
            ])->firstOrFail();
            try {
                $model->delete();;
                return StatusConstants::SUCCESS;
            } catch (\Throwable $th) {
                return StatusConstants::ERROR;
            }
        }
    }
    /**
     * Get legals basis
     */
    public function scopeGetBasisByQuestions($query, $idQuestion){
        $query->join('t_legal_basises', 't_legal_basises.id_legal_basis', 'r_question_legal_basies.id_legal_basis')
            ->join('t_guidelines', 't_guidelines.id_guideline', 't_legal_basises.id_guideline')
            ->where('r_question_legal_basies.id_question', $idQuestion);
        $data = $query->get()->toArray();
        return $data;
    }
}