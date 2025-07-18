<?php

namespace App\Http\Controllers\Audit;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Classes\StatusConstants;
use App\Classes\ProfilesConstants;
use App\Models\Audit\ObligationsModel;
use App\Models\Audit\ObligationUserModel;
use App\Models\Audit\ActionRegistersModel;
use App\Models\Catalogues\StatusModel;
use App\Models\Catalogues\PeriodModel;
use App\Models\Catalogues\ConditionsModel;
use App\Models\Admin\CustomersModel;
use App\Models\Admin\CorporatesModel;
use App\Models\Admin\ProcessesModel;
use App\Notifications\ObligationsNotification;
use App\Models\Files\FilesModel;
use App\Models\Notifications\RemindersModel;
use App\Classes\Periods;
use Carbon\Carbon;
use App\User;
use App\Models\Requirements\obligationRequirementsModel;
use App\Models\Catalogues\ObligationTypesModel;
use App\Models\Catalogues\CategoryModel;
use App\Traits\FileTrait;

class ObligationsController extends Controller
{
    use FileTrait;
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
     * Main view in audit module
     */
    public function index() {
        $idProfileType = Session::get('profile')['id_profile_type'];
        $arrayData = [];
        $arrayData['today'] = Carbon::now('America/Mexico_City')->toDateString();
        $arrayData['status'] = StatusModel::getStatusByGroup(6);
        $arrayData['periods'] = PeriodModel::GetPeriods();
        $arrayData['conditions'] = ConditionsModel::GetConditions();
        $arrayData['categories'] = CategoryModel::all(['id_category', 'category']);
        $arrayData['idProfileType'] = $idProfileType;
        $arrayData['obligationTypes'] = ObligationTypesModel::GetObligationTypes();
        switch ($idProfileType) {
            case ProfilesConstants::ADMIN_GLOBAL:
                case ProfilesConstants::ADMIN_OPERATIVE:
                $arrayData['idUser'] = 0;
                $arrayData['customers'] = CustomersModel::getAllCustomers();
                $arrayData['corporates'] = [];
                $arrayData['process'] = [];
                $actionReg = ActionRegistersModel::all()->toArray();
                break;
            case ProfilesConstants::CORPORATE:
                $arrayData['idUser'] = 0;
                $idCustomer = Session::get('customer')['id_customer'];
                $arrayData['customers'] = CustomersModel::where('id_customer', $idCustomer)->get();
                $arrayData['corporates'] = CorporatesModel::where('id_customer', $idCustomer)->get();
                $arrayData['process'] = [];
                $actionReg = ActionRegistersModel::with('process')
                    ->where(function ($filter) use ($idCustomer) {
                        $filter->whereHas('process', function($query) use ($idCustomer) {
                            $query->where('id_customer', $idCustomer);
                        });
                    })->get()->toArray();
                break;
            case ProfilesConstants::COORDINATOR:
                $arrayData['idUser'] = 0;  
                $idCustomer = Session::get('customer')['id_customer'];
                $idCorporate = Session::get('corporate')['id_corporate'];
                $arrayData['customers'] = CustomersModel::where('id_customer', $idCustomer)->get();
                $arrayData['corporates'] = CorporatesModel::where('id_corporate', $idCorporate)->get();
                $arrayData['process'] = ProcessesModel::where('id_corporate', $idCorporate)->get();
                $actionReg = ActionRegistersModel::with('process')
                    ->where(function ($filter) use ($idCorporate) {
                        $filter->whereHas('process', function($query) use ($idCorporate) {
                            $query->where('id_corporate', $idCorporate);
                        });
                    })->get()->toArray();
                break;
            case ProfilesConstants::OPERATIVE:
                $arrayData['idUser'] = Session::get('user')['id_user'];
                $idCustomer = Session::get('customer')['id_customer'];
                $idCorporate = Session::get('corporate')['id_corporate'];
                $arrayData['customers'] = CustomersModel::where('id_customer', $idCustomer)->get();
                $arrayData['corporates'] = CorporatesModel::where('id_corporate', $idCorporate)->get();
                $arrayData['process'] = ProcessesModel::with('auditors')->where('id_corporate', $idCorporate)
                    ->where(function ($filter) use ($arrayData) {
                        $filter->whereHas('auditors', function($query) use ($arrayData) {
                            $query->where('id_user', $arrayData['idUser']);
                        });
                    })->get();
                $actionReg = ActionRegistersModel::with('process.auditors')
                    ->where(function ($filter) use ($idCorporate, $arrayData) {
                        $filter->whereHas('process', function($query) use ($idCorporate) {
                            $query->where('id_corporate', $idCorporate);
                        });
                        $filter->whereHas('process.auditors', function($query) use ($arrayData) {
                            $query->where('id_user', $arrayData['idUser']);
                        });
                    })->get()->toArray();
            break;
        }
        $view = ($actionReg == NULL) ? 'errors.Contenido' : 'obligations.view_main';
        return view($view, $arrayData);
    }

