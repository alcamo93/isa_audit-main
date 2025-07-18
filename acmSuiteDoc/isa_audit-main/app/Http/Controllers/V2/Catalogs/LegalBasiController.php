<?php

namespace App\Http\Controllers\V2\Catalogs;

use App\Classes\Files\ImageDecodeToFile;
use App\Http\Controllers\Controller;
use App\Http\Requests\LegalBasiRequest;
use App\Models\V2\Catalogs\Guideline;
use App\Models\V2\Catalogs\LegalBasi;
use App\Traits\V2\ResponseApiTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class LegalBasiController extends Controller
{
	use ResponseApiTrait;

	/**
	 * Redirect to view.
	 */
	public function view($idGuideline) 
	{
		$data = ['id_guideline' => $idGuideline];
		return view('v2.catalogs.guideline.legal.main', ['data' => $data]);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index($idGuideline)
	{
		try {
			$data = LegalBasi::withIndex()->included()->filter()->customFilters($idGuideline)->customOrder()->getOrPaginate();
			return $this->successResponse($data);
		} catch(\Throwable $th) {
			throw $th;
		}
	}
	
	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  App\Http\Requests\LegalBasiRequest $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(LegalBasiRequest $request, $idGuideline)
	{
		try {
			DB::beginTransaction();
			$data = $this->validateRequest($idGuideline, $request->all());
			$record = LegalBasi::create($data);
			// store disk: define in config/filesystems.php : disks []
			$storeDisk = 'legals';
			$decodeImgObject = new ImageDecodeToFile($storeDisk, $record->id_legal_basis);
			$decodeImgObject->decodeImg64ToLink($data['legal_quote']);
			if (!$decodeImgObject->allCreate) {
				DB::rollback();
				return $this->errorResponse('No se ha podido crear los archivos, verifica que las imagenes no esten dañadas');
			}
			// update only legal quote
			$record->update(['legal_quote' => $decodeImgObject->richText]);
			$response = ['id_legal_basis' => $record->id_legal_basis, 'legal_basis' => $record->legal_basis];
			DB::commit();
			return $this->successResponse($response, Response::HTTP_CREATED);
		} catch (\Throwable $th) {
			DB::rollback();
			throw $th;
		}
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($idGuideline, $id)
	{
		try {
			$data = LegalBasi::withIndex()->findOrFail($id);
			return $this->successResponse($data);
		} catch(\Throwable $th) {
			throw $th;
		}
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  App\Http\Requests\LegalBasiRequest $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(LegalBasiRequest $request, $idGuideline, $id)
	{
		try {
			DB::beginTransaction();
			$record = LegalBasi::findOrFail($id);
			$validate = $this->validateRelations('update', $idGuideline, $record);
			if (!$validate['success']) {
				return $this->errorResponse($validate['messages']);
			}
			$record->update($request->all());
			// store disk: define in config/filesystems.php : disks []
			$storeDisk = 'legals';
			$decodeImgObject = new ImageDecodeToFile($storeDisk, $record->id_legal_basis);
			$decodeImgObject->decodeImg64ToLink($record->legal_quote);
			if (!$decodeImgObject->allCreate) {
				DB::rollback();
				return $this->errorResponse('No se ha podido crear los archivos, verifica que las imagenes no esten dañadas');
			}
			// update only legal quote
			$record->update(['legal_quote' => $decodeImgObject->richText]);
			$response = ['id_legal_basis' => $record->id_legal_basis, 'legal_basis' => $record->legal_basis];
			DB::commit();
			return $this->successResponse($response);
		} catch (\Throwable $th) {
			DB::rollback();
			throw $th;
		}
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($idGuideline, $id) 
	{
		try {
			$record = LegalBasi::findOrFail($id);
			$validate = $this->validateRelations('destroy', $idGuideline, $record);
			if (!$validate['success']) {
				return $this->errorResponse($validate['messages']);
			}
			$record->delete();
			return $this->successResponse($record);
		} catch(\Throwable $th) {
			throw $th;
		}
	}

	/**
	 * Clear request fields 
	 */
	private function validateRequest($idGuideline, $request) 
	{
		$guideline = Guideline::findOrFail($idGuideline);
		$data = collect($request)->put('id_application_type', $guideline->id_application_type)
			->put('id_guideline', $guideline->id_guideline)->toArray();
		return $data;
	}

	/**
	 * validate guideline
	 */
	private function validateRelations($method, $idGuideline, $record) 
	{
		$data['success'] = true;
		$guideline = Guideline::findOrFail($idGuideline);

		$sameGuideline = $guideline->id_guideline == $record->id_guideline;
		if (!$sameGuideline) {
			$data['success'] = false;
			$data['messages'] = 'El artículo no pertenece al marco jurídico que especifica';
			return $data;
		}

		if ($method == 'destroy') {
			$hasQuestions = $record->questions()->count();
			$hasRequirements = $record->requirements()->count();
			$hasSubrequirements = $record->subrequirements()->count();
			$types = collect([
				[ 'name' => 'Preguntas', 'has' => boolval($hasQuestions) ],
				[ 'name' => 'Requerimientos', 'has' => boolval($hasRequirements) ],
				[ 'name' => 'Subrequerimientos', 'has' => boolval($hasSubrequirements) ],
			]);

			$groups = $types->where('has', true);
			if ( $groups->isNotEmpty() ) {
				$string = $groups->pluck('name')->join(', ');
				$data['success'] = false;
				$data['messages'] = "El artículo no puede ser eliminado tiene registros de {$string} relacionados";
				return $data;
			}
		}
		return $data;
	}


}
