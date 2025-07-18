<?php

namespace App\Http\Controllers\News;


use App\Classes\StatusConstants;
use App\Http\Controllers\Controller;
use App\Models\News\NewsModel;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\Admin\ProfilesModel;
use App\Models\Catalogues\StatusModel;
use App\Models\Catalogues\ProfileTypeModel;
use App\Models\Admin\CustomersModel;
use App\Models\Admin\CorporatesModel;
use App\Models\Admin\ProfilesPermissionModel;

class NewsController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
        $groupStatus = 1; // group status basic
        $data['today'] = Carbon::now('America/Mexico_City')->toDateString();
        $data['status'] = StatusModel::where('group', $groupStatus)->get()->toArray();
        switch (Session::get('profile')['id_profile_type']){
            case 1: case 2:
                $data['profilesType'] = ProfileTypeModel::getProfilesTypes();
                $data['customers'] = CustomersModel::getAllCustomers();
                $data['corporates'] = CorporatesModel::getAllCorporatesDT();
                break;
            case 3:
                $data['profilesType'] = ProfileTypeModel::getProfilesTypes(3);// profile_level 3 customer admin
                $data['customers'] = CustomersModel::getCustomer(Session::get('customer')['id_customer']);
                $data['corporates'] = CorporatesModel::getAllCorporates(Session::get('customer')['id_customer']);
                $data['idCustomer'] = Session::get('customer')['id_customer'];
                $data['idCorporate'] = Session::get('corporate')['id_corporate'];
                break;
            case 4: case 5:
                $data['profilesType'] = ProfileTypeModel::getProfilesTypes(4);// profile_level 4 corporative
                $data['customers'] = CustomersModel::getCustomer(Session::get('customer')['id_customer']);
                $data['corporates'] = CorporatesModel::getAllCorporates(Session::get('customer')['id_customer']);
                $data['idCustomer'] = Session::get('customer')['id_customer'];
                $data['idCorporate'] = Session::get('corporate')['id_corporate'];
                break;
        }
        return view('news.news', $data);
    }

    public function getListNews(Request $request){
        $orderParameter = $request->input('order');
        $orderColumn = $orderParameter[0]['column'];
        $orderDir = $orderParameter[0]['dir'];
        $page = $request->input('start');
        $rows = $request->input('length');
        $search = $request->input('search');
        $draw = $request->input('draw');
        
        $fIdCustomer = $request->input('fIdCustomer');
        $fIdCorporate = $request->input('fIdCorporate');
        $dataNews = NewsModel::GetListNews($page, $rows, $search, $draw, $orderColumn, $orderDir, $fIdCustomer, $fIdCorporate, Session::get('profile')['id_profile_type']);

        $data_news = [];
        for ($i = 0; $i < sizeof($dataNews['data']); $i++) {
            $newsData = array();
            $newsData['new'] = $dataNews['data'][$i];
            $data_news[] = $newsData;
        }
        $dataNews['data'] = $data_news;
        return response($dataNews);
    }

    public function base64ToImage($base64_string, $output_file) {
        $file = fopen($output_file, "wb");
        $data = explode(',', $base64_string);
        fwrite($file, base64_decode($data[1]));
        fclose($file);
        return $file;
    }

    public function saveNews(Request $request){

        $data = array();
        $data['idNew'] = $request->input('id_new', 0);
        $data['title'] = $request->input('title', 0);
        $data['start_date'] = $request->input('start_date', 0);
        $data['clear_date'] = $request->input('clear_date', 0);
        $data['description'] = $request->input('description', "");
        $data['user'] = 1;
        $data['status'] = $request->input('id_status', 1);
        $data['postImage'] = $request->input('postImage');
        $data['postTitle'] =  $request->input('postTitle');
        $data['postContent'] =  $request->input('postContent');
        $data['idCorporate'] = Session::get('profile')['id_corporate'];
        $data['idCustomer'] = Session::get('profile')['id_customer'];
        $imgNew = $request->file("imgNew");
        //$imgNew = $this->base64ToImage($imgNews, 'image.jpg');
        //$imgNew =  file_get_contents($imgNews);
        /*$data = explode(',', $imgNews);
        $img = $data[1];
        $imgNew = base64_decode($img);*/
        //dd($imgNew);
        if (trim($data['title']) == "") {
            return response()->json(array(
                'status' => 'error',
                "message" => "El titulo de la noticia es obligatorio"));
        }
        if (!is_numeric($data['user']) || ((int)$data['user']) < 1) {
            return response()->json(array(
                'status' => 'error',
                "message" => "El id del usuario es obligatorio"));
        }
        if (trim($data['description']) == "") {
            return response()->json(array(
                'status' => 'error',
                "message" => "El contenido de la noticia es obligatorio"));
        }

        if (trim($data['clear_date']) == "") {
            return response()->json(array(
                'status' => 'error',
                "message" => "La fecha de término es invalida"));
        }
        $result = NewsModel::SaveNew($data);
        if ($result['status']) {

            $nameImg = "new_" . $result['idNew'] . "." . $imgNew->getClientOriginalExtension();

            if (!file_exists(public_path() . '/assets/img/news'))
                mkdir(public_path() . '/assets/img/news');

            if (file_exists(public_path() . '/assets/img/news/' . $nameImg))
                unlink(public_path() . '/assets/img/news/' . $nameImg);
                $imgNew->move(public_path() . '/assets/img/news/', $nameImg);


            return response()->json(array(
                'status' => 'success',
                'message' => 'Registro realizado con éxito',
                'id_order' => $result['idNew']));
        } else {
            return response()->json(array(
                'status' => 'error',
                'message' => 'No se pudo realizar el registro, inténtelo en otro momento.'));
        }
    }

    public function getNew(Request $request, $idNew){
        try {
            $data['data'] = NewsModel::findOrFail($idNew);
            $status = StatusConstants::SUCCESS;
            $msg = 'Mostrar registro';
        } catch (\Throwable $th) {
            $status = StatusConstants::ERROR;
            $msg = 'El registro no fue encontrado, verifique nuevamente';
        }
        $data['status'] = $status;
        $data['msg'] = $msg;
        return response($data);
    }

    public function updateNews(Request $request){
        $data = $request->all();
        $updateNew = NewsModel::UpdateNew($data);
        switch ($updateNew) {
            case StatusConstants::SUCCESS:
                if($data['imgNew']!= "null") {
                    $nameImg = "new_" . $data['id_new'] . "." . $data['imgNew']->getClientOriginalExtension();
                    if (!file_exists(public_path() . '/assets/img/news'))
                        mkdir(public_path() . '/assets/img/news');

                    if (file_exists(public_path() . '/assets/img/news/' . $nameImg))
                        unlink(public_path() . '/assets/img/news/' . $nameImg);
                    $data['imgNew']->move(public_path() . '/assets/img/news/', $nameImg);
                }
                $status = StatusConstants::SUCCESS;
                $msg = 'Registro actualizado';
                break;
            case StatusConstants::WARNING:
                $status = StatusConstants::WARNING;
                $msg = 'Algo salio mal, intente nuevamente';
                break;
            case StatusConstants::ERROR:
                $status = StatusConstants::ERROR;
                $msg = 'El registro no fue encontrado, verifique';
                break;
            default:
                # code...
                break;
        }
        $data['status'] = $status;
        $data['msg'] = $msg;
        return $data;
    }

    public function deleteNews(Request $request){
        $idNew = $request->input('idNew');
        $model = NewsModel::DeleteNew($idNew);
        switch ($model) {
            case StatusConstants::SUCCESS:
                if (file_exists(public_path() . '/assets/img/news/new_'.$idNew.'jpg'))
                    unlink(public_path() . '/assets/img/news/new_'.$idNew.'jpg');
                $status = StatusConstants::SUCCESS;
                $msg = 'El registro ha sido eliminado exitosamente!';
                break;
            case StatusConstants::WARNING:
                $status = StatusConstants::WARNING;
                $msg = 'Imposible eliminar, otros elementos dependen de este.';
                break;
            case StatusConstants::ERROR:
                $status = StatusConstants::ERROR;
                $msg = 'Imposible eliminar, esta noticia.';
                break;
            default:
                # code...
                break;
        }
        $data['status'] = $status;
        $data['msg'] = $msg;
        return $data;
    }
}