    /**
     * Datatables corporates registers to audit
     */
    public function obligationsDT(Request $request){
        $dataRequest = $request->all();
        $relationship = ['type', 'status', 'period', 'users.user.person', 'process.auditors', 'file'];
        $obligations = ObligationsModel::with($relationship);
        $obligations->addSelect([
            'count_obligaction_requirement' => obligationRequirementsModel::select(DB::raw('COUNT(id_obligation_requirement)'))
            ->whereColumn('id_obligation', 'r_obligation_requirements.id_obligation')
        ]);
        // filter
        if ($dataRequest['filterObligation'] != '') $obligations->where('title', 'LIKE', '%'.$dataRequest['filterObligation'].'%');
        if ($dataRequest['filterStatus'] != 0) $obligations->where('id_status', $dataRequest['filterStatus']);
        if ($dataRequest['filterPeriod'] != 0) $obligations->where('id_period', $dataRequest['filterPeriod']);
        if ($dataRequest['filterIdCondition'] != 0) $obligations->where('id_condition', $dataRequest['filterIdCondition']);
        // only for user
        if ($dataRequest['idUser'] != 0) {
            $obligations->where(function($filter) use ($dataRequest) {
                $filter->whereHas('process.auditors', function($query) use ($dataRequest) {
                    $query->where('id_user', $dataRequest['idUser']);
                });
            });
        }
        // filter relationship
        $obligations->where(function($filter) use ($dataRequest) {
            if ($dataRequest['filterIdCustomer'] != 0) {
                $filter->whereHas('process', function($query) use ($dataRequest) {
                    $query->where('id_customer', $dataRequest['filterIdCustomer']);
                });
            }
            if ($dataRequest['filterIdCorporate'] != 0) {
                $filter->whereHas('process', function($query) use ($dataRequest) {
                    $query->where('id_corporate', $dataRequest['filterIdCorporate']);
                });
            }
            if ($dataRequest['filterIdAuditProcess'] != 0) {
                $filter->whereHas('process', function($query) use ($dataRequest) {
                    $query->where('id_audit_processes', $dataRequest['filterIdAuditProcess']);
                });
            }
        });
        // Order main
        $dir = (isset($dataRequest['order'][0]['dir']) ? $dataRequest['order'][0]['dir'] : 'desc');            
        $obligations->orderBy('title', $dir);
        // Paginate
        $totalRecords = $obligations->count('id_obligation');
        $paginate = $obligations->skip($dataRequest['start'])->take($dataRequest['length'])->get();
        $data['data'] = $paginate;
        $data['recordsTotal'] = $totalRecords;
        $data['draw'] = intval($dataRequest['draw']);
        $data['recordsFiltered'] = $totalRecords;
        return response($data);
    }
    /**
     * Get data obligation
     */
    public function getDataObligation(Request $request){
        $idObligation = $request->input('idObligation');
        $relationship = ['type', 'status', 'period', 'users.user.person', 'process', 'file.category'];
        $data = ObligationsModel::with($relationship)->where('id_obligation', $idObligation)->first();
        return response($data);
    }
    /**
     * get users by process
     */
    public function getUsersByProcess(Request $request) {
        $idAuditProcess = $request->input('idAuditProcess');
        $infoAR = ProcessesModel::where('id_audit_processes', $idAuditProcess)->first();
        $users = User::with('person')
            ->where('id_corporate', $infoAR->id_corporate)->get();
        return response()->json($users);
    }
    /**
     * Set user
     */
    public function setUsersAssigned(Request $request){
        $users = $request->input('users');
        DB::beginTransaction();
        foreach ($users as $u) {
            if ($u['id_obligation_user'] == null) {
                $set = ObligationUserModel::SetUser($u);
                if ($set != StatusConstants::SUCCESS) {
                    DB::rollBack();
                    $data['status'] = StatusConstants::ERROR;
                    $data['msg'] = 'No se pudo establecer datos de un usuario';
                    return response($data);
                }
            }
            else {
                $update = ObligationUserModel::UpdateUser($u);
                if ($update != StatusConstants::SUCCESS) {
                    DB::rollBack();
                    $data['status'] = StatusConstants::ERROR;
                    $data['msg'] = 'No se pudo actualizar datos de un usuario';
                    return response($data);
                }
            }
        }
        DB::commit();
        $data['status'] = StatusConstants::SUCCESS;
        $data['msg'] = 'Registro de resposables exitoso';
        return response($data);
    }
    /**
     * Remove user 
     */
    public function removeUserAssigned(Request $request){
        $idObligationUser = $request->input('idObligationUser');
        DB::beginTransaction();
        $remove = ObligationUserModel::RemoveUser($idObligationUser);
        if ($remove != StatusConstants::SUCCESS) {
            DB::rollBack();
            $data['status'] = StatusConstants::ERROR;
            $data['msg'] = 'No se pudo remover al usuario';
            return response($data);
        }
        DB::commit();
        $data['status'] = StatusConstants::SUCCESS;
        $data['msg'] = 'Resposable removido exitosamente';
        return response($data);
    }
    /**
     * Set obligation 
     */
    public function setObligation(Request $request) {
        $dataRequest = $request->all();
        $action = ActionRegistersModel::where('id_audit_processes', $dataRequest['idAuditProcess'])
            ->first();
        if ( is_null($action) ) {
            $data['status'] = StatusConstants::WARNING;
            $data['msg'] = 'Esta auditoría aún no es Finalizada';
            return response($data);
        }
        $set = ObligationsModel::SetObligation($dataRequest, $action->id_action_register);
        if ($set != StatusConstants::SUCCESS) {
            $data['status'] = StatusConstants::ERROR;
            $data['msg'] = 'Algo salio mal, intente nuevamente';
            return response($data);
        }
        $data['status'] = StatusConstants::SUCCESS;
        $data['msg'] = 'Registro exitoso';
        return response($data);
    }
    /**
     * Update obligation 
     */
    public function updateObligation(Request $request) {
        $dataRequest = $request->all();
        $action = ActionRegistersModel::where('id_audit_processes', $dataRequest['idAuditProcess'])
            ->first();
        if ( is_null($action) ) {
            $data['status'] = StatusConstants::WARNING;
            $data['msg'] = 'Esta auditoría aún no es Finalizada';
            return response($data);
        }
        $set = ObligationsModel::UpdateObligation($dataRequest, $action->id_action_register);
        if ($set != StatusConstants::SUCCESS) {
            $data['status'] = StatusConstants::ERROR;
            $data['msg'] = 'Algo salio mal, intente nuevamente';
            return response($data);
        }
        $data['status'] = StatusConstants::SUCCESS;
        $data['msg'] = 'Registro actualizado';
        return response($data);
    }
    /**
     * Delete obligation
     */
    public function deleteObligation(Request $request) {
        $idObligation = $request->input('idObligation');
        try {
            DB::beginTransaction();
            // delete record
            $obligation = ObligationsModel::with(['file'])->findOrFail($idObligation);
            $obligation->delete();
            $idFile = is_null($obligation->file) ? null : $obligation->file->id_file;
            // Delete File
            if ( !is_null($idFile) ) {
                $deleteFile = $this->deleteFile($idFile);
            } else $deleteFile = true;
            if ($deleteFile) {
                DB::commit();
                $data['status'] = StatusConstants::SUCCESS;
                $data['msg'] = 'Obliación eliminada';
                return response($data);
            }
            else {
                DB::rollBack();
                $data['status'] = StatusConstants::ERROR;
                $data['msg'] = 'Algo salio mal, no sé puede eliminar la evidencia';
                return response($data);
            }
        } catch (\Throwable $th) {
            $data['status'] = StatusConstants::ERROR;
            $data['msg'] = 'Algo salio mal, intente nuevamente';
            return response($data);   
        }
    }
    /**
     * Calculate limits based on init date
     */
    public function calculateDates(Request $request){
        $initDate = $request->input('initDate');
        $idPeriod = $request->input('idPeriod');
        $object = new Periods();
        $dates = $object->createDates($initDate, $idPeriod);
        $data['initDate'] = $dates['initDate'];
        $data['renewalDate'] = $dates['closeDate'];
        $data['realInitDate'] = $dates['realInitDate'];
        $data['lastRenewalDate'] = $dates['realCloseDate'];   
        return response($data);
    }
    /**
     * Set dates
     */
    public function setDatesObligation(Request $request){
        $idObligation = $request->input('idObligation');
        $initDate = $request->input('initDate');
        $renewalDate = $request->input('renewalDate');
        try {
            ObligationsModel::where('id_obligation', $idObligation)
                ->update([
                    'init_date' => $initDate,
                    'renewal_date' => $renewalDate
                ]);
            $data['status'] = StatusConstants::SUCCESS;
            $data['msg'] = 'Fechas Registradas';
            return response($data);
        } catch (\Throwable $th) {
            $data['status'] = StatusConstants::ERROR;
            $data['msg'] = 'Algo salio mal, intente nuevamente';
            return response($data);   
        }
        return response($data);
    }

