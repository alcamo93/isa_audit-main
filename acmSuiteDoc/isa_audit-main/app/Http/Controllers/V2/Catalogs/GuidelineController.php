<?php

namespace App\Http\Controllers\V2\Catalogs;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\V2\Catalogs\Guideline;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests\GuidelineRequest;
use App\Models\V2\Catalogs\ApplicationType;
use App\Traits\V2\ResponseApiTrait;
use App\Classes\Forms\HandlerChangeGuideline;
use App\Models\V2\Catalogs\Aspect;
use App\Models\V2\Catalogs\Topic;

class GuidelineController extends Controller
{
	use ResponseApiTrait;

	/**
	 * Redirect to view.
	 */
	public function view() 
	{
		return view('v2.catalogs.guideline.main');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function source()
	{
		try {
			$data = Guideline::withIndex()->included()->filter()->getOrPaginate();
			return $this->successResponse($data);
		} catch(\Throwable $th) {
			throw $th;
		}
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		try {
			$data = Guideline::included()->withIndex()->filter()->getOrPaginate();
			return $this->successResponse($data);
		} catch(\Throwable $th) {
			throw $th;
		}
	}
	
	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  App\Http\Requests\GuidelineRequest $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(GuidelineRequest $request)
	{
		try {
			DB::beginTransaction();
			$data = $this->clearRequest($request->all());
			$record = Guideline::create($data);
			$record->guidelines()->sync($data['guidelines_ext'] ?? []); 
			DB::commit();
			return $this->successResponse($record, Response::HTTP_CREATED);
		} catch(\Throwable $th) {
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
			$data = Guideline::withIndex()->findOrFail($id);
			return $this->successResponse($data);
		} catch(\Throwable $th) {
			throw $th;
		}
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  App\Http\Requests\GuidelineRequest $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(GuidelineRequest $request, $id)
	{
		try {
			DB::beginTransaction();
			$data = $this->clearRequest($request->all());
			$record = Guideline::findOrFail($id);
			$oldData = $record->toArray();
			$record->update($data);
			$record->guidelines()->sync($data['guidelines_ext'] ?? []); 
			$handlerGuideline = new HandlerChangeGuideline();
			$validation = $handlerGuideline->canModifyGuideline($record, $oldData, 'update');
			if ( !$validation['success'] ) {
				DB::rollback();
				return $this->errorResponse($validation['messages']);	
			}
			DB::commit();
			return $this->successResponse($record);
			DB::rollback();
		} catch(\Throwable $th) {
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
			DB::beginTransaction();
			// todo: validation si esta relacionada un hijo a pergunta o requerimiento
			$record = Guideline::findOrFail($id);
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
	private function clearRequest($request) 
	{
		$exceptFields = [];
		$applicationType = $request['id_application_type'];
		if ($applicationType == ApplicationType::FEDERAL) $exceptFields = ['id_state', 'id_city'];
		if ($applicationType == ApplicationType::STATE) $exceptFields = ['id_city'];
		$data = collect($request)->except($exceptFields)->toArray();
		return $data;
	}

	/**
	 * Get topics for relations
	 */
	public function topics($idGuideline) {
		try {
			$data = Topic::filter()->getRelationsForGuideline($idGuideline)->getOrPaginate();
		  return $this->successResponse($data);
		} catch (\Throwable $th) {
		  throw $th;
		}
	}

	/**
	 * Set topics for relations
	 */
	public function relation($idGuideline, $idTopic) {
		try {
		  $guideline = Guideline::findOrFail($idGuideline);
		  $guideline->topics()->toggle([$idTopic]);
		  return $this->successResponse($guideline);
		} catch (\Throwable $th) {
		  throw $th;
		}
	}

	/**
	 * Get aspects for relations
	 */
	public function aspects($idGuideline) {
		try {
		  $data = Aspect::filter()->getRelationsForGuideline($idGuideline)->getOrPaginate();
		  return $this->successResponse($data);
		} catch (\Throwable $th) {
		  throw $th;
		}
	}

	/**
	 * Set aspects for relations
	 */
	public function relationAspects($idGuideline, $idAspect, $idMatter) {
		try {
		  $guideline = Guideline::findOrFail($idGuideline);

		  $aspectsToDetach = $guideline->aspects()
            ->where('id_matter', '!=', $idMatter)
            ->pluck('c_aspects.id_aspect');

  		  if ($aspectsToDetach->isNotEmpty()) {
			$guideline->aspects()->detach($aspectsToDetach);
		  }

		  $guideline->aspects()->toggle([$idAspect]);
		  return $this->successResponse($guideline);
		} catch (\Throwable $th) {
		  throw $th;
		}
	}
}
