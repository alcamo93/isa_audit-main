<?php

namespace App\Models\Audit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException as Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Classes\StatusConstants;
use Illuminate\Support\Facades\Session;

class AuditModel extends Model
{
    use \Awobaz\Compoships\Compoships;

    protected $table = 't_audit';
    protected $primaryKey = 'id_audit';

    const NEGATIVE_ANSWER = 0;
    const POSITIVE_ANSWER = 1;
    const NO_APPLY_ANSWER = 2;

    public function process()
    {
        return $this->belongsTo('App\Models\Admin\ProcessesModel', 'id_audit_processes', 'id_audit_processes');
    }

    public function requirement()
    {
        return $this->hasOne('App\Models\Catalogues\RequirementsModel', 'id_requirement', 'id_requirement');
    }

    public function subrequirement()
    {
        return $this->hasOne('App\Models\Catalogues\SubrequirementsModel', 'id_subrequirement', 'id_subrequirement');
    }

	/**
	 * Get all of the risk_totals for the Audit
	 */
	public function risk_totals()
	{
	    return $this->morphMany('App\Models\V2\Audit\RiskTotal', 'registerable');
	}

	/**
	 * Get all of the risk_answers for the Audit
	 */
	public function risk_answers()
	{
	    return $this->morphMany('App\Models\V2\Audit\RiskAnswer', 'registerable');
	}


    public function aspect()
    {
        return $this->belongsTo('App\Models\Catalogues\AspectsModel', 'id_aspect', 'id_aspect');
    }

