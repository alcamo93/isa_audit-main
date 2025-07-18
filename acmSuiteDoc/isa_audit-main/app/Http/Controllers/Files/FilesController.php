<?php

namespace App\Http\Controllers\Files;

use App\Classes\ProfilesConstants;
use App\Http\Controllers\Controller;
use App\Models\Admin\CorporatesModel;
use App\Models\Admin\ContractsModel;
use App\Models\Admin\CustomersModel;
use App\Models\Admin\ProcessesModel;
use App\Models\Catalogues\CategoryModel;
use App\Models\Catalogues\ScopeModel;
use App\Models\Catalogues\SourceModel;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use App\Models\Files\FilesModel;
use App\Classes\StatusConstants;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Audit\TasksModel;
use App\Models\Audit\ActionPlansModel;
use App\Models\Audit\AuditRegistersModel;
use App\Models\Catalogues\RequirementsModel;
use App\Models\Catalogues\SubrequirementsModel;
use App\Models\Audit\ObligationsModel;
use App\Notifications\ActionPlanNotification;
use App\Notifications\ObligationsNotification;
use App\Classes\Evaluate;
use App\User;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Audit\TasksController;
use App\Traits\FileTrait;
use Illuminate\Support\Arr;
use App\Models\Audit\TaskExpiredModel;
use App\Models\Audit\ActionExpiredModel;
use App\Traits\HelpersActionPlanTrait;
use Carbon\Carbon;

