<?php

namespace App\Http\Controllers\Catalogues;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use App\Models\Catalogues\AspectsModel;
use App\Classes\StatusConstants;
use Throwable;

class AspectsController extends Controller
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
     * Get aspects by matter
     */
    public function getAspectsByIdMatter(Request $request)
    {
        $idMatter = $request->input('idMatter');
        $data = AspectsModel::getAspectsByMatter($idMatter);
        return $data;
    }
    /**
     * get aspects for data table
     */
    public function getAspectsDT(Request $request)
    {
        $page = $request->input('start');
        $rows = $request->input('length');
        $search = $request->input('search');
        $draw = $request->input('draw');
        $order = $request->input('order');
        $filterName = $request->input('filterName');
        $idMatter = $request->input('idMatter');
        if($idMatter) $data = AspectsModel::getAspectsDT($page, $rows, $search, $draw, $order, $filterName, $idMatter);
        else $data = array('data'=> array(), 'recordsTotal'=> 0, 'draw'=> 0, 'recordsFiltered'=> 0); 
        return response($data);
    }
    /**
     * Set aspect
     */
    public function setAspect(Request $request) {
        $idMatter = $request->input('idMatter');
        $aspect = $request->input('aspect');
        $order = $request->input('order');
        $set = AspectsModel::setAspect($aspect, $order, $idMatter);
        switch ($set) {
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
     *  update aspect
     */
    public function updateAspect(Request $request) {
        $idAspect = $request->input('idAspect');
        $aspect = $request->input('aspect'); 
        $order = $request->input('order');
        $idMatter = $request->input('idMatter');
        $update = AspectsModel::updateAspect($idAspect, $aspect, $order, $idMatter);
        switch ($update) {
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
     * 
     */
    public function deleteAspect(Request $request) {
        $idAspect = $request->input('idAspect');
        $delete = AspectsModel::deleteAspect($idAspect);
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
    public function getAspectsByMatterId($id)
    {
        try {
            $aspects = AspectsModel::where('id_matter', $id)
                ->orderBy('aspect', 'asc')
                ->get(['id_aspect', 'aspect']);

            return response()->json(['aspects' => $aspects], 200);
        } catch(\Throwable $th) {
            throw $th;
        }
    }
} 