    /**
     * Set anser in audit
     */
    public function scopeSetAudit($query, $info)
    {
        $answer = false;
        try {
            $model = AuditModel::where([
                ['id_audit_processes', $info['idAuditProcess']],
                ['id_audit_aspect', $info['idAuditAspect']],
                ['id_contract', $info['idContract']],
                ['id_requirement', $info['idRequirement']],
                ['id_aspect', $info['idAspect']],
                ['id_subrequirement', $info['idSubrequirement']],
            ])->firstOrFail();
            $answer = ($model->answer == 0) ? true : false;
        } catch (ModelNotFoundException $th) {
            $model = new AuditModel;
            $model->id_audit_processes = $info['idAuditProcess'];
            $model->id_audit_aspect = $info['idAuditAspect'];
            $model->id_contract = $info['idContract'];
            $model->id_requirement = $info['idRequirement'];
            $model->id_aspect = $info['idAspect'];
            $model->id_subrequirement = $info['idSubrequirement'];
            $model->id_audit_processes = $info['idAuditProcess'];
        }
        // Save info
        try {
            $model->id_user = $idUser = Session::get('user')['id_user'];
            $model->answer = $info['answer'];
            $model->finding = null;
            $model->save();
            $data['previousAnswer'] = $answer;
            $data['idAudit'] = $model->id_audit;
            $data['model'] = $model;
            $data['status'] = StatusConstants::SUCCESS;
            return $data;
        } catch (Exception $e) {
            return $data['status'] = StatusConstants::ERROR;
        }
    }
    /**
     * Set finding in audit
     */
    public function scopeSetFinding($query, $data)
    {
        try {
            $model = AuditModel::where([
                ['id_audit_processes', $data['idAuditProcess']],
                ['id_audit', $data['idAudit']],
                ['id_requirement', $data['idRequirement']],
                ['id_aspect', $data['idAspect']],
                ['id_subrequirement', $data['idSubrequirement']]
            ])->firstOrFail();
        } catch (ModelNotFoundException $th) {
            return StatusConstants::WARNING;
        }
        // Save data
        try {
            $model->finding = $data['finding'];
            $model->save();
            return StatusConstants::SUCCESS;
        } catch (Exception $e) {
            return StatusConstants::ERROR;
        }
    }
    /**
     * Set recomendation in audit
     */
    public function scopeSetRecomendation($query, $data)
    {
        try {
            $model = AuditModel::where([
                ['id_contract', '=', $data['idContract']],
                ['id_requirement', '=', $data['idRequirement']],
                ['id_aspect', '=', $data['idAspect']],
                ['id_subrequirement', '=', $data['idSubrequirement']]
            ])->firstOrFail();
        } catch (ModelNotFoundException $th) {
            return StatusConstants::WARNING;
        }
        // Save data
        try {
            if (is_null($data['idSubrequirement'])) {
                $model->id_recomendation = $data['idRecomendation'];
            } else {
                $model->id_subrecomendation = $data['idRecomendation'];
            }
            $model->save();
            return StatusConstants::SUCCESS;
        } catch (Exception $e) {
            return StatusConstants::ERROR;
        }
    }
    /**
     * Get answer by contract aspect
     */
    public function scopeGetAspectAnswers($query, $info)
    {
        $query->join('t_requirements', 't_requirements.id_requirement', 't_audit.id_requirement')
            ->where('t_audit.id_aspect', $info['idAspect'])
            ->where('t_audit.id_audit_processes', $info['idAuditProcess'])
            ->whereNull('t_audit.id_subrequirement');
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Get only answer
     */
    public function scopeGetOnlyAnswer($query, $info)
    {
        $query->where('t_audit.id_audit_processes', $info['idAuditProcess'])
            ->where('t_audit.id_audit', $info['idAudit'])
            ->where('t_audit.id_aspect', $info['idAspect'])
            ->where('t_audit.id_requirement', $info['idRequirement'])
            ->where('t_audit.id_subrequirement', $info['idSubrequirement']);
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Get requiremnt to action plan
     */
    public function scopeGetRequirementsByAnswer($query, $idContract, $answer)
    {
        $query->join('t_requirements', 't_requirements.id_requirement', 't_audit.id_requirement')
            ->select(
                't_requirements.id_obtaining_period',
                't_requirements.has_subrequirement',
                't_audit.finding',
                't_audit.id_contract',
                't_audit.id_aspect',
                't_audit.id_requirement',
                't_audit.id_subrequirement',
                't_audit.id_recomendation'
            )
            ->where('t_audit.id_contract', $idContract)
            ->where('t_audit.answer', $answer);
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Get requiremnt to action plan
     */
    public function scopeGetAuditedRequirements($query, $idAuditProcess, $idAspectsArray, $answer)
    {
        $query->join('t_requirements', 't_requirements.id_requirement', 't_audit.id_requirement')
            ->whereIn('t_audit.id_aspect', $idAspectsArray)
            ->where('t_audit.id_audit_processes', $idAuditProcess)
            ->where('t_audit.answer', $answer);
        if ($answer == 1) {
            $query->whereNotNull('t_requirements.id_update_period');
        }
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Get Subrequirements by requirement in audit
     */
    public function scopeGetSubrequirementAudit($query, $idContract, $idRequirement, $answer)
    {
        $query->join('t_requirements', 't_requirements.id_requirement', 't_audit.id_requirement')
            ->where('t_audit.id_requirement', $idRequirement)
            ->where('t_audit.id_contract', $idContract)
            ->where('t_audit.answer', $answer)
            ->whereNotNull('t_audit.id_subrecomendation');
        if ($answer == 1) {
            $query->whereNotNull('t_requirements.id_update_period');
        }
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Get answer subrequirements
     */
    public function scopeGetSubrequirementAnswers($query, $info)
    {
        $query->where('t_audit.id_audit_processes', $info['idAuditProcess'])
            ->where('t_audit.id_aspect', $info['idAspect'])
            ->where('t_audit.id_requirement', $info['idRequirement'])
            ->whereNotNull('t_audit.id_subrequirement');
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Get requirements answer by aspects
     */
    public function scopeGetReqAnswerByAspect($query, $idContract, $idAspect)
    {
        $query
            ->join('t_requirements', 't_requirements.id_requirement', 't_audit.id_requirement')
            ->join('c_conditions', 'c_conditions.id_condition', 't_requirements.id_condition')
            ->leftJoin('t_requirement_recomendations', 't_requirement_recomendations.id_requirement', 't_audit.id_requirement')
            ->join('c_aspects', 'c_aspects.id_aspect', 't_audit.id_aspect')
            ->join('c_periods', 't_requirements.id_obtaining_period',  'c_periods.id_period')
            ->select(
                't_audit.id_requirement',
                't_audit.answer',
                't_requirements.no_requirement',
                't_requirements.requirement',
                'c_conditions.condition',
                't_requirement_recomendations.recomendation',
                't_requirements.has_subrequirement',
                'c_periods.period',
                't_requirements.id_update_period',
                't_audit.finding'
            )
            ->where('t_audit.id_contract', $idContract)
            ->where('t_audit.id_aspect', $idAspect)
            ->whereNull('t_audit.id_subrequirement');
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Get subrequirements answer by aspects
     */
    public function scopeGetSubAnswerByAspect($query, $idContract, $idAspect, $idRequirement)
    {
        $query
            ->join('t_subrequirements', 't_subrequirements.id_subrequirement', 't_audit.id_subrequirement')
            ->join('c_conditions', 'c_conditions.id_condition', 't_subrequirements.id_condition')
            ->leftJoin('t_subrequirement_recomendations', 't_subrequirement_recomendations.id_subrequirement', 't_audit.id_subrequirement')
            ->join('c_aspects', 'c_aspects.id_aspect', 't_audit.id_aspect')
            ->join('c_periods', 't_subrequirements.id_obtaining_period',  'c_periods.id_period')
            ->select(
                't_audit.id_subrequirement',
                't_audit.answer',
                't_subrequirements.no_subrequirement',
                't_subrequirements.subrequirement',
                'c_conditions.condition',
                't_subrequirement_recomendations.recomendation',
                'c_periods.period',
                't_subrequirements.id_update_period',
                't_audit.finding'
            )
            ->where('t_audit.id_contract', $idContract)
            ->where('t_audit.id_aspect', $idAspect)
            ->where('t_audit.id_requirement', $idRequirement)
            ->whereNotNull('t_audit.id_subrequirement');
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Count answers in audit
     */
    public function scopeCountAnswers($query, $idAuditProcess, $idAspect, $answer, $exclud)
    {
        $hasSubrequirement = 0;
        $count = AuditModel::where([
            ['t_audit.id_audit_processes', $idAuditProcess],
            ['t_audit.id_aspect', $idAspect]
        ])
            ->whereNotIn('t_audit.id_audit', $exclud)
            ->whereIn('t_audit.answer', $answer);
        return $count->count();
    }

    public function scopeExcludAnswers($query, $idAuditProcess, $idAspect)
    {
        $hasSubrequirement = 1;
        $count = AuditModel::where([
            ['t_audit.id_audit_processes', $idAuditProcess],
            ['t_audit.id_aspect', $idAspect],
            ['t_requirements.has_subrequirement', $hasSubrequirement]
        ])
            ->whereNull('t_audit.id_subrequirement')
            ->join('t_requirements', 't_requirements.id_requirement', 't_audit.id_requirement');
        return $count->pluck('t_audit.id_audit')->toArray();
    }

    public function scopeGetAspectsByIdContract($query, $id_contract)
    {
        $data = AuditModel::select('id_aspect')->where('id_contract', $id_contract)->distinct()
            ->groupBy('id_aspect')->get()->toArray();
        return $data;
    }
}
