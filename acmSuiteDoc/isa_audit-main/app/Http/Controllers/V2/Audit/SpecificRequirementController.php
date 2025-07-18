<?php

namespace App\Http\Controllers\V2\Audit;

use App\Classes\Process\SpecificRequirements\SpecificRequirementEvaluate;
use App\Http\Controllers\Controller;
use App\Http\Requests\SpecificRequirementRequest;
use App\Models\V2\Admin\Corporate;
use App\Models\V2\Catalogs\Aspect;
use App\Models\V2\Catalogs\SpecificRequirement;
use App\Models\V2\Catalogs\Condition;
use App\Models\V2\Catalogs\RequirementType;
use App\Traits\V2\ResponseApiTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SpecificRequirementController extends Controller
{
  use ResponseApiTrait;
  
	/**
	 * Redirect to view.
	 */
	public function view() 
	{
		return view('v2.catalogs.forms.requirement.specific.main');
	}

  /**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		try {
			$data = SpecificRequirement::included()->withIndex()->filter()->specificFilters()->customOrderRequirement()->getOrPaginate();
			return $this->successResponse($data);
		} catch(\Throwable $th) {
			throw $th;
		}
	}
	
	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  App\Http\Requests\SpecificRequirementRequest $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(SpecificRequirementRequest $request)
	{
		try {
			DB::beginTransaction();
			$validate = $this->validateRequest('store', $request->all());
			if (!$validate['success']) {
				DB::rollback();
				return $this->errorResponse($validate['message']);
			}
			
			$record = SpecificRequirement::create($validate['request']);
			// add in audits
			$specific = new SpecificRequirementEvaluate('store', $record);
			$setInAudit = $specific->setSpecificInProgressAudit();
			if (!$setInAudit) {
				DB::rollback();
				return $this->errorResponse($setInAudit['message']);
			}
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
	public function show($id)
	{
		try {
			$data = SpecificRequirement::findOrFail($id);
			return $this->successResponse($data);
		} catch(\Throwable $th) {
			throw $th;
		}
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  App\Http\Requests\SpecificRequirementRequest $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(SpecificRequirementRequest $request, $id)
	{
		try {
			DB::beginTransaction();
			$record = SpecificRequirement::findOrFail($id);
			// validate request
			$validate = $this->validateRequest('update', $request->all(), $record);
			if (!$validate['success']) {
				DB::rollback();
				return $this->errorResponse($validate['message']);
			}
			// verify evaluates
			$specific = new SpecificRequirementEvaluate('update', $record, $validate['request']);
			$setInAudit = $specific->setSpecificInProgressAudit();
			if (!$setInAudit) {
				DB::rollback();
				return $this->errorResponse($setInAudit['message']);
			}
			// update data
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
	public function destroy($id) 
	{
		try {
			$record = SpecificRequirement::findOrFail($id);
			// verify evaluates
			$specific = new SpecificRequirementEvaluate('destroy', $record);
			$setInAudit = $specific->setSpecificInProgressAudit();
			if (!$setInAudit) {
				DB::rollback();
				return $this->errorResponse($setInAudit['message']);
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
	private function validateRequest($method, $request, $record = null) 
	{
		try {
			$aspect = Corporate::findOrFail($request['id_corporate']);
			if ( $aspect->id_customer != $request['id_customer'] ) {
				$data['success'] = false;
				$data['message'] = 'Datos errones Cliente/Planta';
				return $data;
			}

			$aspect = Aspect::findOrFail($request['id_aspect']);
			if ( $aspect->id_matter != $request['id_matter'] ) {
				$data['success'] = false;
				$data['message'] = 'Datos errones Materia/Aspecto';
				return $data;
			}

			$verifyRequest = Arr::only($request, [
				'id_customer', 'id_corporate', 'id_matter', 'id_aspect', 'order', 
				'id_application_type', 'no_requirement', 'requirement', 'description'
			]);

			$verifyRequest = Arr::add($verifyRequest, 'id_requirement_type', RequirementType::REQUIREMENT_SPECIFIC);
			$verifyRequest = Arr::add($verifyRequest, 'id_condition', Condition::CRITICAL);

			$data['success'] = true;
			$data['request'] = $verifyRequest;
			return $data;
		} catch (\Throwable $th) {
			$data['success'] = false;
			$data['message'] = 'Algo salio mal verificar los datos';
			return $data;
		}
	}
}
