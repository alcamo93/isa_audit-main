<?php

namespace App\Http\Controllers\Catalogues;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use App\Models\Catalogues\StatusModel;
use App\Models\Catalogues\IndustriesModel;
use App\Models\Catalogues\StatesModel;
use App\Models\Catalogues\CitiesModel;
use App\Classes\StatusConstants;

class IndustriesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Main view in insdustries catalogue module
     */
    public function index(){
        return view('catalogs.industries.industries');
    }

    public function getIndustriesDT(Request $request){
        $page = $request->input('start');
        $rows = $request->input('length');
        $search = $request->input('search');
        $draw = $request->input('draw');
        $order = $request->input('order');
        $filterName = $request->input('filterName');
        $data = IndustriesModel::getIndustriesDT($page, $rows, $search, $draw, $order, $filterName);
        return response($data);
    }

    public function setIndustry(Request $request) {
        $name = $request->input('name');
        $setIndustry = IndustriesModel::setIndustryCT($name);
        switch ($setIndustry) {
            case StatusConstants::SUCCESS:
                $status = StatusConstants::SUCCESS;
                $msg = 'Datos registrados';
                break;
            case StatusConstants::ERROR:
                $status = StatusConstants::WARNING;
                $msg = 'Algo salio mal, intente nuevamente';
                break;
            case StatusConstants::WARNING:
                $status = StatusConstants::WARNING;
                $msg = 'Los datos que desea registrar, ya se encuentran en el sistema';
                break;
            default:
                # code... 
                break;
        }
        $data['status'] = $status;
        $data['msg'] = $msg;
        return $data;
    }

    public function updateIndustry(Request $request) {
        $name = $request->input('name');
        $idIndustry = $request->input('id_industry');
        $update = IndustriesModel::updateIndustryCT($idIndustry, $name);
        switch ($update) {
            case StatusConstants::SUCCESS:
                $status = StatusConstants::SUCCESS;
                $msg = 'Registro actualizado';
                break;
            case StatusConstants::ERROR:
                $status = StatusConstants::WARNING;
                $msg = 'Algo salio mal, intente nuevamente';
                break;
            case StatusConstants::WARNING:
                $status = StatusConstants::WARNING;
                $msg = 'Los datos que desea registrar, ya se encuentran en el sistema';
                break;
            default:
                # code... 
                break;
        }
        $data['status'] = $status;
        $data['msg'] = $msg;
        return $data;
    }

    public function deleteIndustry(Request $request) {
        $idIndustry = $request->input('id_industry');
        $delete = IndustriesModel::deleteIndustryCT($idIndustry);
        switch ($delete) {
            case StatusConstants::SUCCESS:
                $status = StatusConstants::SUCCESS;
                $msg = 'Registro eliminado';
                break;
            case StatusConstants::ERROR:
                $status = StatusConstants::WARNING;
                $msg = 'Algo salio mal, intente nuevamente';
                break;
            case StatusConstants::WARNING:
                $status = StatusConstants::WARNING;
                $msg = 'El registro está en uso, no se puede eliminar';
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