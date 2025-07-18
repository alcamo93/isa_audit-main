<?php

namespace App\Http\Controllers\V2\Catalogs;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests\SubrequirementRequest;
use App\Models\V2\Catalogs\Requirement;
use App\Models\V2\Catalogs\Subrequirement;
use App\Traits\V2\ResponseApiTrait;
use App\Classes\Forms\HandlerChangeSubrequirements;

class SubrequirementController extends Controller
{
  use ResponseApiTrait;

  /**
	 * Redirect to view.
	 */
	public function view($idForm, $idRequirement) 
	{
		$data['id_requirement'] = $idRequirement;
    $data['id_form'] = $idForm;
		return view('v2.catalogs.forms.requirement.subrequirement.main', ['data' => $data]);
	}

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index($idForm, $idRequirement)
  {
    $data = Subrequirement::included()->withIndex()->filter()->customFilters($idRequirement)->customOrderRequirement()->getOrPaginate();
    return $this->successResponse($data);
  }

  /**
	 * Store a newly created resource in storage.
	 *
	 * @param  App\Http\Requests\SubrequirementRequest $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(SubrequirementRequest $request, $idForm, $idRequirement)
	{
		try {
			DB::beginTransaction();
			$validate = $this->validateRequest('store', $idRequirement, $request->all());
			if (!$validate['success']) {
				DB::rollback();
				return $this->errorResponse($validate['messages']);
			}
			$record = Subrequirement::create($validate['request']);
			$record->update(['id_track' => $record->id_subrequirement]);
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
  public function show($idForm, $idRequirement, $idSubrequirement)
  {
    try {
      $data = Subrequirement::findOrFail($idSubrequirement);
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
      $data = Subrequirement::included()->findOrFail($id);
      return $this->successResponse($data);
    } catch(\Throwable $th) {
      throw $th;
    }
  }

  /**
	 * Update the specified resource in storage.
	 *
	 * @param  App\Http\Requests\SubrequirementRequest $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(SubrequirementRequest $request, $idForm, $idRequirement, $idSubrequirement)
	{
		try {
			DB::beginTransaction();
			$record = Subrequirement::findOrFail($idSubrequirement);
			$validate = $this->validateRequest('update', $idRequirement, $request->all());
			if (!$validate['success']) {
				DB::rollback();
				return $this->errorResponse($validate['messages']);
			}
			$handler = new HandlerChangeSubrequirements();
			$update = $handler->canModifySubrequirement($record, null, 'update');
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
	public function destroy($idForm, $idRequirement, $idSubrequirement) 
	{
		try {
			DB::beginTransaction();
			$record = Subrequirement::findOrFail($idSubrequirement);
			$validate = $this->validateRequest('destroy', $idRequirement);
			if (!$validate['success']) {
				DB::rollback();
				return $this->errorResponse($validate['messages']);
			}
			$handler = new HandlerChangeSubrequirements();
			$destroy = $handler->canModifySubrequirement($record, null, 'destroy');
			if (!$destroy['success']) {
				DB::rollback();
				return $this->errorResponse($destroy['messages']);
			}
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
	private function validateRequest($method, $idRequirement, $request = []) 
	{
		$data['success'] = true;

		$allowed = [
      'order', 'id_evidence', 'id_condition', 'document', 'id_requirement_type', 'no_subrequirement', 
      'subrequirement', 'description', 'help_subrequirement', 'acceptance', 'id_periodicity'
		];

		$fields = collect($request)->only($allowed);
		$requirement = Requirement::findOrFail($idRequirement);

		if ($method != 'destroy') {
			$fields->put('id_requirement', $requirement->id_requirement)
				->put('id_matter', $requirement->id_matter)
				->put('id_aspect', $requirement->id_aspect)
        ->put('id_application_type', $requirement->id_application_type)
        ->put('id_state', $requirement->id_state)
        ->put('id_city', $requirement->id_city);
		}

		if ($method == 'destroy') return $data;
		
		$data['request'] = $fields->toArray();
		return $data;
	}
}
