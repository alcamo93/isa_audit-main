<?php

namespace App\Models\News;

use App\Classes\StatusConstants;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class NewsModel extends Model
{
    protected $table = 't_news';
    protected $primaryKey = 'id_new';

    public function scopeGetListNews($query, $page, $rows, $search, $draw, $orderColumn, $orderDir,  $fIdCustomer, $fIdCorporate, $profileLevel){
        DB::statement("SET lc_time_names = 'es_MX'");
        $query->select([
            "t_news.id_new",
            "t_news.title",
            "t_news.description",
            "t_news.id_customer",
            "t_news.id_corporate",
            \DB::raw("DATE_FORMAT(t_news.start_date, \"%d/%M/%Y\")as start_date"),
            \DB::raw("DATE_FORMAT(t_news.clear_date, \"%d/%M/%Y\")as clear_date"),
            \DB::raw('concat(t_people.first_name,\' \',t_people.second_name) as name'),
            "t_news.name_image",
            "c_status.id_status",
            "c_status.status",
            "t_news.id_new AS detail",
            "t_news.id_new AS edit",
            "t_news.id_new AS delete"])
            ->join('t_users', 't_users.id_user', '=', 't_news.id_user')
            ->join('t_corporates', 't_news.id_corporate', 't_corporates.id_corporate')
            ->join('t_people', 't_users.id_person', '=', 't_people.id_person')
            ->join("c_status", "t_news.id_status", "=", "c_status.id_status");

        // Add filters
        $where = [];
        if ($fIdCustomer != 0) array_push($where, ['t_news.id_customer', $fIdCustomer]);
        if ($fIdCorporate != 0) array_push($where, ['t_news.id_corporate', $fIdCorporate]);

        $query->where($where);

        $query->where("t_news.id_status", "!=", 3)
            ->distinct()->orderBy("id_new","DESC");

        /*if($orderColumn == 0)
            $query->orderBy("consecutive", $orderDir);

        if($orderColumn == 4)
            $query->orderBy("start_date", $orderDir);

        if($orderColumn == 5)
            $query->orderBy("clear_date", $orderDir);*/
            

        $queryCount = $query->get();
        $result = $query->limit($rows)->offset($page)->get()->toArray();
        $total = $queryCount->count();
        $data['data'] = (sizeof($result) > 0) ? $result : array();
        $data['recordsTotal'] = $total;
        $data['draw'] = (int)$draw;
        $data['recordsFiltered'] = $total;

        return $data;
    }

    public function scopeSaveNew($querey, $data) {
        $response = array();
        try {
            if ($data['idNew'] == 0) {
                $model = new NewsModel();
            } else {
                $model = NewsModel::find($data['idNew']);
            }
            $model->title = $data['title'];
            $model->description = $data['description'];
            $model->start_date = $data['start_date'].' 00:00:00';
            $model->clear_date = $data['clear_date'].' 23:59:59';
            $model->name_image = 'image.jpg';
            $model->id_user = $data['user'];
            $model->id_status = 1;
            $model->show_image = $data['postImage'];
            $model->show_title = $data['postTitle'];
            $model->show_description = $data['postContent'];
            $model->id_customer = $data['idCustomer'];
            $model->id_corporate = $data['idCorporate'];

            $model->save();

            $response['status'] = true;
            $response['idNew'] = $model->id_new;

        } catch (\Exception $e) {
            $response['status'] = false;
        }

        return $response;
    }

    public function scopeGetNew($query, $idNew){
        $query->select([
            "t_news.id_new",
            "t_news.title",
            "t_news.description",
            "t_news.show_image",
            "t_news.show_title",
            "t_news.show_description",
            \DB::raw("DATE_FORMAT(t_news.start_date, \"%Y-%m-%d\")as start_date"),
            \DB::raw("DATE_FORMAT(t_news.clear_date, \"%Y-%m-%d\")as clear_date")])
            ->where('t_news.id_new', $idNew);
        $data = $query->get()->toArray();
        return $data;
    }

    public function scopeGetTodayNews($query, $idCustomer){
        $query->select([
            "t_news.id_new",
            "t_news.title",
            "t_news.description",
            "t_news.show_image",
            "t_news.show_title",
            "t_news.show_description",
            \DB::raw("DATE_FORMAT(t_news.start_date, \"%Y-%m-%d\")as start_date"),
            \DB::raw("DATE_FORMAT(t_news.clear_date, \"%Y-%m-%d\")as clear_date")])
            ->where('t_news.id_customer', $idCustomer);
        $data = $query->get()->toArray();
        return $data;
    }

    public function scopeUpdateNew($query, $data){
        try {
            $model = NewsModel::findOrFail($data['id_new']);
            $model->title = $data['title'];
            $model->description = $data['description'];
            $model->start_date = $data['start_date'].' 00:00:00';;
            $model->clear_date = $data['clear_date'].' 23:59:59';;
            $model->name_image = 'image.jpg';
            $model->id_user = 1;
            $model->show_image = $data['postImage'];
            $model->show_title = $data['postTitle'];
            $model->show_description = $data['postContent'];
            $model->id_customer = Session::get('profile')['id_customer'];
            $model->id_corporate = Session::get('profile')['id_corporate'];
            try {
                $model->save();
                return StatusConstants::SUCCESS;
            } catch (Exception $e) {
                return StatusConstants::WARNING;
            }
        } catch (ModelNotFoundException $th) {
            return StatusConstants::ERROR;
        }
    }

    public function scopeDeleteNew($query, $idNew){
        $model = NewsModel::findOrFail($idNew);
        try {
            $model->delete();
            return StatusConstants::SUCCESS;
        } catch (Exception $e) {
            return StatusConstants::ERROR;  //on cascade exception
        }
    }

}
