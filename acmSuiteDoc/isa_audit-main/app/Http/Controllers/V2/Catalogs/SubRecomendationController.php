<?php

namespace App\Http\Controllers\V2\Catalogs;

use App\Http\Controllers\Controller;
use App\Http\Requests\RecomendationRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\V2\Catalogs\SubrequirementRecomendation;
use App\Traits\V2\ResponseApiTrait;

class SubRecomendationController extends Controller
{
	use ResponseApiTrait;

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index($idForm, $idRequirement, $idSubrequirement)
	{
		try {
			$data = SubrequirementRecomendation::included()->filter()->customFilters($idSubrequirement)->getOrPaginate();
			return $this->successResponse($data);
		} catch(\Throwable $th) {
			throw $th;
		}
	}
	
	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  App\Http\Requests\RecomendationRequest $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(RecomendationRequest $request, $idForm, $idRequirement, $idSubrequirement)
	{
		try {
			$data = $this->validateRequest($idSubrequirement, $request->all());
			$record = SubrequirementRecomendation::create($data);
			return $this->successResponse($record, Response::HTTP_CREATED);
		} catch (\Throwable $th) {
			throw $th;
		}
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($idForm, $idRequirement, $idSubrequirement, $idRecomendation)
	{
		try {
			$data = SubrequirementRecomendation::findOrFail($idRecomendation);
			return $this->successResponse($data);
		} catch(\Throwable $th) {
			throw $th;
		}
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  App\Http\Requests\RecomendationRequest $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(RecomendationRequest $request, $idForm, $idRequirement, $idSubrequirement, $idRecomendation)
	{
		try {
			$record = SubrequirementRecomendation::findOrFail($idRecomendation);
			$record->update($request->all());
			return $this->successResponse($record);
		} catch (\Throwable $th) {
			throw $th;
		}
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($idForm, $idRequirement, $idSubrequirement, $idRecomendation) 
	{
		try {
			$record = SubrequirementRecomendation::findOrFail($idRecomendation);
			$record->delete();
			return $this->successResponse($record);
		} catch(\Throwable $th) {
			throw $th;
		}
	}

	/**
	 * Clear request fields 
	 */
	private function validateRequest($idSubrequirement, $request) 
	{
		$data = collect($request)->put('id_subrequirement', $idSubrequirement)->toArray();
		return $data;
	}

	/**
	 * validate guideline
	 */
	private function validateRelations($record) 
	{
		$data['success'] = true;

		$hasDependency = $record->audits()->exists();
		if ( $hasDependency ) {
			$data['success'] = false;
			$data['messages'] = "La recomendación no puede ser eliminado tiene registros en Auditoría relacinados";
			return $data;
		}
	
		return $data;
	}


}
