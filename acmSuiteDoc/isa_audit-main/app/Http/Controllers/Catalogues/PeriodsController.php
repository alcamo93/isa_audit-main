<?php

namespace App\Http\Controllers\Catalogues;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use App\Models\Catalogues\PeriodModel;
use App\Classes\StatusConstants;

class PeriodsController extends Controller
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
        return view('catalogs.periods.periods');
    }

    public function getPeriodsDT(Request $request){
        $page = $request->input('start');
        $rows = $request->input('length');
        $search = $request->input('search');
        $draw = $request->input('draw');
        $order = $request->input('order');
        $filterName = $request->input('filterName');
        $data = PeriodModel::getPeriodsDT($page, $rows, $search, $draw, $order, $filterName);
        return response($data);
    }

    public function setPeriod(Request $request) {
        $period = $request->input('period');
        $lastDay = $request->input('lastDay');
        $lastMonth = $request->input('lastMonth');
        $lastYear = $request->input('lastYear');
        $lastRealDay = $request->input('lastRealDay');
        $lastRealMonth = $request->input('lastRealMonth');
        $lastRealYear = $request->input('lastRealYear');
        $set = PeriodModel::setPeriod($period, $lastDay, $lastMonth, $lastYear, $lastRealDay, $lastRealMonth, $lastRealYear );
        switch ($set) {
            case StatusConstants::SUCCESS:
                $status = StatusConstants::SUCCESS;
                $msg = 'Datos registrados';
                break;
            case StatusConstants::ERROR:
                $status = StatusConstants::ERROR;
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

    public function updatePeriod(Request $request) {
        $idPeriod = $request->input('idPeriod');
        $period = $request->input('period');
        $lastDay = $request->input('lastDay');
        $lastMonth = $request->input('lastMonth');
        $lastYear = $request->input('lastYear');
        $lastRealDay = $request->input('lastRealDay');
        $lastRealMonth = $request->input('lastRealMonth');
        $lastRealYear = $request->input('lastRealYear');
        $update = PeriodModel::updatePeriod($idPeriod, $period, $lastDay, $lastMonth, $lastYear, $lastRealDay, $lastRealMonth, $lastRealYear );
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

    public function deletePeriod(Request $request) {
        $idPeriod = $request->input('id_period');
        $delete = PeriodModel::deletePeriod($idPeriod);
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