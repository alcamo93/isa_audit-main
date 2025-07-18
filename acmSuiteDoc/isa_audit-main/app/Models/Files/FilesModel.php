<?php

namespace App\Models\Files;

use Illuminate\Database\Eloquent\Model;
use App\Classes\StatusConstants;
use Illuminate\Support\Facades\DB;

class FilesModel extends Model
{
    protected $table = 't_files_den';
    protected $primaryKey = 'id_file';

    public function process() {
        return $this->belongsTo('App\Models\Admin\ProcessesModel', 'id_audit_processes', 'id_audit_processes');
    }
    
    public function task() {
        return $this->belongsTo('App\Models\Audit\TasksModel', 'id_task', 'id_task');
    }

    public function obligation() {
        return $this->belongsTo('App\Models\Audit\ObligationsModel', 'id_obligation', 'id_obligation');
    }

    public function source() {
        return $this->hasOne('App\Models\Catalogues\SourceModel', 'id_source', 'id_source');
    }

    public function category() {
        return $this->hasOne('App\Models\Catalogues\CategoryModel', 'id_category', 'id_category');
    }

    public function customer() {
        return $this->belongsTo('App\Models\Admin\CustomersModel', 'id_customer', 'id_customer');
    }

    public function corporate() {
        return $this->belongsTo('App\Models\Admin\CorporatesModel', 'id_corporate', 'id_corporate');
    }
    /**
    * Return aspects
    */
    public function scopeGetAspects($query){ 
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Get aspects for data table
     */
    public function scopeGetFilesDT($query, $page, $rows, $search, $draw, $order, $filterTitle, $idComment, $idActionPlan, $idObligation, $idTask){
        $where = [
            ['id_comment', $idComment],
            ['id_action_plan', $idActionPlan],
            ['id_obligation', $idObligation],
            ['id_task', $idTask]
        ];
        if ($filterTitle != null) array_push($where, ['t_files_den.title','LIKE','%'.$filterTitle.'%']);
        if ($search['value'] != null) array_push($where, ['t_files_den.title','LIKE','%'.$search['value'].'%']);
        $query->orWhere($where);
        //Order by
        $query->orderBy('title', 'ASC');

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
    * Set Aspect from add catalogs module
    */
    public function scopeSetFile($query, $info, $idUser, $url, $fileSize, $fileType) {
        if ($info['idFile'] != 'null') {
            $model = FilesModel::findOrFail($info['idFile']);
        }
        else {
            $model = new FilesModel();
            $model->id_action_plan = $info['idActionPlan'];
            $model->id_obligation = $info['idObligation'];
            $model->id_task = $info['idTask'];
            $model->id_audit_processes = $info['idAuditProcesses'];
            $model->id_source = $info['idSource'];
            $model->id_customer = $info['idCustomer'];
            $model->id_corporate = $info['idCorporate'];
        }
        $model->id_category = $info['idCategory'];
        $model->title = $info['title'];
        $model->url = $url;
        $model->file_size = $fileSize;
        $model->file_type = $fileType;
        $model->id_user = $idUser;
        try {
            $model->save();
            return StatusConstants::SUCCESS;
        } catch (\Exception $e) {
            if ($e->errorInfo[1] == 1062) {
                return StatusConstants::DUPLICATE;
            }else{
                return StatusConstants::ERROR;
            }
        }
    }

    /**
     * delete aspect from add catalogs module
    */
    public function scopeDeleteFile($query, $idFile){
        $response = StatusConstants::ERROR;
        if(
            $query
            ->where('id_file', $idFile)
            ->delete()
        ) $response = StatusConstants::SUCCESS;
        return $response;
    }

    /**
     * delete files by origin
     */
    public function scopeDeleteFileOrigin($query, $idActionPlan, $idObligation, $idComment, $idTask){
        $where = [
            ['id_comment', $idComment],
            ['id_action_plan', $idActionPlan],
            ['id_obligation', $idObligation],
            ['id_task', $idTask]
        ];
        $model = FilesModel::orWhere($where);
        try {
            $model->delete();
            return StatusConstants::SUCCESS;
        } catch (Exception $e) {
            return StatusConstants::WARNING; 
        }
    }
}
