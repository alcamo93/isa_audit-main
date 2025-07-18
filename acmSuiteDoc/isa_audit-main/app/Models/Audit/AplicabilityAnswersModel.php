<?php

namespace App\Models\Audit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException as Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Classes\StatusConstants;
use Illuminate\Support\Facades\Session;

class AplicabilityAnswersModel extends Model
{
    protected $table = 't_aplicability_answers';
    protected $primaryKey = 'id_aplicability_answer';

    /**
     * Get answers by aplicability
     */
    public function scopeGetAnswers($query, $idAplicability){
        $query->where('id_aplicability', $idAplicability);
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Set answer by answer question in aplicability
     */
    public function scopeSetAnswer($query, $info){
        try {
            $model = AplicabilityAnswersModel::where([
                ['id_answer_question', $info['idAnswerQuestion']],
                ['id_aplicability', $info['idAplicability']],
                ['id_contract', $info['idContract']],
                ['id_audit_processes', $info['idAuditProcess']],
                ['id_aspect', $info['idAspect']],
            ])->firstOrFail();
        } catch (ModelNotFoundException $th) {
            $model = new AplicabilityAnswersModel;
            $model->id_answer_question = $info['idAnswerQuestion'];
            $model->id_aplicability = $info['idAplicability'];
            $model->id_contract = $info['idContract'];
            $model->id_audit_processes = $info['idAuditProcess'];
            $model->id_aspect = $info['idAspect'];
            $model->id_user = $idUser = Session::get('user')['id_user'];
        }
        // Save data
        try {
            if ($info['setAnswer'] == 'true') $model->save();
            else $model->delete();
            return StatusConstants::SUCCESS;
        } catch (Exception $e) {
            return StatusConstants::ERROR;
        }
    }
    /**
     * Delete answers by aplicability id
     */
    public function scopeDeleteAnswers($query, $idAplicability){
        try {
            $model = AplicabilityAnswersModel::where('id_aplicability', $idAplicability);
        } catch (ModelNotFoundException $th) {
            return StatusConstants::WARNING;
        }
        try {
            $model->delete();
            return StatusConstants::SUCCESS;
        } catch (\Throwable $th) {
            return StatusConstants::ERROR;
        }
    }
    
}