    /**
     * Change status task to Aproved 
     */
    public function completeobligation(Request $request) {
        $idObligation = $request->input('idObligation');
        $status = $request->input('status');
        $idStatus = ($status == 'true') ? ObligationsModel::APPROVED : ObligationsModel::REJECTED;
        try {
            $updateStatusTask = ObligationsModel::find($idObligation)->update(['id_status' => $idStatus]);
        } catch (\Throwable $th) {
            $data['status'] = StatusConstants::WARNING;
            $data['msg'] = 'No se puede actualizar el estatus de la obligación';
            return response($data);
        }
        $data['status'] = StatusConstants::SUCCESS;
        $msg = ($status == 'true') ? 'completada' : 'rechazada';
        $data['msg'] = 'Obligación '.$msg;
        return response($data);
    }
    /**
     * get action registers by corporate for data table
     */
    public function getActionRegistersByContractDT(Request $request) {
        $dataRequest = $request->all();
        $data = ActionRegistersModel::GetActionRegisterByIdCorporateDT($dataRequest);
        return response($data);
    }
    /**
     * Get data obligation requirements
     */
    public function getObligationsRequirements(Request $request){
        $idObligation = $request->input('idObligation');
        $response['data_requirements'] = obligationRequirementsModel::GetDataobligationRequirement($idObligation);
        return response()->json($response);
        
    }
}