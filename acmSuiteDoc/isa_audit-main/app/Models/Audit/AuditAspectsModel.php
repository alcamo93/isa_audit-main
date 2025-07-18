<?php

namespace App\Models\Audit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException as Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Classes\StatusConstants;
use App\Models\Audit\AuditMattersModel;

class AuditAspectsModel extends Model
{
    protected $table = 'r_audit_aspects';
    protected $primaryKey = 'id_audit_aspect';

    public function audits()
    {
        return $this->hasMany('App\Models\Audit\AuditModel', 'id_audit_aspect', 'id_audit_aspect');
    }

    public function evaluates()
    {
        return $this->hasMany('App\Models\Audit\EvaluateRequirementModel', 'id_audit_aspect', 'id_audit_aspect');
    }

    /**
     * Get the user that owns the AuditAspectsModel
     */
    public function aspect()
    {
        return $this->belongsTo('App\Models\Catalogues\AspectsModel', 'id_aspect', 'id_aspect');
    }

    /**
     * Get the user that owns the AuditMattersModel
     */
    public function status()
    {
        return $this->belongsTo('App\Models\Catalogues\StatusModel', 'id_status', 'id_status');
    }

    public function scopeGetAuditAspectsDT($query, $page, $rows, $search, $draw, $order, $idAuditRegister, $idAuditMatter, $filterIdStatus)
    {
        $idAuditMatters = AuditMattersModel::where('id_audit_register', $idAuditRegister)->pluck('id_audit_matter');
        $query->join('c_status', 'r_audit_aspects.id_status', 'c_status.id_status')
            ->join('c_aspects', 'r_audit_aspects.id_aspect', 'c_aspects.id_aspect')
            ->join('c_matters', 'c_matters.id_matter', 'r_audit_aspects.id_matter')
            ->join('c_states', 'c_states.id_state', 'r_audit_aspects.id_state')
            ->join('c_application_types', 'c_application_types.id_application_type', 'r_audit_aspects.id_application_type')
            ->whereIn('r_audit_aspects.id_audit_matter', $idAuditMatters);
        if ($idAuditMatter != 0) {
            $query->where('r_audit_aspects.id_audit_matter', $idAuditMatter);
        }
        $query->orderBy('c_matters.order', 'ASC')->orderBy('c_aspects.order', 'ASC');
        // Add filters
        if ($filterIdStatus != 0) {
            $query->where('r_audit_aspects.id_status', $filterIdStatus);
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
                $columnSwitch = 'c_status.status';
                break;
            default:
                $columnSwitch = 'c_application_types.application_type';
                break;
        }
        $column = $columnSwitch;
        $dir = (isset($order[0]['dir']) ? $order[0]['dir'] : 'desc');
        $query->orderBy($column, $dir);

        /*Search*/
        $query->where(function ($query) use ($search) {
            $query->where('c_aspects.aspect', 'LIKE', '%' . $search['value'] . '%')
                ->orWhere('c_status.status', 'LIKE', '%' . $search['value'] . '%');
        });

        $queryCount = $query->get();
        $result = $query->limit($rows)->offset($page)->get()->toArray();
        $total = $queryCount->count();
        $data['data'] = (sizeof($result) > 0) ? $result : array();
        $data['recordsTotal'] = $total;
        $data['draw'] = (int) $draw;
        $data['recordsFiltered'] = $total;
        return $data;
    }
    /**
     * update quiz by apsect
     */
    public function scopeUpdateStatusAspectQuiz($query, $idAuditAspect, $idStatus)
    {
        try {
            $model = AuditAspectsModel::find($idAuditAspect);
            try {
                $model->id_status = $idStatus;
                $model->save();
                $data['status'] = StatusConstants::SUCCESS;
                $data['id_aspect'] = $model->id_aspect;
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
    public function scopeSetFinishedAspects($query, $idAuditProcess)
    {
        $model = AuditAspectsModel::where([
            ['r_audit_aspects.id_audit_processes', $idAuditProcess],
            ['r_audit_aspects.id_status', StatusConstants::AUDITED]
        ]);
        try {
            $model->update(['r_audit_aspects.id_status' => StatusConstants::FINISHED_AUDIT]);
            return StatusConstants::SUCCESS;
        } catch (Exception $e) {
            return StatusConstants::ERROR;
        }
    }
    /**
     * Set audit aspects
     */
    public function scopeSetAuditAspects($query, $aspect, $idAuditProcess, $idAuditMatter)
    {
        $model = new AuditAspectsModel();
        $model->self_audit = $aspect['self_audit'];
        $model->id_audit_matter = $idAuditMatter;
        $model->id_contract = $aspect['id_contract'];
        $model->id_matter =  $aspect['id_matter'];
        $model->id_aspect =  $aspect['id_aspect'];
        $model->id_application_type = $aspect['id_application_type'];
        $model->id_state = $aspect['id_state'];
        $model->id_audit_processes = $idAuditProcess;
        $model->id_status = StatusConstants::NOT_AUDITED;
        try {
            $model->save();
            $data['idAuditAspect'] = $model->id_audit_aspect;
            $data['status'] = StatusConstants::SUCCESS;
            return $data;
        } catch (Exception $e) {
            $data['status'] = StatusConstants::ERROR;
            return $data;
        }
    }
    /**
     * Delete audit asepct
     */
    public function scopeDeleteAuditAspect($query, $idAuditAspect)
    {
        try {
            $model = AuditAspectsModel::findOrFail($idAuditAspect);
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
     * Count aspects by status
     */
    public function scopeCountAspectsByStataus($query, $idAuditMatter, $idStatus)
    {
        $count = AuditAspectsModel::where([
            ['id_audit_matter', $idAuditMatter],
            ['id_status', $idStatus]
        ])->count();
        return $count;
    }
    /**
     * Get audited aspects 
     */
    public function scopeGetAuditedAspects($query, $idAuditProcess)
    {
        $query->where([
            ['r_audit_aspects.id_audit_processes', $idAuditProcess],
            ['r_audit_aspects.id_status', StatusConstants::AUDITED]
        ]);
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Get audited aspects 
     */
    public function scopeGetAuditedAspectsByMatter($query, $idAuditMatter, $idStatus)
    {
        $query->join('c_aspects', 'r_audit_aspects.id_aspect', 'c_aspects.id_aspect')
            ->where([
                ['r_audit_aspects.id_audit_matter', $idAuditMatter],
                ['r_audit_aspects.id_status', $idStatus]
            ])->orderBy('c_aspects.order', 'ASC');
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * update total percent
     */
    public function scopeUpdatePercent($query, $idAuditAspect, $percent)
    {
        try {
            $model = AuditAspectsModel::find($idAuditAspect);
            try {
                $model->total = $percent;
                $model->save();
                $data['status'] = StatusConstants::SUCCESS;
                $data['idAuditMatter'] = $model->id_audit_matter;
                return $data;
            } catch (Exception $e) {
                $data['status'] = StatusConstants::ERROR;
                return $data;
            }
        } catch (ModelNotFoundException $th) {
            $data['status'] = StatusConstants::WARNING;
            return $data;
        }
    }
    /**
     * Get data aspect for subrequiremnt in send to action plan
     */
    public function scopeGetAspectAuditAP($query, $idAspect, $idAuditProcess)
    {
        $query->where([
            ['r_audit_aspects.id_audit_processes', $idAuditProcess],
            ['r_audit_aspects.id_aspect', $idAspect]
        ]);
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Get audited aspects by status
     */
    public function scopeGetAuditedAspectsByMatterStatus($query, $idAuditMatter, $idStatusArray)
    {
        $query->join('c_aspects', 'r_audit_aspects.id_aspect', 'c_aspects.id_aspect')
            ->where('r_audit_aspects.id_audit_matter', $idAuditMatter)
            ->whereIn('r_audit_aspects.id_status', $idStatusArray);
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Get audited aspects by idAuditMatter
     */
    public function scopeGetAspectsAuditByMatter($query, $idAuditMatter)
    {
        $query->join('c_aspects', 'r_audit_aspects.id_aspect', 'c_aspects.id_aspect')
            ->where('r_audit_aspects.id_audit_matter', $idAuditMatter);
        $data = $query->get()->toArray();
        return $data;
    }

    /**
     * @param $query
     * @param $idAuditProcesses
     * @return mixed
     */
    public function scopeGetAspectsNameByAuditProcesses($query, $idAuditProcesses)
    {
        $query->select([
            'r_audit_aspects.id_aspect',
            'c_aspects.aspect'
        ])
            ->join('c_aspects', 'c_aspects.id_aspect', 'r_audit_aspects.id_aspect')
            ->where('r_audit_aspects.id_audit_processes', $idAuditProcesses);

        return $query->get();
    }

    /**
     * @param $query
     * @param $idAuditProcesses
     * @return mixed
     */
    public function scopeGetAspectsByAuditProcesses($query, $idAuditProcesses)
    {
        $query->select("r_audit_aspects.*")
            ->where('r_audit_aspects.id_audit_processes', $idAuditProcesses)
            ->orderBy('r_audit_aspects.id_matter', 'asc')
            ->orderBy('r_audit_aspects.id_aspect', 'asc');

        return $query->get();
    }
}
