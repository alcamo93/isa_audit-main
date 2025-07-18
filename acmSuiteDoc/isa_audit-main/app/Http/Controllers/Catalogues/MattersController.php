<?php

namespace App\Http\Controllers\Catalogues;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use App\Models\Catalogues\AspectsModel;
use App\Models\Catalogues\MattersModel;
use App\Classes\StatusConstants;
use Throwable;

class MattersController extends Controller
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
     * Main view in matters catalogue module
     */
    public function index(){
        return view('catalogs.matters.legal_matters');
    }
    /**
     *  Get matters for data table
     */
    public function getMattersDT(Request $request){
        $page = $request->input('start');
        $rows = $request->input('length');
        $search = $request->input('search');
        $draw = $request->input('draw');
        $order = $request->input('order');
        $filterName = $request->input('filterName');
        $data = MattersModel::getMattersDT($page, $rows, $search, $draw, $order, $filterName);
        return response($data);
    }
    /**
     * Set matter
     */
    public function setMatter(Request $request) {
        $matter = $request->input('matter');
        $desc = $request->input('desc');
        $set = MattersModel::setMatter($matter, $desc);
        switch ($set) {
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
    /**
     *  update matter
     */
    public function updateMatter(Request $request) {
        $matter = $request->input('matter');
        $desc = $request->input('desc');
        $idMatter = $request->input('idMatter');
        $update = MattersModel::updateMatter($idMatter, $matter, $desc);
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
    /**
     * 
     */
    public function deleteMatter(Request $request) {
        $idMatter = $request->input('idMatter');
        $delete = MattersModel::deleteMatter($idMatter);
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

    /* Versión 2 */
    public function getMatters()
    {
        try {
            $matters = MattersModel::orderBy('matter', 'asc')->get(['id_matter', 'matter']);
            return response()->json(['matters' => $matters], 200);
        } catch(\Throwable $th) {
            throw $th;
        }
    }
} 