class FilesController extends Controller
{
    use FileTrait, HelpersActionPlanTrait;
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
     * Main view in contracts module
     */
    public function index() {
        // data by view
        $arrayData['idProfileType'] = Session::get('profile')['id_profile_type'];
        switch ($arrayData['idProfileType']) {
            case ProfilesConstants::ADMIN_GLOBAL:
            case ProfilesConstants::ADMIN_OPERATIVE:
                $arrayData['idUser'] = null;
                $arrayCustomers = ContractsModel::GetArrayCustomerActiveContract();
                $arrayData['customers'] = CustomersModel::GetCustomerActiveContract($arrayCustomers);
                $arrayData['categories'] = CategoryModel::all(['id_category', 'category']);
                $arrayData['sources'] = SourceModel::all(['id_source', 'source']);
                $view = 'library.main.view_owner';
            break;
            case ProfilesConstants::CORPORATE:
                $arrayData['idUser'] = null;
                $arrayData['customerTrademark'] = Session::get('customer')['cust_trademark'];
                $arrayData['idCustomer'] = Session::get('customer')['id_customer'];
                $arrayData['corporates'] = CorporatesModel::GetCorporateActiveContract($arrayData['idCustomer']);
                $arrayData['categories'] = CategoryModel::all(['id_category', 'category']);
                $arrayData['sources'] = SourceModel::all(['id_source', 'source']);
                $view = 'library.main.view_corporate';
                break;
            case ProfilesConstants::COORDINATOR:
                $arrayData['idUser'] = null;
                $arrayData['customerTrademark'] = Session::get('customer')['cust_trademark'];
                $arrayData['corporateTrademark'] = Session::get('corporate')['corp_trademark'];
                $arrayData['idCustomer'] = Session::get('customer')['id_customer'];
                $arrayData['idCorporate'] = Session::get('corporate')['id_corporate'];
                $arrayData['corporates'] = CorporatesModel::GetCorporateActiveContract($arrayData['idCustomer']);
                $arrayData['categories'] = CategoryModel::all(['id_category', 'category']);
                $arrayData['sources'] = SourceModel::all(['id_source', 'source']);
                $view = 'library.main.view_coordinator';
                break;
            case ProfilesConstants::OPERATIVE:
                $arrayData['idUser'] = Session::get('user')['id_user'];
                $arrayData['customerTrademark'] = Session::get('customer')['cust_trademark'];
                $arrayData['corporateTrademark'] = Session::get('corporate')['corp_tradename'];
                $arrayData['idCustomer'] = Session::get('customer')['id_customer'];
                $arrayData['idCorporate'] = Session::get('corporate')['id_corporate'];
                $arrayData['corporates'] = CorporatesModel::GetCorporateActiveContract($arrayData['idCustomer']);
                $arrayData['categories'] = CategoryModel::all(['id_category', 'category']);
                $arrayData['sources'] = SourceModel::all(['id_source', 'source']);
                $view = 'library.main.view_coordinator';
                break;
        }
        return view($view, $arrayData);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function getFilesLibrary(Request $request){
        $requestData = $request->all();
        $files = FilesModel::with(['process', 'customer', 'task', 'obligation', 'source', 'category']);
        // filter
        if ($requestData['idCustomer'] != null && intval($requestData['idCustomer']) > 0) {
            $files->where('id_customer', $requestData['idCustomer']);
        }
        if ($requestData['idCorporate'] != null && intval($requestData['idCorporate']) > 0) {
            $files->where('id_corporate', $requestData['idCorporate']);
        }
        if ($requestData['filterTitle'] != null && $requestData['filterTitle'] !== '') {
            $files->where('title', 'LIKE', '%'.$requestData['filterTitle'].'%' );
        }
        if ($requestData['idCategory'] != null && intval($requestData['idCategory']) > 0) {
            $files->where('id_category', $requestData['idCategory']);
        }
        if ($requestData['idSource'] != null && intval($requestData['idSource']) > 0) {
            $files->where('id_source', $requestData['idSource']);
        }
        $files->where(function ($filter) use ($requestData) {
            $filter->whereHas('task', function($query) {
                $query->where('id_status', TasksModel::APPROVED);
            });
            $filter->orDoesntHave('task');
            $filter->whereHas('obligation', function($query) {
                $query->where('id_status', ObligationsModel::APPROVED);
            });
            $filter->orDoesntHave('obligation');
            if ($requestData['origin'] != null && $requestData['origin'] !== ''){
                $filter->whereHas('process', function($query) use ($requestData) {
                    $query->where('audit_processes', 'LIKE', '%'.$requestData['origin'].'%');
                });
            }
        });
        // Order
        $arrayOrder = [
            '0' => 'id_file',
            '1' => 'title'
        ];
        $column = $arrayOrder[$requestData['order'][0]['column']];
        $dir = (isset($requestData['order'][0]['dir']) ? $requestData['order'][0]['dir'] : 'desc');            
        $files->orderBy($column, $dir);
        // Paginate
        $totalRecords = $files->count('id_file');
        $paginate = $files->skip($requestData['start'])->take($requestData['length'])->get();
        $data['data'] = $paginate;
        $data['recordsTotal'] = $totalRecords;
        $data['draw'] = intval($requestData['draw']);
        $data['recordsFiltered'] = $totalRecords;
        return response($data);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function getFilesDT(Request $request)
    {
        $page = $request->input('start');
        $rows = $request->input('length');
        $search = $request->input('search');
        $draw = $request->input('draw');
        $order = $request->input('order');
        $filterTitle = $request->input('filterTitle');
        $idComment = ( $request->input('idComment') == 'null' ) ? null : $request->input('idComment');
        $idActionPlan = ( $request->input('idActionPlan') == 'null' ) ? null : $request->input('idActionPlan');
        $idObligation = ( $request->input('idObligation') == 'null' ) ? null : $request->input('idObligation');
        $idTask = ( $request->input('idTask') == 'null' ) ? null : $request->input('idTask');
        $data = FilesModel::getFilesDT($page, $rows, $search, $draw, $order, $filterTitle, $idComment, $idActionPlan, $idObligation, $idTask);
        return response($data);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function setFile(Request $request) {
        $requestData = $request->all();
        $file = $request->file('file');
        $idUser = Auth::user()->id_user;
        // verify storage
        $fileDestination = null;
        $auxFileName = null;
        $requestData['isLibrary'] = ($requestData['isLibrary'] == 'true') ? true : false;
        if($requestData['isLibrary']) {
            $fileDestination = 'library';
            $auxFileName = 'library';
            $requestData['idActionPlan'] = null;
            $requestData['idTask'] = null;
            $requestData['idObligation'] = null;
            $requestData['idAuditProcesses'] = null;
        }
        else {
            if(($requestData['idActionPlan'] != 'null') && $requestData['idTask'] != 'null') {
                $fileDestination = 'requeriments/action_'.$requestData['idActionPlan'];
                $auxFileName = 'task_'.$requestData['idTask'];
                $requestData['idObligation'] = null;
                $task = TasksModel::find($requestData['idTask']);
                if ($task->id_status == TasksModel::APPROVED) {
                    $data['status'] = StatusConstants::INFO;
                    $data['msg'] = 'Esta tarea esta Aprovada, no se puede modificar la evidencia';
                    return response($data);
                }
            } else if($requestData['idObligation'] != null) {
                $fileDestination = 'obligations';
                $auxFileName = 'obligation_'.$requestData['idObligation'];
                $requestData['idActionPlan'] = null;
                $requestData['idTask'] = null;
            }
        }
        if ($fileDestination == null || $auxFileName == null) {
            $data['status'] = StatusConstants::WARNING;
            $data['msg'] = 'No se puede establecer ubicación del archivo';
            return response($data);
        }
        $filePath = 'customer_'.$requestData['idCustomer'].'/corporate_'.$requestData['idCorporate'].'/'.$fileDestination;
        // Set file in file system
        DB::beginTransaction();
        $disk = $this->getStorageEnviroment('files');
        $fileName = $auxFileName."_".md5(microtime()).".".$file->getClientOriginalExtension();
        $fileSize = $file->getSize();
        $fileType = $file->getMimeType();
        $put = Storage::disk($disk)->putFileAs($filePath, new File($file), $fileName);
        if ( Storage::disk($disk)->missing($put) ) {
            DB::rollBack();
            $data['status'] = StatusConstants::ERROR;
            $data['msg'] = 'Algo salio mal en carga de documento, intente nuevamente';
            return response($data);
        }
        // set register in DB
        $putPath = 'storage/files/'.$put;
        $set = FilesModel::SetFile($requestData, $idUser, $putPath, $fileSize, $fileType);
        if ($set != StatusConstants::SUCCESS) {
            DB::rollBack();
            $msgError = 'Algo salio mal en carga de documento, intente nuevamente';
            $msgDuplicate = 'No se puede subir otro documento al mismo registro';
            $msg = ($set == StatusConstants::DUPLICATE) ? $msgDuplicate : $msgError;
            $status = ($set == StatusConstants::DUPLICATE) ? StatusConstants::WARNING : StatusConstants::ERROR;
            $data['status'] = $status;
            $data['msg'] = $msg;            
            return response($data);
        }
        // detaills by module
        if ($requestData['idActionPlan'] != null && $requestData['idTask'] != null) {
            try {
                $task = TasksModel::findOrFail($requestData['idTask'])
                        ->update(['id_status' => TasksModel::REVIEW]);
                $this->statusActionByTask($requestData['idActionPlan']);
            } catch (\Throwable $th) {
                DB::rollBack();
                $data['status'] = StatusConstants::ERROR;
                $data['msg'] = 'No se pudo actualizar los estatus, intente nuevamente';            
                return response($data);
            }
            // Notification
            $build = $this->buildActionNotify($requestData['idTask'], $requestData['title']);
            $notify = User::findOrFail($build['userToNotify']);
            $notify->notify(new ActionPlanNotification($build['notifyData']));
        } 
        else if ($requestData['idObligation'] != null) {
            try {
                $relationships = [
                    'users.user.person',
                    'process'
                ];
                $obligation = ObligationsModel::with($relationships)->findOrFail($requestData['idObligation']);
                // search user
                if (sizeof($obligation->users) == 0) {
                    DB::rollBack();
                    $data['status'] = StatusConstants::WARNING;
                    $data['msg'] = 'No se pudo registrar la evidencia hasta contar con el Responsable de Seguimiento';            
                    return response($data);
                }
                $today = Carbon::now('America/Mexico_City')->toDateString();
                ObligationsModel::find($requestData['idObligation'])->update([
                    'id_status' => ObligationsModel::REVIEW,
                    'last_renewal_date' =>  $today
                ]);
            } catch (\Throwable $th) {
                DB::rollBack();
                $data['status'] = StatusConstants::WARNING;
                $data['msg'] = 'No se pudo actualizar los estatus, intente nuevamente';            
                return response($data);
            }
            // Notification
            $build = $this->buildObligationNotify($requestData['idObligation'], $requestData['title']);
            $notify = User::findOrFail($build['userToNotify']);
            $notify->notify(new ObligationsNotification($build['notifyData']));
        }
        DB::commit();
        $data['status'] = StatusConstants::SUCCESS;
        $data['msg'] = 'Documento guardado exitosamente';            
        return response($data);
    }

    public function testFiles() {
        // $diskName = $this->getStorageEnviroment('legals');
        // $disk = Storage::disk($diskName);
        // $test = $disk->deleteDirectory(26527);
        // dd($test, $diskName);
        $filesInDirectory = [
            "26566/14ab0ca38b57223ad8398ef67335412f.png",
            "26566/1d30e20d41d9cd2fa30885c09a8eb286.png",
            "26566/4347ac4e55e8414f97f62557f1b70fdb.png",
            "26566/55777d4a669cfad9658a2648d638b5fd.png"
        ];
        $currentFiles = [
            "26566/14ab0ca38b57223ad8398ef67335412f.png",
            "26566/55777d4a669cfad9658a2648d638b5fd.png",
            "26566/1d30e20d41d9cd2fa30885c09a8eb286.png"
        ];

        dd(array_diff($filesInDirectory, $currentFiles));
    }

    public function downloadFile(Request $request, $idFile) {
        $disk = $this->getStorageEnviroment('files');
        $data = FilesModel::with('corporate')->findOrFail($idFile);
        $explodePerPoint = explode('.', $data->url);
        $ext = end($explodePerPoint);
        $name = $data->corporate->corp_tradename.'_'.$data->title.'.'.$ext;
        $path = str_replace('storage/files/', '', $data->url);
        $nameFile = str_replace([' ', '/', '\''], '_', $name);
        return Storage::disk($disk)->download($path, $nameFile);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function deleteFile(Request $request) {
        $status = StatusConstants::ERROR;
        $msg = 'Algo salio mal, no es posible eliminar el registro';
        try {
            DB::beginTransaction();
            // delete file
            $idFile = $request->input('idFile');
            $deleteFile = deleteFile($idFile);
            if ($deleteFile) {
                DB::commit();
                $status = StatusConstants::SUCCESS;
                $msg = 'Registro eliminado';
            }
        } catch (\Throwable $th) {
            DB::rollBack();
        }
        $data['status'] = $status;
        $data['msg'] = $msg;
        return $data;
    }

    public function getAuditProcessesByClientAndPlant(Request $request, $idCorporate){
        $data = AuditRegistersModel::with('process')->where([
            ['id_corporate', $idCorporate],
            ['id_status', StatusConstants::FINISHED_AUDIT]
        ])->get()->toArray();
        return response($data);
    }
} 
