<?php

namespace App\Http\Controllers\Catalogues;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use App\Models\Catalogues\GuidelinesModel;
use App\Models\Catalogues\BasisModel;
use App\Models\Catalogues\StatesModel;
use App\Models\Catalogues\CitiesModel;
use App\Models\Catalogues\CountriesModel;
use App\Models\Catalogues\ApplicationTypesModel;
use App\Models\Catalogues\LegalClassificationModel;
use App\Classes\StatusConstants;
use App\Classes\ImageDecodeToFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Jobs\DecodeImageBase64;
use App\Traits\FileTrait;

class LegalsController extends Controller
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
     * Main view in Basis Guidelines catalogue module
     */
    public function index(){
        $countries = CountriesModel::getCountries();
        $states = StatesModel::getStates(1);
        $cities = CitiesModel::getCities(1);
        $appTypes = ApplicationTypesModel::getApplicationTypes([1]);
        $legalC = LegalClassificationModel::GetLegalClassifications();
        return view('catalogs.legals.legal_founds', [
            'countries' => $countries, 
            'cities' => $cities,
            'states' => $states,
            'appTypes' => $appTypes,
            'legalC' => $legalC
        ]); 
    }
    /**
     * Obtain Guidelines for data table
     */
    public function getGuidelinesDT(Request $request){
        $page = $request->input('start');
        $rows = $request->input('length');
        $search = $request->input('search');
        $draw = $request->input('draw');
        $order = $request->input('order');
        $filterName = $request->input('filterName');
        $applicationType = $request->input('filter_application_type');
        $legalClassification = $request->input('filter_legal_classification');
        $state = $request->input('filter_state');
        $city = $request->input('filter_city');
        $filterInitials = $request->input('filterInitials');
        $data = GuidelinesModel::GetGuidelinesDT( $page,  $rows,  $search,  $draw, $order, $filterName, $applicationType, $legalClassification, $state, $city, $filterInitials);
        return response($data);
    }
    /**
     * Obtain Guidelines for select
     */
    public function getGuidelinesSelection(Request $request){
        $page = $request->input('start');
        $applicationType = $request->input('filter_application_type');
        $legalClassification = $request->input('filter_legal_classification');
        $state = $request->input('filter_state');
        $data = GuidelinesModel::getGuidelinesSelection( $applicationType, $legalClassification, $state);
        return response($data);
    }
    /**
     * Save guide line in DB
     */
    public function setGuideline(Request $request) {

        $guideline = $request->input('guideline_s');
        $initialsGuideline = $request->input('initials_guideline_s');
        $applicationType = $request->input('application_type_s');
        $legalClassification = $request->input('legal_classification_s');
        $state = $request->input('state_s');
        $city = $request->input('city_s');
        $lastDate = $request->input('lastDate');

        $setGuideline = GuidelinesModel::SetGuideline(
            $guideline,
            $initialsGuideline,
            $applicationType,
            $legalClassification,
            $state,
            $city,
            $lastDate
        );
        switch ($setGuideline) {
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
     * Update guideline
     */
    public function updateGuideline(Request $request) {
        $idguideline = $request->input('idguideline');
        $guideline = $request->input('guideline'); 
        $initialsGuideline = $request->input('initials_guideline'); 
        $applicationType = $request->input('application_type'); 
        $legalClassification = $request->input('legal_classification'); 
        $state = $request->input('state');
        $city = $request->input('city');
        $lastDate = $request->input('lastDate');
        $update = GuidelinesModel::UpdateGuideline(
            $idguideline,
            $guideline,
            $initialsGuideline,
            $applicationType,
            $legalClassification,
            $state,
            $city,
            $lastDate           
        );
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
                $msg = 'El registro no se ha encontrado';
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
     * Delete guideline
     */
    public function deleteGuideline(Request $request) {
        $idGuideline = $request->input('idGuideline');
        $delete = GuidelinesModel::deleteGuideline($idGuideline);
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
     /**
     * Obtain Basis for data table
     */
    public function getBasisDT(Request $request){
        $dataRequest = $request->all();
        $data = BasisModel::GetBasisDT($dataRequest);   
        return response($data);
    }
    /**
     * Get data basis
     */
    public function getBasis(Request $request) {
        $idBasis = $request->input('idBasis');
        $data = BasisModel::where('id_legal_basis', $idBasis)->first()->toArray();
        $data['legal_quote'] = $this->setUrlInRichText($data['legal_quote']);
        return response($data);
    }
    /**
     * Save basis in DB
     */
    public function setBasis(Request $request) {
        $dataRequest = $request->all();
        DB::beginTransaction();
        $set = BasisModel::SetBasis($dataRequest);
        if ($set['status'] != StatusConstants::SUCCESS) {
            DB::rollBack();
            $data['status'] = StatusConstants::ERROR;
            $data['msg'] = 'Algo salio mal, intente nuevamente';
            return response($data);
        }
        // store disk: define in config/filesystems.php : disks []
        $storeDisk = 'legals';
        $decodeImgObject = new ImageDecodeToFile($storeDisk, $set['idBasis']);
        $decodeImg = $decodeImgObject->decodeImg64ToLink($dataRequest['quote']);
        if (!$decodeImgObject->allCreate) {
            DB::rollBack();
            $data['status'] = StatusConstants::ERROR;
            $data['msg'] = 'No se ha podido crear los archivos, verifica que las imagenes no esten corrompidas o dañadas';
            return response($data);
        }
        $updateQuote = BasisModel::UpdateQuote($set['idBasis'], $decodeImgObject->richText);
        if ($updateQuote['status'] != StatusConstants::SUCCESS) {
            // Delete files with use
            $directory = $set['idBasis'];
            Storage::disk($decodeImgObject->diskStore)->deleteDirectory($directory);
            DB::rollBack();
            $data['status'] = StatusConstants::ERROR;
            $data['msg'] = 'Algo salio mal, al guardar la cita legal';
            return response($data);
        }
        DB::commit();
        $data['status'] = StatusConstants::SUCCESS;
        $data['msg'] = 'Datos registrados';
        return response($data);
    }
    /**
     * Update basis
     */
    public function updateBasis(Request $request) {
        $dataRequest = $request->all();
        // store disk: define in App\Traits\FileTrait
        $storeDisk = 'legals';
        $decodeImgObject = new ImageDecodeToFile($storeDisk, $dataRequest['idBasis']);
        $decodeImg = $decodeImgObject->decodeImg64ToLink($dataRequest['quote']);
        if (!$decodeImgObject->allCreate) {
            $data['status'] = StatusConstants::ERROR;
            $data['msg'] = 'No se ha podido crear los archivos, verifica que las imagenes no esten corrompidas o dañadas';
            return response($data);
        }
        DB::beginTransaction();
        $update = BasisModel::UpdateBasis($dataRequest, $decodeImgObject->richText);
        if ($update['status'] != StatusConstants::SUCCESS) {
            DB::rollBack();
            $data['status'] = StatusConstants::ERROR;
            $data['msg'] = 'Algo salio mal, intente nuevamente';
            return response($data);
        }
        // Delete files with use
        $documentsToKeep = $decodeImgObject->linksExistImg;
        $documentsCreated = $decodeImgObject->createdImg;
        $currentFiles = array_merge($documentsToKeep, $documentsCreated);
        $filesInDirectory = Storage::disk($decodeImgObject->diskStore)->allFiles($dataRequest['idBasis']);
        if ( sizeof($currentFiles) > 0 ) {
            $filesToDelete = array_diff($filesInDirectory, $currentFiles);
            Storage::disk($decodeImgObject->diskStore)->delete($filesToDelete);
        }
        // response
        DB::commit();
        $data['status'] = StatusConstants::SUCCESS;
        $data['msg'] = 'Registro actualizado';
        return response($data);
    }
    /**
     * Delete basis
     */
    public function deleteBasis(Request $request) {
        $idBasis = $request->input('idBasis');
        DB::beginTransaction();
        $delete = BasisModel::deleteBasis($idBasis);
        if ($delete['status'] != StatusConstants::SUCCESS) {
            DB::rollBack();
            $data['status'] = $delete['status'];
            $evaluateStatus = $delete['status'] == StatusConstants::ERROR;
            $msg = ($evaluateStatus) ? 'Algo salio mal, intente nuevamente' : 'El registro está en uso, no se puede eliminar';
            $data['msg'] = $msg;
            return response($data);
        }
        // Delete files without use
        $storeDisk = $this->getStorageEnviroment('legals');
        Storage::disk($storeDisk)->deleteDirectory($idBasis);
        DB::commit();
        $data['status'] = StatusConstants::SUCCESS;
        $data['msg'] = 'Registro eliminado';
        return response($data);
    }

    public function images(){
        ini_set('memory_limit', '2048M');
        set_time_limit(3600);
        $legals = BasisModel::select('t_legal_basises.id_legal_basis', 't_legal_basises.legal_quote')
            ->where('t_legal_basises.legal_quote', 'LIKE', '%data:image%')
            ->get()->toArray();
        foreach ($legals as $key => $value) {
            DecodeImageBase64::dispatch($value['id_legal_basis'], $value['legal_quote']);
        }
    }
} 