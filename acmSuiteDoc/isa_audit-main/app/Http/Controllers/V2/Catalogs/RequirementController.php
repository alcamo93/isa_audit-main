<?php

namespace App\Http\Controllers\V2\Catalogs;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests\RequirementRequest;
use App\Models\V2\Catalogs\Form;
use App\Models\V2\Catalogs\Requirement;
use App\Models\V2\Catalogs\ApplicationType;
use App\Models\V2\Catalogs\RequirementType;
use App\Traits\V2\ResponseApiTrait;
use App\Classes\Forms\HandlerChangeRequirements;

class RequirementController extends Controller
{
  use ResponseApiTrait;
  
	/**
	 * Redirect to view.
	 */
	public function view($idForm) 
	{
		$data = ['id_form' => $idForm];
		return view('v2.catalogs.forms.requirement.main', ['data' => $data]);
	}

  /**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index($idForm)
	{
		try {
			$data = Requirement::included()->withIndex()->filter()->customFilters($idForm)->customOrderRequirement()->getOrPaginate();
			return $this->successResponse($data);
		} catch(\Throwable $th) {
			throw $th;
		}
	}
	
	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  App\Http\Requests\RequirementRequest $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(RequirementRequest $request, $idForm)
	{
		try {
			DB::beginTransaction();
			$validate = $this->validateRequest('store', $idForm, $request->all());
			if (!$validate['success']) {
				DB::rollback();
				return $this->errorResponse($validate['messages']);
			}
			$record = Requirement::create($validate['request']);
			$record->update(['id_track' => $record->id_requirement]);
			DB::commit();
			return $this->successResponse($record, Response::HTTP_CREATED);
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
	public function show($idForm, $id)
	{
		try {
			$data = Requirement::withIndex()->findOrFail($id);
			return $this->successResponse($data);
		} catch(\Throwable $th) {
			throw $th;
		}
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function single($id)
	{
		try {
			$data = Requirement::included()->findOrFail($id);
			return $this->successResponse($data);
		} catch(\Throwable $th) {
			throw $th;
		}
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  App\Http\Requests\RequirementRequest $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(RequirementRequest $request, $idForm, $id)
	{
		try {
			DB::beginTransaction();
			$record = Requirement::findOrFail($id);
			$validate = $this->validateRequest('update', $idForm, $request->all());
			if (!$validate['success']) {
				DB::rollback();
				return $this->errorResponse($validate['messages']);
			}
			$record->update($validate['request']);
			DB::commit();
			return $this->successResponse($record);
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
	public function destroy($idForm, $id) 
	{
		try {
			DB::beginTransaction();
			$record = Requirement::findOrFail($id);
			$validate = $this->validateRequest('destroy', $idForm);
			if (!$validate['success']) {
				DB::rollback();
				return $this->errorResponse($validate['messages']);
			}
			$handler = new HandlerChangeRequirements();
			$destroy = $handler->canModifyRequirement($record, null, 'destroy');
			if (!$destroy['success']) {
				DB::rollback();
				return $this->errorResponse($destroy['messages']);
			}
			$record->delete();
			DB::commit();
			return $this->successResponse($record);
		} catch(\Throwable $th) {
			DB::rollback();
			throw $th;
		}
	}

	/**
	 * Clear request fields 
	 */
	private function validateRequest($method, $idForm, $request = [], $record = null) 
	{
		$data['success'] = true;

		$allowed = [
			'order', 'id_evidence', 'id_condition', 'document', 'id_requirement_type', 'id_application_type',
			'id_state', 'id_city', 'no_requirement', 'requirement', 'description', 'help_requirement', 'acceptance', 'id_periodicity'
		];

		if ($method != 'destroy') {
			$fields = collect($request)->only($allowed);
			$evaluateRequirementType = $fields['id_requirement_type'] == RequirementType::REQUIREMENT_COMPOSE;
			$evaluateApplicationType = $fields['id_application_type'] == ApplicationType::FEDERAL;
			$typeNoAllowed = $evaluateRequirementType && $evaluateApplicationType;

			if ( $typeNoAllowed ) {
				$data['success'] = false;
				$data['messages'] = 'Los Requrimientos Compuesto de tipo Federeal son una excepeción en el registro de esta plataforma';
				return $data;
			}
		}

		$form = Form::findOrFail($idForm);

		if ($method != 'destroy') {	
			$requirementCompose = $fields['id_requirement_type'] == RequirementType::REQUIREMENT_COMPOSE;
			$identificalCompose = $fields['id_requirement_type'] == RequirementType::REQUIREMENT_IDENTIFICATION_COMPOSE;
			$hasSubrequirements = $requirementCompose || $identificalCompose ? 1 : 0;
			$fields->put('has_subrequirement', $hasSubrequirements);
		}

		if ($method == 'store') {
			$fields->put('form_id', $form->id)
				->put('id_matter', $form->matter_id)
				->put('id_aspect', $form->aspect_id);
		}
		
		if ($method == 'destroy') return $data; 

		$applicationType = $request['id_application_type'];
		if ($applicationType == ApplicationType::FEDERAL) {
			$fields->put('id_state', null)->put('id_city', null);
		}
		if ($applicationType == ApplicationType::STATE) {
			$fields->put('id_city', null);
		}
		
		$data['request'] = $fields->toArray();
		return $data;
	}
}
